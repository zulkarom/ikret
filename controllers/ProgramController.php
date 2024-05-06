<?php

namespace app\controllers;

use app\models\Member;
use app\models\Model;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use app\models\Program;
use app\models\ProgramRegistration;
use app\models\Questionnaire;
use app\models\QuestionnaireAnswer;
use app\models\QuestionnaireAnswerPost;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
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

        $check = QuestionnaireAnswer::findOne(['user_id' => Yii::$app->user->identity->id]);
        if(!$check){
            Yii::$app->session->addFlash('info', "You need to answer pre-event questionnaire before registering to any program below.");
        }

        $registered = ProgramRegistration::find()
        ->where(['user_id' => Yii::$app->user->identity->id])
        ->all();

        $arr = ArrayHelper::map($registered, 'id', 'id');

        $programs = Program::find()
        ->where(['NOT IN', 'id', $arr])
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
        $check = QuestionnaireAnswer::findOne(['user_id' => Yii::$app->user->identity->id]);
        if($check){
            Yii::$app->session->addFlash('error', "You have answered this pre-event question.");
            return $this->render('empty');
        }
        $quest_likert = Questionnaire::find()
        ->where(['pre_post' => 1, 'question_type' => 1])
        ->orderBy('question_order ASC')
        ->all();

        $quest_essay = Questionnaire::find()
        ->where(['pre_post' => 1, 'question_type' => 2])
        ->orderBy('question_order ASC')
        ->all();

        $model = new QuestionnaireAnswer();
        $model->user_id = Yii::$app->user->identity->id;
        //time zone

        if ($model->load(Yii::$app->request->post())) {
            $model->submitted_at = new Expression('NOW()');
            if($model->save()){
                Yii::$app->session->addFlash('success', "Thank you, your pre-event questionnaire has been successfully submitted. Please proceed to program registration.");
                return $this->redirect(['index']);
            }else{
                if($model->getErrors()){
                    foreach($model->getErrors() as $error){
                        if($error){
                            foreach($error as $e){
                                Yii::$app->session->addFlash('error', $e);
                            }
                        }
                    }
                }
            }
            
        }

        return $this->render('prequestion',[
            'quest_likert' => $quest_likert,
            'quest_essay' => $quest_essay,
            'model' => $model
        ]);
    }

    public function actionPostquestion($fresh = false)
    {
        //check dah register event
        $check = ProgramRegistration::find()->where(['user_id' => Yii::$app->user->identity->id])
        ->andWhere(['>', 'status', 0])
        ->one();
        if(!$check){
            Yii::$app->session->addFlash('error', "Please proceed to program registration first before post-event questionnaire.");
            return $this->render('empty');
        }

        $check = QuestionnaireAnswerPost::findOne(['user_id' => Yii::$app->user->identity->id]);
        if($check){
            if(!$fresh){
                Yii::$app->session->addFlash('error', "You have answered this post-event question.");
            }
            
            return $this->render('empty');
        }

        $quest_likert = Questionnaire::findAll(['pre_post' => 2, 'question_type' => 1]);
        $quest_essay = Questionnaire::findAll(['pre_post' => 2, 'question_type' => 2]);

        $model = new QuestionnaireAnswerPost();
        $model->user_id = Yii::$app->user->identity->id;

        if ($model->load(Yii::$app->request->post())) {
            $model->submitted_at = new Expression('NOW()');
            if($model->save()){
                Yii::$app->session->addFlash('success', "Thank you, your post-event questionnaire has been successfully submitted.");
                return $this->redirect(['postquestion', 'freash' => 1]);
            }else{
                if($model->getErrors()){
                    foreach($model->getErrors() as $error){
                        if($error){
                            foreach($error as $e){
                                Yii::$app->session->addFlash('error', $e);
                            }
                        }
                    }
                }
            }
            
        }

        return $this->render('postquestion',[
            'quest_likert' => $quest_likert,
            'quest_essay' => $quest_essay,
            'model' => $model
        ]);
    }


    public function actionRegister($id){
        date_default_timezone_set("Asia/Kuala_Lumpur");
        $check = QuestionnaireAnswer::findOne(['user_id' => Yii::$app->user->identity->id]);
        if(!$check){
            Yii::$app->session->addFlash('info', "You need to answer pre-event questionnaire before registering to the program.");
            return $this->redirect(['prequestion']);
        }

        $model = $this->findModel($id);
        $register = new ProgramRegistration();
        $register->group_member =1;
        $register->scenario = 'program'.$id;
        $register->status == 0;
        $defaultMember = new Member();
        $defaultMember->member_name = Yii::$app->user->identity->fullname;
        $defaultMember->member_matric = Yii::$app->user->identity->matric;
       /// echo $defaultMember->member_matric;die();
        $members = [$defaultMember];

        $register->program_id = $model->id;
        $register->user_id = Yii::$app->user->identity->id;

        if($register->load(Yii::$app->request->post())){
            $register->project_name = $this->myTrim($register->project_name);
            $register->created_at = time();
            $register->updated_at = time();

            $action =  Yii::$app->request->post('action');

            if($action == 'submit'){
                $register->status = 10;
                $register->submitted_at = new Expression('NOW()');
            }else if($action == 'draft'){
                $register->status = 0;
            }

            $register->uploadFile('payment');
            $register->uploadFile('poster');


            $members = Model::createMultiple(Member::class);
            Model::loadMultiple($members, Yii::$app->request->post());
        
            $valid = $register->validate();
            
            $valid = Model::validateMultiple($members) && $valid;
            //$valid = true;
            if ($valid) {
               
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    
                    if ($flag = $register->save()) {

                        foreach ($members as $i => $member) {
                            if ($flag === false) {
                                break;
                            }
                            //do not validate this in model
                            $member->program_reg_id = $register->id;

                            if (!($flag = $member->save(false))) {
                                break;
                            }
                        }

                    }else{
                        $register->flashError();
                    }

                    if ($flag) {

                        $transaction->commit();

                        if($action == 'submit'){
                            Yii::$app->session->addFlash('success', "Registration successful.");
                            return $this->redirect(['index']);
                        }else if($action == 'draft'){
                            Yii::$app->session->addFlash('success', "The information has been successfully saved.");
                            return $this->redirect(['view-register', 'id' => $register->program_id, 'reg' => $register->id]);
                        }

                    } else {
                        $transaction->rollBack();
                    }
                } catch (\Exception $e) {
                    
                    Yii::$app->session->addFlash('error', $e->getMessage());
                    $transaction->rollBack();
                    
                }
            }else{
                $register->flashError();
            }

        }
        
        return $this->render('register', [
            'model' => $model,
            'register' => $register,
            'members' => (empty($members)) ? [$defaultMember] : $members
        ]);
    }

    private function myTrim($str){
        $str = str_replace(array("\r", "\n"), ' ', $str);
        $str = preg_replace('/\s+/', ' ', $str);
        $str = trim($str);
        $str = rtrim($str, '.'); // buang noktah
        return $str;
    }


    public function actionViewRegister($id, $reg){
        date_default_timezone_set("Asia/Kuala_Lumpur");
        $model = $this->findModel($id);
        $register = $this->findRegistration($reg);
        $members = $register->members;
        //print_r(Yii::$app->request->post());die();
        if ($register->load(Yii::$app->request->post())){
            $register->project_name = $this->myTrim($register->project_name);
            $register->updated_at = time();
            $action =  Yii::$app->request->post('action');

        if($action == 'submit'){
            $register->status = 10;
            $register->submitted_at = new Expression('NOW()');
        }else if($action == 'draft'){
            $register->status = 0;
        }

        $register->uploadFile('payment');
        $register->uploadFile('poster');

        $oldIDs = ArrayHelper::map($members, 'id', 'id');
            
            
            $members = Model::createMultiple(Member::classname(), $members);
            
            Model::loadMultiple($members, Yii::$app->request->post());
            
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($members, 'id', 'id')));
        
            $valid = $register->validate();
            
            $valid = Model::validateMultiple($members) && $valid;
            
            if ($valid) {

                $transaction = Yii::$app->db->beginTransaction();
                
                try {
                    if ($flag = $register->save(false)) {
                        if (! empty($deletedIDs)) {
                            Member::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($members as $i => $member) {
                            if ($flag === false) {
                                break;
                            }
                            //do not validate this in model
                            $member->program_reg_id = $register->id;

                            if (!($flag = $member->save(false))) {
                                break;
                            }
                        }

                    }

                    if ($flag) {
                        $transaction->commit();
                        if($action == 'submit'){
                            Yii::$app->session->addFlash('success', "Registration successful.");
                            return $this->redirect(['index']);
                        }else if($action == 'draft'){
                            Yii::$app->session->addFlash('success', "The information has been successfully saved.");
                            return $this->redirect(['view-register', 'id' => $register->program_id, 'reg' => $register->id]);
                        }
                    } else {
                        $transaction->rollBack();
                    }
                } catch (\Exception $e) {
                    Yii::$app->session->addFlash('error', $e->getMessage());
                    $transaction->rollBack();
                    
                }
            }else{
                $register->flashError();
            }

        }
        
        return $this->render('register', [
            'model' => $model,
            'register' => $register,
            'members' => (empty($members)) ? [new Member()] : $members
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
