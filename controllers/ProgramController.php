<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use app\models\Program;
use app\models\ProgramRegistration;
use app\models\Questionnaire;
use yii\web\NotFoundHttpException;

class ProgramController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
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
        //Yii::$app->session->addFlash('success', "hai");

        $programs = Program::find()->all();
        $registered = ProgramRegistration::find()
        ->where(['user_id' => Yii::$app->user->identity->id])
        ->all();

        return $this->render('index',[
            'programs' => $programs,
            'registered' => $registered
        ]);
    }

    public function actionCertificate()
    {
        Yii::$app->session->addFlash('info', "The certificate will be available after the program date. Please be noted that you need to complete post-event questionnaire before getting the access to the certificate.");

        return $this->render('certificate',[
        ]);
    }

    public function actionPrequestion()
    {
        $quest = Questionnaire::findAll(['pre_post' => 1]);
        return $this->render('prequestion',[
            'quest' => $quest
        ]);
    }

    public function actionPostquestion()
    {
        $quest = Questionnaire::findAll(['pre_post' => 2]);
        return $this->render('postquestion',[
            'quest' => $quest
        ]);
    }


    public function actionRegister($id){
        
        $model = $this->findModel($id);
        $register = new ProgramRegistration();
        $register->program_id = $model->id;
        $register->user_id = Yii::$app->user->identity->id;

        
        
        if ($register->load(Yii::$app->request->post())){
       
            if ($register->save()) {
                Yii::$app->session->addFlash('success', "Registration successful.");
                //login terus
                
                return $this->redirect(['index']);
            }else{
                Yii::$app->session->addFlash('error', "Sorry, registration is not successful.");
            }

        }
        
        return $this->render('register', [
            'model' => $model,
            'register' => $register,
        ]);
    }

    public function actionViewRegister($id, $reg){
        
        $model = $this->findModel($id);
        $register = $this->findRegistration($reg);
        
        if ($register->load(Yii::$app->request->post())){
       
            if ($register->save()) {
                Yii::$app->session->addFlash('success', "Registration successful.");
                //login terus
                
                return $this->redirect(['index']);
            }else{
                Yii::$app->session->addFlash('error', "Sorry, registration is not successful.");
            }

        }
        
        return $this->render('register', [
            'model' => $model,
            'register' => $register,
        ]);
    }

    /**
     * Finds the Program model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Program the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Program::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findRegistration($id)
    {
        if (($model = ProgramRegistration::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
