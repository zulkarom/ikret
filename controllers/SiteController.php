<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\PasswordResetRequestForm;
use app\models\QuestionnaireAnswer;
use app\models\RegisterForm;
use app\models\ResetPasswordForm;
use app\models\Session;
use app\models\SessionAttendance;
use InvalidArgumentException;
use yii\db\Expression;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            if(Yii::$app->user->identity->isParticipant){
                $check = QuestionnaireAnswer::findOne(['user_id' => Yii::$app->user->identity->id]);
                if(!$check){
                    Yii::$app->session->addFlash('info', "You need to answer <a href='".Url::to(['program/prequestion'])."'>pre-event questionnaire</a> before registering to any program below.");
                }
            }
        }
        
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin($t=null)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::$app->session->addFlash('success', "You has been logged in to I-CREATE system");
            if($t){
                return $this->redirect(['site/qr', 't' => $t]);
            }else{
                return $this->redirect(['site/index']);
            }
            
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
            'attendanceToken' => $t
        ]);
    }

    public function actionForgotPassword()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                
                return $this->redirect(['/site/login']);
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, currently we are unable to reset your password. Please contact system administration for passsword reset.');
            }
        }
        
        return $this->render('forgot_password', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Your new password has been successfully created.');
            
            return $this->redirect(['/site/login']);
        }
        
        return $this->render('reset_password', [
            'model' => $model,
        ]);
    }

    public function actionRegister(){
        
        $model = new RegisterForm();
        
        if ($model->load(Yii::$app->request->post())){
       
            if ($model->signup()) {
                Yii::$app->session->addFlash('success', "Registration successful. You are now logged in to the system. You may proceed to register to events organised in this programme");
                //login terus
                
                return $this->goHome();
            }else{
                Yii::$app->session->addFlash('error', "Sorry, Registration is not successful.");
            }

        }
        
        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        
        Yii::$app->user->logout();
        Yii::$app->session->addFlash('info', "You has been logged out.");

        return $this->redirect(['login']);
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionQr($t=null)
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->addFlash('error', "You need to login here to proceed with attendance record. Kindly register if you haven't already");
            return $this->redirect(['login', 't' => $t]);
        }else{
            
            //record je la
            $session = Session::findOne(['token' => $t]);
            $start = strtotime($session->datetime_start);
            $end = strtotime($session->datetime_end);
            $valid = time() >= $start && time() <= $end;

            if($session){
                if($valid){
                    $att = new SessionAttendance();
                    $att->user_id = Yii::$app->user->identity->id;
                    $att->session_id = $session->id;
                    $att->scanned_at = new Expression("NOW()");
                    if($att->save()){
                        Yii::$app->session->addFlash('success', "Your attendance has been recorded.");
                        return $this->redirect(['/session/participant']);
                    }else{
                        Yii::$app->session->addFlash('error', "Error in recording attendance.");
                    }
                }else{
                    Yii::$app->session->addFlash('error', "Invalid time session");
                }
                
            }else{
                Yii::$app->session->addFlash('error', "Not a valid session");
            }
        }
        
        
    }
}
