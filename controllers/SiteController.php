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
use app\models\JuryApplication;
use app\models\JuryApplyForm;
use app\models\JuryAssign;
use app\models\JuryRequirement;
use app\models\AppSetting;
use app\models\DefaultPasswordForm;
use app\models\Program;
use app\models\ProgramRegistration;
use app\models\ProgramSub;
use app\models\RubricJudgingSession;
use app\models\RegisterForm;
use app\models\ResetPasswordForm;
use app\models\Session;
use app\models\SessionAttendance;
use app\models\UserRole;
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
        $curr = Session::find()
        ->where(['<', 'datetime_start', new Expression('NOW()')])
        ->andWhere(['>', 'datetime_end', new Expression('NOW()')])
        ->limit(5)
        ->all();

        $next = null;
        $kira_curr = 0;
        if($curr){
            $kira_curr = count($curr);
        }
        
        if($kira_curr < 4){
            $next = Session::find()
            ->where(['>', 'datetime_start', new Expression('NOW()')])
            ->orderBy(['datetime_start' => SORT_ASC, 'id' => SORT_ASC])
            ->limit(4)
            ->all();
        }

        $previous = null;
        $kira_session  = 0;
        if($next){
            $kira_session = count($next) + $kira_curr;
        }else{
            $kira_session =  $kira_curr;
        }
        
        if($kira_session < 2){
            $previous = Session::find()
            ->where(['<', 'datetime_end', new Expression('NOW()')])
            ->limit(3)
            ->orderBy('datetime_end DESC')
            ->all();
        }
        
        return $this->render('index', [
            'next' => $next,
            'previous' => $previous,
            'current' => $curr
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin($t=null)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['site/dashboard']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
            if(trim((string)$model->username) !== '' && !$model->getUser()){
                Yii::$app->session->set('registerPrefillFromLogin', [
                    'username' => trim((string)$model->username),
                    'password' => (string)$model->password,
                ]);
                Yii::$app->session->addFlash('warning', 'Username not found. Please fill in your full name and email to register.');
                return $this->redirect(['site/register']);
            }

            if($model->login()){
                if($t){
                    $returnUrl = Url::to(['site/qr', 't' => $t]);
                }else{
                    $returnUrl = Url::to(['site/dashboard']);
                }

                $user = $model->getUser();
                if($user && $model->password === $user->username){
                    Yii::$app->session->addFlash('warning', "You are using the default password. Please change your password.");
                    return $this->redirect(['site/change-default-password', 'returnUrl' => $returnUrl]);
                }

                if($t){
                    return $this->redirect(['site/qr', 't' => $t]);
                }else{
                    Yii::$app->session->addFlash('success', "You has been logged in to I-CREATE system");
                    return $this->redirect(['site/dashboard']);
                }
            }
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
            'attendanceToken' => $t
        ]);
    }

    public function actionDashboard()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $user = Yii::$app->user->identity;
        $roles = UserRole::find()
            ->where(['user_id' => $user->id, 'status' => 10])
            ->orderBy(['role_name' => SORT_ASC])
            ->all();

        $participantStats = [
            'registrations' => (int)ProgramRegistration::find()->where(['user_id' => $user->id])->count(),
            'complete' => (int)ProgramRegistration::find()->where(['user_id' => $user->id, 'status' => ProgramRegistration::STATUS_COMPLETE])->count(),
        ];

        $juryStats = null;
        $juryAssignments = [];
        if ($user->isJury) {
            $juryQuery = JuryAssign::find()->where(['user_id' => $user->id]);
            $juryStats = [
                'total' => (int)(clone $juryQuery)->count(),
                'assigned' => (int)(clone $juryQuery)->andWhere(['status' => 0])->count(),
                'judging' => (int)(clone $juryQuery)->andWhere(['status' => 10])->count(),
                'complete' => (int)(clone $juryQuery)->andWhere(['status' => 20])->count(),
            ];

            $juryAssignments = JuryAssign::find()
                ->where(['user_id' => $user->id])
                ->with(['registration.program', 'registration.programSub', 'rubric', 'judgingSession'])
                ->orderBy(['status' => SORT_ASC, 'date_start' => SORT_ASC, 'id' => SORT_DESC])
                ->limit(6)
                ->all();
        }

        return $this->render('dashboard', [
            'user' => $user,
            'roles' => $roles,
            'participantStats' => $participantStats,
            'juryStats' => $juryStats,
            'juryAssignments' => $juryAssignments,
        ]);
    }

    public function actionChangeDefaultPassword($returnUrl = null)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $returnUrl = $this->normalizeReturnUrl($returnUrl);

        try {
            $model = new DefaultPasswordForm(Yii::$app->user->id);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if (Yii::$app->request->isPost && Yii::$app->request->post('skip')) {
            Yii::$app->session->addFlash('info', 'You can change your password later from your account page.');
            return $this->redirect($returnUrl);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', 'Password updated successfully.');
            return $this->redirect($returnUrl);
        }

        return $this->render('change-default-password', [
            'model' => $model,
            'returnUrl' => $returnUrl,
        ]);
    }

    private function normalizeReturnUrl($returnUrl)
    {
        if (!$returnUrl || strpos($returnUrl, '//') === 0 || preg_match('/^[a-z][a-z0-9+.-]*:/i', $returnUrl)) {
            return Url::to(['site/index']);
        }

        return $returnUrl;
    }

    public function actionJuryApply()
    {
        if(!AppSetting::getBool('call_for_juries_enabled', true)){
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }

        $model = new JuryApplyForm();

        $requirements = JuryRequirement::find()
            ->where(['is_required' => 1, 'is_active' => 1])
            ->orderBy(['program_id' => SORT_ASC, 'program_sub_id' => SORT_ASC])
            ->all();

        $programIdAllow = [];
        $programSubIdAllow = [];
        if($requirements){
            foreach($requirements as $r){
                $programIdAllow[(int)$r->program_id] = true;
                if($r->program_sub_id){
                    $programSubIdAllow[(int)$r->program_sub_id] = true;
                }
            }
        }

        $programsById = [];
        if($programIdAllow){
            $programs = Program::find()->where(['id' => array_keys($programIdAllow)])->orderBy(['program_name' => SORT_ASC])->all();
            if($programs){
                foreach($programs as $p){
                    $programsById[(int)$p->id] = $p;
                }
            }
        }

        $subsById = [];
        if($programSubIdAllow){
            $subs = ProgramSub::find()->where(['id' => array_keys($programSubIdAllow)])->orderBy(['sub_name' => SORT_ASC])->all();
            if($subs){
                foreach($subs as $sp){
                    $subsById[(int)$sp->id] = $sp;
                }
            }
        }

        $sessionsAll = RubricJudgingSession::find()->orderBy(['session_name' => SORT_ASC])->all();
        $sessionsById = [];
        if($sessionsAll){
            foreach($sessionsAll as $s){
                $sessionsById[(int)$s->id] = $s;
            }
        }

        $counts = JuryApplication::find()
            ->select(['program_id', 'program_sub_id', 'judging_session_id', 'COUNT(*) AS c'])
            ->groupBy(['program_id', 'program_sub_id', 'judging_session_id'])
            ->asArray()
            ->all();

        $countMap = [];
        if($counts){
            foreach($counts as $row){
                $pid = (int)$row['program_id'];
                $subKey = $row['program_sub_id'] !== null ? (string)(int)$row['program_sub_id'] : 'null';
                $sessionKey = $row['judging_session_id'] !== null ? (string)(int)$row['judging_session_id'] : 'null';
                $countMap[$pid . ':' . $subKey . ':' . $sessionKey] = (int)$row['c'];
            }
        }

        $juryMatrix = [];
        if($requirements){
            foreach($requirements as $r){
                $pid = (int)$r->program_id;
                $subId = $r->program_sub_id ? (int)$r->program_sub_id : null;
                $sid = $r->judging_session_id ? (int)$r->judging_session_id : null;

                $pname = isset($programsById[$pid]) ? $programsById[$pid]->program_name : ('Program #' . $pid);
                if($subId && isset($subsById[$subId])){
                    $rowLabel = $pname . ' / ' . $subsById[$subId]->sub_name;
                }else{
                    $rowLabel = $pname;
                }

                $rowKey = $pid . ':' . ($subId ? (string)$subId : 'null');
                if(!isset($juryMatrix[$rowKey])){
                    $juryMatrix[$rowKey] = [
                        'program_id' => $pid,
                        'program_sub_id' => $subId,
                        'label' => $rowLabel,
                        'sessions' => [],
                    ];
                }

                if($sid && isset($sessionsById[$sid])){
                    $session = $sessionsById[$sid];

                    $modeList = RubricJudgingSession::listMode();
                    $modeLabel = isset($modeList[(int)$session->mode]) ? $modeList[(int)$session->mode] : null;

                    $start = $session->datetime_start ? new \DateTime($session->datetime_start) : null;
                    $end = $session->datetime_end ? new \DateTime($session->datetime_end) : null;

                    $range = '';
                    if($start && $end){
                        if($start->format('Y-m-d') === $end->format('Y-m-d')){
                            $range = $start->format('d M Y') . ' ' . $start->format('H:i') . ' - ' . $end->format('H:i');
                        }else{
                            $range = $start->format('d M Y H:i') . ' - ' . $end->format('d M Y H:i');
                        }
                    }elseif($start){
                        $range = $start->format('d M Y H:i');
                    }

                    $parts = [];
                    if($range !== ''){
                        $parts[] = $range;
                    }
                    if($session->location){
                        $parts[] = $session->location;
                    }
                    if($modeLabel){
                        $parts[] = $modeLabel;
                    }

                    if($parts){
                        $sessionName = $session->session_name . ' (' . implode(' / ', $parts) . ')';
                    }else{
                        $sessionName = $session->session_name;
                    }
                }elseif($sid){
                    $sessionName = 'Session #' . $sid;
                }else{
                    $sessionName = 'N/A';
                }

                $countKey = $pid . ':' . ($subId ? (string)$subId : 'null') . ':' . ($sid ? (string)$sid : 'null');
                $used = $countMap[$countKey] ?? 0;
                $limit = $r->jury_limit;
                $disabled = ($limit !== null && $used >= (int)$limit);

                $juryMatrix[$rowKey]['sessions'][] = [
                    'requirement_id' => (int)$r->id,
                    'judging_session_id' => $sid,
                    'session_name' => $sessionName,
                    'used' => (int)$used,
                    'limit' => $limit === null ? null : (int)$limit,
                    'disabled' => $disabled,
                ];
            }
        }

        if($juryMatrix){
            foreach($juryMatrix as &$row){
                usort($row['sessions'], function($a, $b){
                    return strcmp((string)$a['session_name'], (string)$b['session_name']);
                });
            }
            unset($row);
        }

        $programList = [];
        $programMeta = [];
        $programSubList = [];
        $sessionList = [];

        if($model->load(Yii::$app->request->post())){
            if($model->submit()){
                Yii::$app->session->addFlash('success', 'Your application has been submitted.');
                return $this->refresh();
            }
        }

        return $this->render('jury-apply', [
            'model' => $model,
            'juryMatrix' => $juryMatrix,
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

        if(!Yii::$app->request->isPost){
            $prefill = Yii::$app->session->get('registerPrefillFromLogin');
            Yii::$app->session->remove('registerPrefillFromLogin');
            if(is_array($prefill)){
                $model->username = $prefill['username'] ?? null;
                $model->password = $prefill['password'] ?? null;
                $model->password_repeat = $prefill['password'] ?? null;
            }
        }
        
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
        date_default_timezone_set("Asia/Kuala_Lumpur");
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->addFlash('error', "You need to login here to proceed with attendance record. Kindly register if you haven't already");
            return $this->redirect(['login', 't' => $t]);
        }else{
            
            //record je la
            $session = Session::findOne(['token' => $t]);

            if($session){
                $att = new SessionAttendance();
                $att->user_id = Yii::$app->user->identity->id;
                $att->session_id = $session->id;
                $att->scanned_at = new Expression("NOW()");
                if($att->save()){
                    Yii::$app->session->addFlash('success', "Your attendance has been recorded.");
                    return $this->redirect(['/session/participant']);
                }else{
                    $errors = $att->getFirstErrors();
                    $message = $errors ? reset($errors) : 'Error in recording attendance.';
                    Yii::$app->session->addFlash('error', "Failed to record attendance: " . $message);
                }
                
            }else{
                Yii::$app->session->addFlash('error', "Failed to record attendance due to invalid session.");
            }

            return $this->redirect(['index']);
        }
        
        
    }
}
