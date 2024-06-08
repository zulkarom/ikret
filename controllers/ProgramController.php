<?php

namespace app\controllers;

use app\models\Certificate;
use app\models\CertificateTemplate;
use app\models\JuryAssign;
use app\models\Member;
use app\models\Mentor;
use app\models\Model;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use app\models\Program;
use app\models\ProgramAchievement;
use app\models\ProgramRegistration;
use app\models\ProgramRubric;
use app\models\Questionnaire;
use app\models\QuestionnaireAnswer;
use app\models\QuestionnaireAnswerPost;
use app\models\Rubric;
use app\models\RubricAnswer;
use app\models\Setting;
use app\models\Upload;
use app\models\UserRole;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
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
            Yii::$app->session->addFlash('info', "You need to answer <a href='".Url::to(['program/prequestion'])."'>pre-event questionnaire</a> before registering to any program below.");
        }

        $registered = ProgramRegistration::find()
        ->where(['user_id' => Yii::$app->user->identity->id])
        ->all();

        $arr = ArrayHelper::map($registered, 'program_id', 'program_id');

        $programs = Program::find()
        //->where(['NOT IN', 'id', $arr])
        ->all();

        return $this->render('index',[
            'programs' => $programs,
            'registered' => $registered
        ]);
    }

    public function actionInfo($id){
        if(!Yii::$app->user->identity->isManager) return false;
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if($model->save()){
                Yii::$app->session->addFlash('success', "Data Updated");
                return $this->refresh();
            }
        }

        return $this->render('info',[
            'model' => $model
        ]);
    }

    public function actionViewRubric($id){
        if(!Yii::$app->user->identity->isManager) return false;

        $rubric = $this->findRubric($id);
        $assign = new JuryAssign();
        $assign->rubric_id = $rubric->id;

        $model = new RubricAnswer();

        return $this->render('../program-registration/jury-judge',[
            'model' => $model,
            'assign' => $assign,
            'plain' => true,
            'title' => 'View Rubric',
            'write' => false,
        ]);
    }

    public function actionRubrics($id, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;
        $role = UserRole::findOne(['program_id' => $id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager', 'program_sub' => $sub]);

        if(!$role){
            return;
        }

        $programSub = null;
        $program = $role->program;
        if($role->program->has_sub == 1){
            if($sub){
                $programSub = $role->programSub;
            }else{
                throw new NotFoundHttpException('Please provide sub program.');
            }
        }

        if($programSub){
            $rubrics = ProgramRubric::find()
            ->where(['program_id' => $id, 'program_sub' => $sub])->all();
        }else{
            $rubrics = ProgramRubric::find()->where(['program_id' => $id])->all();
        }
        
        $model = $this->findModel($id);
        return $this->render('rubrics',[
            'model' => $model,
            'rubrics' => $rubrics,
            'programSub' => $programSub
        ]);
    }

    public function actionAchievement($id, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;
        $role = UserRole::findOne(['program_id' => $id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager']);

        if(!$role){
            return;
        }

        $programSub = null;
        $program = $role->program;
        if($role->program->has_sub == 1){
            if($sub){
                $programSub = $role->programSub;
            }else{
                throw new NotFoundHttpException('Please provide sub program.');
            }
        }

        if($programSub){
            $achievement = ProgramAchievement::find()
            ->where(['program_id' => $id, 'program_sub' => $sub])->all();
        }else{
            $achievement = ProgramAchievement::find()->where(['program_id' => $id])->all();
        }
        
        $model = $this->findModel($id);
        return $this->render('achievements',[
            'model' => $model,
            'achievement' => $achievement,
            'programSub' => $programSub
        ]);
    }

    public function actionRegisterFields($id, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;
        $role = UserRole::findOne(['program_id' => $id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager']);
        $program = $role->program;
        $register = new ProgramRegistration();
        $register->program_id = $program->id;

        return $this->render('register',[
            'model' => $program,
            'register' => $register,
            'members' => [new Member()],
            'demo' => true,
        ]);
    }

    public function actionCertificate()
    {
        $check = QuestionnaireAnswerPost::findOne(['user_id' => Yii::$app->user->identity->id]);
        if(!$check){
            Yii::$app->session->addFlash('info', "Please be noted that you need to complete post-event questionnaire before getting the access to the certificate.");
            return $this->render('empty');
        }
        $setting = Setting::findOne(1);
        $allow_from = $setting->allow_cert_from;
        if(time() < strtotime($allow_from)){
            Yii::$app->session->addFlash('info', "The certificate will be available after the program date.");
            //return $this->render('empty');
        }

        $registered = ProgramRegistration::find()
        ->where(['user_id' => Yii::$app->user->identity->id, 'status' => 10])
        ->all();

        return $this->render('certificate',[
            'registered' => $registered
        ]);
    }

    public function actionPrequestion($fresh = false)
    {
        $check = QuestionnaireAnswer::findOne(['user_id' => Yii::$app->user->identity->id]);
        if($check){
            if(!$fresh){
                Yii::$app->session->addFlash('error', "You have answered this pre-event question.");
            }
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

        $quest_checkbox = Questionnaire::find()
        ->where(['pre_post' => 1, 'question_type' => 3])
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
            'quest_checkbox' => $quest_checkbox,
            'model' => $model
        ]);
    }

    public function actionPostquestion($fresh = false)
    {
        //check dah register event
        $check = ProgramRegistration::find()->where(['user_id' => Yii::$app->user->identity->id])
        ->andWhere(['>', 'status', 0])
        ->all();

        if($check){
            foreach($check as $p){
                if($p->program->has_sub && $p->program->programSubs){
                    foreach($p->program->programSubs as $sub){
                        if(time() < strtotime($sub->date_end)){
                            Yii::$app->session->addFlash('error', "To answer this post-questionnaire, you need to wait until the program (".$sub->sub_name.") ends.");
                            return $this->render('empty');
                        }
                    }
                }else{
                    if(time() < strtotime($p->program->date_end)){
                        Yii::$app->session->addFlash('error', "To answer this post-questionnaire, you need to wait until the program (".$p->program->program_name.") ends.");
                        return $this->render('empty');
                    }
                }
            }
        }else{
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
        $quest_checkbox = Questionnaire::find()
        ->where(['pre_post' => 1, 'question_type' => 3])
        ->orderBy('question_order ASC')
        ->all();

        $model = new QuestionnaireAnswerPost();
        $model->user_id = Yii::$app->user->identity->id;

        if ($model->load(Yii::$app->request->post())) {
            $model->submitted_at = new Expression('NOW()');
            if($model->save()){
                Yii::$app->session->addFlash('success', "Thank you, your post-event questionnaire has been successfully submitted.");
                return $this->redirect(['postquestion', 'fresh' => 1]);
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
            'quest_checkbox' => $quest_checkbox,
            'model' => $model
        ]);
    }


    public function actionRegisterForm($id, $reg = null){

        date_default_timezone_set("Asia/Kuala_Lumpur");
        $check = QuestionnaireAnswer::findOne(['user_id' => Yii::$app->user->identity->id]);
        if(!$check){
            Yii::$app->session->addFlash('info', "You need to answer pre-event questionnaire before registering to the program.");
            return $this->redirect(['prequestion']);
        }

        $model = $this->findModel($id);

        if($reg){
            $register = $this->findRegistration($reg);
            $members = $register->members;
        }else{
            $register = new ProgramRegistration();
            $defaultMember = new Member();
            $defaultMember->member_name = Yii::$app->user->identity->fullname;
            $defaultMember->member_matric = Yii::$app->user->identity->matric;
            $members = [$defaultMember];
            $register->program_id = $model->id;
            $register->user_id = Yii::$app->user->identity->id;
            $register->status = 0;
        }
        
        $register->scenario = 'draft';
        
        
        return $this->render('register', [
            'model' => $model,
            'register' => $register,
            'err' => false,
            'members' => (empty($members)) ? [$defaultMember] : $members,
            'demo' => false
        ]);
    }

    /**
     * method ni hanya utk post - khusus untuk store data
     */
    public function actionRegister(){
        $id = Yii::$app->request->post('program_id');
        $reg = Yii::$app->request->post('reg_id');
        date_default_timezone_set("Asia/Kuala_Lumpur");
        $check = QuestionnaireAnswer::findOne(['user_id' => Yii::$app->user->identity->id]);
        if(!$check){
            Yii::$app->session->addFlash('info', "You need to answer pre-event questionnaire before registering to the program.");
            return $this->redirect(['prequestion']);
        }
        
        $model = $this->findModel($id);
        
        if($reg){
            //echo 'reg';die();
            $register = $this->findRegistration($reg);
            $members = $register->members;
        }else{
            //echo 'not reg';die();
            $register = new ProgramRegistration();
            $defaultMember = new Member();
            $defaultMember->member_name = Yii::$app->user->identity->fullname;
            $defaultMember->member_matric = Yii::$app->user->identity->matric;
            $members = [$defaultMember];
            $register->program_id = $model->id;
            $register->user_id = Yii::$app->user->identity->id;
        }
        
        $register->scenario = 'draft';
        $register->status = 0;


        if($register->load(Yii::$app->request->post())){

            //verify dia nk register ke belum
            $p = $register->program_id;
            $sub = $register->program_sub;
            $ada = ProgramRegistration::find()->where(['program_id' => $p, 'user_id' => Yii::$app->user->identity->id]);
            if($sub){
                $ada = $ada->andWhere(['program_sub' => $sub]);
            }
            $ada = $ada->one();
            if($ada){
                Yii::$app->session->addFlash('error', "You have registered to this program");
            }else{
                $action =  Yii::$app->request->post('action');
                if($action == 'submit'){
                    $register->status = 10;
                    $register->scenario = 'program'.$id;
                    $register->submitted_at = new Expression('NOW()');
                }
    
                $register->group_member = 1;
                $register->project_name = $this->myTrim($register->project_name);
                if($register->isNewRecord){
                    $register->created_at = time();
                }
                
                $register->updated_at = time();
                $register->uploadFile('payment');
                $register->uploadFile('poster');
    
                if(!$register->isNewRecord){
                    $oldIDs = ArrayHelper::map($members, 'id', 'id');
                }
    
    
                $members = Model::createMultiple(Member::class);
                Model::loadMultiple($members, Yii::$app->request->post());
    
                if(!$register->isNewRecord){
                    $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($members, 'id', 'id')));
                }
            
                $valid = $register->validate();
                
                $valid = Model::validateMultiple($members) && $valid;
                //$valid = true;
                if ($valid) {
                   
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        
                        if ($flag = $register->save()) {
                            if(!$register->isNewRecord){
                                if (! empty($deletedIDs)) {
                                    Member::deleteAll(['id' => $deletedIDs]);
                                }
                            }
    
                            foreach ($members as $i => $member) {
                                if ($flag === false) {
                                    break;
                                }
                                $member->member_name = strtoupper($member->member_name);
                                //do not validate this in model
                                $member->program_reg_id = $register->id;
                                
    
                                if (!($flag = $member->save(false))) {
                                    break;
                                }
                            }
    
                            //mentor
                                $flag = $this->processMentor($register);
    
                        }else{
                            $register->flashError();
                        }
    
                        if ($flag) {
    
                            $transaction->commit();
    
                            if($action == 'submit'){
                                Yii::$app->session->addFlash('success', "Registration successful.");
                            }else if($action == 'draft'){
                                Yii::$app->session->addFlash('success', "The information has been successfully saved.");
                            }
    
    
                            return $this->redirect(['register-form', 'id' => $register->program_id, 'reg' => $register->id]);
                            
    
                        } else {
                            $register->status = 0;
                            $transaction->rollBack();
                        }
                    } catch (\Exception $e) {
                        $register->status = 0;
                        Yii::$app->session->addFlash('error', $e->getMessage());
                        $transaction->rollBack();
                        
                    }
                }else{
                    $register->flashError();
                    $register->status = 0;
                }
            }


            

        }
        //kena render jgk in case ada error
        return $this->render('register', [
            'model' => $model,
            'register' => $register,
            'err' => true,
            'members' => (empty($members)) ? [$defaultMember] : $members,
            'demo' => false,
        ]);
    }

    private function processMentor($model){
        if($model->mentor_main){
            //check dah ada ke
            $main = Mentor::findOne(['program_reg_id' => $model->id, 'is_main' => 1]);
            if($main){
                $main->user_id = $model->mentor_main;
                if(!$main->save()){
                    return false;
                }
            }else{
                $main = new Mentor();
                $main->program_reg_id = $model->id;
                $main->user_id = $model->mentor_main;
                $main->is_main = 1;
                if(!$main->save()){
                    return false;
                }
            }
        }else{
            //del
            $main = Mentor::findOne(['program_reg_id' => $model->id, 'is_main' => 1]);
            if($main){
                $main->delete();
            }
        }
        if($model->mentor_co){
            if($model->mentor_co != $model->mentor_main){
                //check dah ada ke
                $co = Mentor::findOne(['program_reg_id' => $model->id, 'is_main' => 0]);
                if($co){
                    $co->user_id = $model->mentor_co;
                    if(!$co->save()){
                        return false;
                    }
                }else{
                    $co = new Mentor();
                    $co->program_reg_id = $model->id;
                    $main->user_id = $model->mentor_co;
                    $co->is_main = 0;
                    if(!$co->save()){
                        return false;
                    }
                }
            }else{
                Yii::$app->session->addFlash('error', "Main & Co mentor cannot be the same person!");
                return false;
            }
            
        }else{
            //del
            $co = Mentor::findOne(['program_reg_id' => $model->id, 'is_main' => 0]);
            if($co){
                $co->delete();
            }
        }
    return true;
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

        $action =  Yii::$app->request->post('action');
            if($action == 'submit'){
                $register->status = 10;
                $register->scenario = 'program'.$id;
                $register->submitted_at = new Expression('NOW()');
            }

        //print_r(Yii::$app->request->post());die();
        if ($register->load(Yii::$app->request->post())){
            $register->group_member = 1;
            $register->project_name = $this->myTrim($register->project_name);
            $register->updated_at = time();
            $action =  Yii::$app->request->post('action');


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

                            $member->member_name = strtoupper($member->member_name);
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
                            
                        }else if($action == 'draft'){
                            Yii::$app->session->addFlash('success', "The information has been successfully saved.");
                            
                        }
                        return $this->refresh();

                    } else {
                        $transaction->rollBack();
                    }
                } catch (\Exception $e) {
                    Yii::$app->session->addFlash('error', $e->getMessage());
                    $transaction->rollBack();
                    
                }
            }else{
                $register->flashError();
                $register->status = 0;
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

    protected function findRubric($id)
    {
        if (($model = Rubric::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findMember($reg, $m)
    {
        $model = Member::find()
        ->where(['id' => $m, 'program_reg_id' => $reg])
        ->one();
        if ($model !== null) {
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

    public function actionDownloadPosterFile($id){
        $model = $this->findRegistration($id);
        Upload::download($model, 'poster', 'Poster_iCreate');
    }

    public function actionDownloadPaymentFile($id){
        $model = $this->findRegistration($id);
        Upload::download($model, 'payment', 'Payment_iCreate');
    }

    public function actionCertParticipation($reg,$m){
        $pdf = new Certificate;
        $reg = $this->findRegistration($reg);
        $member = $this->findMember($reg, $m);
        $pdf->template = CertificateTemplate::findOne(1);
        $pdf->model = $member;
        $pdf->generatePdf();
    }

}
