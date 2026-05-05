<?php

namespace app\controllers;

use app\models\CertificateJury;
use app\models\CertificateTemplate;
use app\models\JuryAssign;
use app\models\JuryAssignSearch;
use app\models\JuryApplicationSearch;
use app\models\JuryResultSearch;
use app\models\ManagerAnalysisSearch;
use app\models\ManagerSessionSearch;
use app\models\Member;
use app\models\Mentor;
use app\models\MentorMenteesSearch;
use app\models\ParticipantAchieve;
use app\models\Program;
use app\models\ProgramAchievement;
use app\models\ProgramRegField;
use app\models\ProgramRegistration;
use app\models\ProgramRegistrationManagerSearch;
use app\models\ProgramRegistrationSearch;
use app\models\ProgramRubric;
use app\models\ProgramSub;
use app\models\JuryApplication;
use app\models\QuestionnaireAnswer;
use app\models\Rubric;
use app\models\RubricAnswer;
use app\models\Setting;
use app\models\User;
use app\models\UserRole;
use app\models\JuryProfile;
use app\models\RubricJudgingSession;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * ProgramRegistrationController implements the CRUD actions for ProgramRegistration model.
 */
class ProgramRegistrationController extends Controller
{
    /**
     * @inheritDoc
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
     * Lists all ProgramRegistration models.
     *
     * @return string
     */
    public function actionIndex()
    {
        if(!Yii::$app->user->identity->isAdmin) return false;
        $searchModel = new ProgramRegistrationSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
                'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
        ]);
    }

    public function actionJuryCertPdf($p, $s = null, $u = null){
        //$u = Yii::$app->user->identity->id;
        if($u){
            $u = $this->findUser($u);
        }else{
            $u = Yii::$app->user->identity;
        }
        if(!$this->canAccessDoc($u, $p,$s)) return false;

        $pdf = new CertificateJury;
        
        $assign = $this->findAssignmentByProgram($u, $p, $s);
        $pdf->template = CertificateTemplate::findOne(3);
        $pdf->model = $assign;
        $pdf->generatePdf();
        exit;
    }

    private function canAccessDoc($u, $p, $s){
        if(Yii::$app->user->identity->isManager){
            return true;
        }else{
            $role = $this->findAssignmentByProgram($u, $p, $s);
            if($role->user_id == Yii::$app->user->identity->id){
                return true;
            }
        }
        return false;
    }

    public function actionJuryCertPage($u=null)
    {
        $setting = Setting::findOne(1);
        $admin = $u && Yii::$app->user->identity->isManager;
        if(time() < strtotime($setting->allow_cert_from) && !$admin){
            Yii::$app->session->addFlash('info', "Certificates have not been published.");
            return $this->render("empty");
        }
        if($u){
            $user = $this->findUser($u);
        }else{
            $user = Yii::$app->user->identity;
        }

        if(!$user->isJury) return false;
        //cari unique program/sub
        //pastikan semua assignemnt siap
        $list = JuryAssign::find()
        ->where(['user_id' => $user->id])
        ->all();
        if($list){
            foreach($list as $a){
                if($a->status != 20){
                    Yii::$app->session->addFlash('error', "Sorry, you need to finish all the assignments.");
                    return $this->render('empty');
                }
            }
            //kena dapatkan unique program
            $programs = JuryAssign::find()->alias('a')
            ->select('r.program_id, r.program_sub')
            ->joinWith(['registration r'])
            ->where(['a.user_id' => $user->id])
            ->groupBy('r.program_id, r.program_sub')
            ->all();

        }else{
            //cari & test ni dulu
            Yii::$app->session->addFlash('error', "Sorry, you dont't have any assignment.");
            return $this->render('empty');
        }


        return $this->render('jury-cert-page', [
            'list' => $list,
            'programs' => $programs,
            'user' => $user
        ]);
    }

    public function actionMentorMentees($u = null)
    {
        $setting = Setting::findOne(1);
        $admin = $u && Yii::$app->user->identity->isManager;
        if(time() < strtotime($setting->allow_cert_from) && !$admin){
            Yii::$app->session->addFlash('info', "Certificates have not been published.");
            return $this->render("empty");
        }
        if($u){
            $user = $this->findUser($u);
        }else{
            $user = Yii::$app->user->identity;
        }


        if(!$user->isMentor) return false;
        

        $searchModel = new MentorMenteesSearch(['user' => $user]);
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('mentor-mentees', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'user' => $user
        ]);
    }

    public function actionManagerViewCerts($id, $sub = null)
    {
        if(!Yii::$app->user->identity->isManager) return false;
        $role = UserRole::findOne(['program_id' => $id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager', 'program_sub' => $sub, 'status' => 10]);

        if(!$role){
            throw new ForbiddenHttpException('No access');
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
        
        $searchModel = new ProgramRegistrationManagerSearch();
            $searchModel->program_id = $role->program_id;
            $searchModel->program_sub = $sub;
            $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('manager-view-certs', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'role' => $role,
            'programSub' => $programSub

        ]);
    }

    public function actionJuryAssignment()
    {
        if(!Yii::$app->user->identity->isJury) return false;

        $searchModel = new JuryAssignSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('jury-assignment', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionJuryDelete($id, $p, $s = null){
        if(!Yii::$app->user->identity->isManager) return false;

        $assign = $this->findAssignment($id);
        $reg = $assign->registration;

        $role = UserRole::findOne(['program_id' => $p, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager', 'status' => 10]);

        if($role && $role->program){
            
            if($assign->status == 0){
                if($assign->delete()){
                    Yii::$app->session->addFlash('success', "Assignment Deleted");
                    
                }
            }
        }

        return $this->redirect(['manager', 'id' => $p, 'sub' => $s]);
    }

    public function actionJuryJudge($id){
        date_default_timezone_set("Asia/Kuala_Lumpur");
        $assign = $this->findAssignment($id);
        $register = $assign->registration;
        //kita ubah status kepada judging once buka
        if($assign->status == 0){
            $assign->status = 10;
            $assign->save();
        }
        
        //kita create terus klu takde
        //mapping one to one mcm xkena, tp just proceeds
        $ada = RubricAnswer::findOne([
            'rubric_id' => $assign->rubric_id,
            'assignment_id' => $assign->id
        ]);
        if($ada){
            $model = $ada;
        }else{
            $model = new RubricAnswer([
            'rubric_id' => $assign->rubric_id,
            'assignment_id' => $assign->id,
            'created_at' => new Expression('NOW()'),
            ]);
            $model->save();
        }

        if($assign->status <= 10 && $this->request->isPost && $model->load($this->request->post())) {
            
            $action = $this->request->post('action');
            //print_r($this->request->post());

            $nullify = $this->request->post('nullify');
            $assign->is_nullified = $nullify == 1 ? 1 : 0;
            $assign->reason_nullified = $this->request->post('reason_nullified');
            $valid = true;
            if($assign->is_nullified == 1 && !$assign->reason_nullified){
                Yii::$app->session->addFlash('error', "You need to state the reason of nullification.");
                $valid = false;
            }

            //echo $nullify;die();
            
            $model->updated_at = new Expression('NOW()');
            if($action == 'submit' && $valid){
                if($model->isComplete || $assign->is_nullified == 1){
                    $model->submitted_at = new Expression('NOW()');
                    $transaction = Yii::$app->db->beginTransaction();

                    try {
                        if($model->save()){
                            $assign->status = 20;

                            //put score in jury assign
                            if($assign->is_nullified == 1){
                                $assign->score = 0;
                            }else{
                                $assign->score = $model->scoreValue;
                            }
                            
                            if($assign->save()){
                                //calc average put score in registration
                                $register->setScoreAndAward();
                                $register->save();
                            }else{
                                Yii::$app->session->addFlash('error', "Failed to update status");
                            }
                        }
                        
                        
                        $transaction->commit();

                        Yii::$app->session->addFlash('success', "Thank you, you have completed the judging session for this participant.");
                        return $this->refresh();
                    }catch (\Exception $e){
                        $transaction->rollBack();
                        Yii::$app->session->addFlash('error', $e->getMessage());
                    }
                }else{
                    Yii::$app->session->addFlash('error', "You need to complete all first before submitting.");
                }
            }else{

                //put score in jury assign
                if($assign->is_nullified == 1){
                    $assign->score = 0;
                }else{
                    $assign->score = $model->scoreValue;
                }
                if($assign->save()){
                    if($model->save()){
                    Yii::$app->session->addFlash('success', "Data Updated");
                    return $this->refresh();
                }
                }
                
            }
        }

        return $this->render('jury-judge', [
            'assign' => $assign,
            'model' => $model,
            'plain' => false,
            'title' => 'Judging Session',
            'write' => true,
        ]);
    }

    /**
     * Displays a single ProgramRegistration model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            
        ]);
    }

    public function actionViewResult($id)
    {
        $assign = $this->findAssignment($id);
        $model = RubricAnswer::findOne([
            'rubric_id' => $assign->rubric_id,
            'assignment_id' => $assign->id
        ]);

        return $this->render('jury-judge', [
            'assign' => $assign,
            'model' => $model,
            'plain' => false,
            'title' => 'View Result',
            'write' => false,
        ]);
    }

    public function actionAchieveDelete($id){
        if(!Yii::$app->user->identity->isManager) return false;
        $model = $this->findAchievement($id);
        $reg = $model->registration->id;
        $sub = $model->registration->program_sub;
        if($model->delete()){
            Yii::$app->session->addFlash('success', "Achievement Deleted");
        }

        return $this->redirect(['manager-view', 'id' => $reg, 'sub' => $sub]);

    }

    public function actionManagerView($id, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;
        $model = $this->findModel($id);
        $role = UserRole::findOne(['program_id' => $model->program_id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager', 'program_sub' => $sub, 'status' => 10]);

        if(!$role){
            return;
        }

        $programSub = null;
        $program = $role->program;
        if($model->program->has_sub == 1){
            if($sub){
                $programSub = $role->programSub;
            }else{
                throw new NotFoundHttpException('Please provide sub program.');
            }
        }



        return $this->render('manager-view', [
            'model' => $model,

        ]);
    }

    public function actionManagerAward($id, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;
        $model = $this->findModel($id);
        $role = UserRole::findOne(['program_id' => $model->program_id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager', 'status' => 10]);

        if(!$role){
            return;
        }

        $programSub = null;
        $program = $role->program;
        if($model->program->has_sub == 1){
            if($sub){
                $programSub = $role->programSub;
            }else{
                throw new NotFoundHttpException('Please provide sub program.');
            }
        }

        if($programSub){
            $achievement = ProgramAchievement::find()
            ->where(['program_id' => $program->id, 'program_sub' => $sub])->all();
        }else{
            $achievement = ProgramAchievement::find()->where(['program_id' => $program->id])->all();
        }
        $list = ArrayHelper::map($achievement, 'id', 'name');


        $achieve = new ParticipantAchieve();
        $achieve->program_reg_id = $id;
        if ($this->request->isPost && $achieve->load($this->request->post())) {
            $achieve->achieved_at = time();
            if($achieve->save()){
                Yii::$app->session->addFlash('success', "Achievement Added");
                return $this->refresh();
            }
        }

        if ($this->request->isPost && $model->load($this->request->post())) {
            if($model->save()){
                Yii::$app->session->addFlash('success', "Medal updated");
                return $this->refresh();
            }
        }

        return $this->render('manager-award', [
            'model' => $model,
            'achieve' => $achieve,
            'list' => $list
        ]);
    }

    public function actionManagerAddJury($id){
        $model = new JuryAssign();
        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('manager-add-jury', [
            'model' => $model
        ]);
    }

    public function actionUserListJson($q = null, $id = null){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query();
            $query->select(new Expression('`id`, `fullname` AS `text`'))
                ->from('user')
                ->where(['like', 'fullname', $q])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => User::find($id)->fullname];
        }
        return $out;
    }

    public function actionMentorListJson($q = null, $id = null){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query();
            $query->select(new Expression('u.id, u.fullname AS text'))
                ->from('user u')
                ->innerJoin('user_role r','r.user_id = u.id')
                ->where(['like', 'u.fullname', $q])
                ->andWhere(['r.role_name' => 'mentor'])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => User::find($id)->fullname];
        }
        return $out;
    }

    public function actionManagerFlag($id, $flag, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;
        $reg = $this->findModel($id);
        if($flag == 1){
            $reg->flag = 1;
        }else{
            $reg->flag = 0;
        }
        if($reg->save()){
            Yii::$app->session->addFlash('success', "Flagged Participants Updated");
        }
        return $this->redirect(['manager', 'id' => $reg->program_id, 'sub' => $sub]);
    }

    public function actionManagerClearForm($id, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;

        $session = Yii::$app->session;
        $this->clearSession($session);
        $session->remove('keep-open');
        Yii::$app->session->addFlash('success', "Form Cleared");
        return $this->redirect(['manager', 'id' => $id, 'sub' => $sub]);
    }


    public function actionJuryResult($id, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;

        $role = UserRole::findOne(['program_id' => $id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager', 'program_sub' => $sub, 'status' => 10]);
        if(!$role){
            throw new ForbiddenHttpException('No access');
        }
        $programSub = null;
        $program = $role->program;
        $rubrics = $program->programRubrics;

        $programSub = null;
        $program = $role->program;

        if($role->program->has_sub == 1){
            if($sub){
                $programSub = $role->programSub;
                $rubrics = $program->getProgramRubricsSub($sub)->all();
            }else{
                throw new NotFoundHttpException('Please provide sub program.');
            }
        }

        $firstRubric = null;
        if($rubrics){
            $firstRubric = $rubrics[0]->id;
        }

        $searchModel = new JuryResultSearch();
        $searchModel->program_id = $id;
        $searchModel->program_sub = $sub;
        $searchModel->rubric = $firstRubric;
        $dataProvider = $searchModel->search($this->request->queryParams);

        $selectedRubric = Rubric::findOne($searchModel->rubric);

        return $this->render('jury-result', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'program' => $program,
            'rubrics' => $rubrics,
            'selectedRubric' => $selectedRubric,
            'programSub' => $programSub,
        ]);
    }

    public function actionManager($id, $sub = null){
        $session = Yii::$app->session;
        //print_r($session->get('keep-data'));die();
        if(!Yii::$app->user->identity->isManager) return false;

        $role = UserRole::findOne(['program_id' => $id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager', 'program_sub' => $sub, 'status' => 10]);
        if(!$role){
            throw new ForbiddenHttpException('No access');
        }
        $programSub = null;
        $program = $role->program;
        if($program->program_type == 2){
            return $this->redirect(['manager-session', 'id' => $id, 'sub' => $sub]);
        }

        if($role->program->has_sub == 1){
            if($sub){
                $programSub = $role->programSub;
            }else{
                throw new NotFoundHttpException('Please provide sub program.');
            }
        }

        if($role && $role->program){
            
            $model = new JuryAssign();
            $model->scenario = 'assign';
            if ($session->has('keep-data') && $session->get('keep-data') == 1){
                $model->users = $session->get('users');
                $model->rubric_id = $session->get('rubric_id');
                $model->keep_data = $session->get('keep_data');
                $model->keep_open = $session->get('keep_open');
            }

            //cari ada stage tak
            $stages = $program->programStages;
            if($stages){
                $model->scenario = 'stage';
            }
            $model->stage = 0;
            //////////////////post
                if ($this->request->isPost && $model->load($this->request->post())) {
                    //proses session
                    //echo $model->keep_data;die();
                    if($model->keep_data == 1){
                        //die('keep data 1');
                        $session->set('keep-data', 1);
                        $session->set('users', $model->users);
                        $session->set('rubric_id', $model->rubric_id);
                        $session->set('keep_data', $model->keep_data);
                        $session->set('keep_open', $model->keep_data);
                    }else{
                        $this->clearSession($session);
                    }

                    if($model->keep_open == 1){
                        $session->set('keep-open', 1);
                    }else{
                        $session->remove('keep-open');
                    }
                    // echo '<pre>';
                    // print_r($this->request->post());die();
                    $users = $model->users;
                    $post = Yii::$app->request->post();

                    if(isset($post['selection'])){
                        $kira_juri = 0;
                        $selection = $post['selection'];
                        foreach($selection as $select){

                            if($users){
                                foreach($users as $u){
                                    //validate dh assign ke belum
                                    $ada = JuryAssign::findOne(['user_id' => $u, 'reg_id' => $select, 'stage' => $model->stage]);
                                    if($ada){
                                        $name = $ada->user->fullname;
                                        $peserta = $ada->registration->participantText;
                                        Yii::$app->session->addFlash('error', 'Failed: ' .$name . ' had been assigned to ' . $peserta);
                                    }else{
                                        $jury = new JuryAssign([
                                            'user_id' => $u,
                                            'reg_id' => $select,
                                            'stage' => $model->stage,
                                            'method' => $model->method,
                                            'location' => $model->location,
                                            'date_start' => $model->date_start,
                                            'date_end' => $model->date_end,
                                            'rubric_id' => $model->rubric_id,
                                            'note' => $model->note,
                                            'link' => $model->link,
                                            'created_at' => time(),
                                            'updated_at' => time(),
                                        ]);
                                        if($jury->save()){
                                            $kira_juri++;
                                        }else{
                                            $jury->flashError();
                                        }
                                        
                                    }
                                    
                                }
                            }

                        }
                        Yii::$app->session->addFlash('success', "Juries (".$kira_juri.") have been assigned to participants");
                    }
                    return $this->refresh();
                }
        
            $searchModel = new ProgramRegistrationManagerSearch();
            $searchModel->program_id = $role->program_id;
            $searchModel->program_sub = $sub;
            $dataProvider = $searchModel->search($this->request->queryParams);
    
            return $this->render('manager', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'role' => $role,
                'model' => $model,
                'programSub' => $programSub
            ]);
        }

        
    }

    public function actionManagerJuryApplications($id, $sub = null)
    {
        if(!Yii::$app->user->identity->isManager) return false;

        $role = UserRole::findOne([
            'program_id' => $id,
            'user_id' => Yii::$app->user->identity->id,
            'role_name' => 'manager',
            'program_sub' => $sub,
            'status' => 10,
        ]);
        if(!$role){
            throw new ForbiddenHttpException('No access');
        }

        $programSub = null;
        if((int)$role->program->has_sub === 1){
            if(!$sub){
                throw new NotFoundHttpException('Please provide sub program.');
            }
            $programSub = $role->programSub;
        }

        $searchModel = new JuryApplicationSearch();
        $searchModel->program_id = (int)$id;
        $searchModel->program_sub_id = $sub ? (int)$sub : null;

        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('manager-jury-applications', [
            'role' => $role,
            'programSub' => $programSub,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAdminJuryApplications($id, $sub = null)
    {
        if(!Yii::$app->user->identity->isAdmin) return false;

        $program = Program::findOne((int)$id);
        if(!$program){
            throw new NotFoundHttpException('Program not found.');
        }

        $programSub = null;
        if((int)$program->has_sub === 1){
            if(!$sub){
                throw new NotFoundHttpException('Please provide sub program.');
            }
            $programSub = ProgramSub::findOne((int)$sub);
            if(!$programSub || (int)$programSub->program_id !== (int)$program->id){
                throw new NotFoundHttpException('Sub program not found.');
            }
        }

        $searchModel = new JuryApplicationSearch();
        $searchModel->program_id = (int)$id;
        $searchModel->program_sub_id = $sub ? (int)$sub : null;

        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('manager-jury-applications', [
            'role' => null,
            'program' => $program,
            'programSub' => $programSub,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAdminJuryApplicationsAll()
    {
        if(!Yii::$app->user->identity->isAdmin) return false;

        $searchModel = new JuryApplicationSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('manager-jury-applications', [
            'role' => null,
            'program' => null,
            'programSub' => null,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionJuryApplicationView($id)
    {
        $model = JuryApplication::findOne((int)$id);
        if(!$model){
            throw new NotFoundHttpException('Application not found.');
        }

        if(Yii::$app->user->identity->isAdmin){
            return $this->render('jury-application-view', [
                'model' => $model,
            ]);
        }

        if(!Yii::$app->user->identity->isManager){
            throw new ForbiddenHttpException('No access');
        }

        $role = UserRole::findOne([
            'program_id' => $model->program_id,
            'user_id' => Yii::$app->user->identity->id,
            'role_name' => 'manager',
            'program_sub' => $model->program_sub_id,
            'status' => 10,
        ]);
        if(!$role){
            throw new ForbiddenHttpException('No access');
        }

        return $this->render('jury-application-view', [
            'model' => $model,
        ]);
    }

    public function actionJuryApplicationBulkUpdate()
    {
        if(!Yii::$app->request->isPost){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $ids = Yii::$app->request->post('selection', []);
        $action = Yii::$app->request->post('bulk_action');

        if(!in_array($action, ['approve', 'reject'], true)){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $targetStatus = $action === 'approve' ? 10 : 20;

        if(!$ids || !is_array($ids)){
            Yii::$app->session->addFlash('warning', 'No applications selected.');
            return $this->redirect(Yii::$app->request->referrer ?: ['admin-jury-applications-all']);
        }

        $isAdmin = !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin;
        $isManager = !Yii::$app->user->isGuest && Yii::$app->user->identity->isManager;

        if(!$isAdmin && !$isManager){
            throw new ForbiddenHttpException('No access');
        }

        $updated = 0;
        $skipped = 0;
        foreach($ids as $id){
            $app = JuryApplication::findOne((int)$id);
            if(!$app){
                $skipped++;
                continue;
            }

            if($isManager && !$isAdmin){
                $role = UserRole::findOne([
                    'program_id' => $app->program_id,
                    'user_id' => Yii::$app->user->identity->id,
                    'role_name' => 'manager',
                    'program_sub' => $app->program_sub_id,
                    'status' => 10,
                ]);
                if(!$role){
                    $skipped++;
                    continue;
                }
            }

            $app->status = $targetStatus;
            if($app->save(false, ['status'])){
                $updated++;
            }else{
                $skipped++;
            }
        }

        if($updated > 0){
            Yii::$app->session->addFlash('success', 'Updated ' . $updated . ' application(s).');
        }
        if($skipped > 0){
            Yii::$app->session->addFlash('warning', 'Skipped ' . $skipped . ' application(s).');
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['admin-jury-applications-all']);
    }

    public function actionJuryApplicationImport()
    {
        if(!Yii::$app->user->identity->isAdmin) return false;

        return $this->render('jury-application-import');
    }

    public function actionJuryApplicationImportCsv()
    {
        if(!Yii::$app->user->identity->isAdmin) return false;

        if(!Yii::$app->request->isPost){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $file = UploadedFile::getInstanceByName('csv_file');
        if(!$file){
            Yii::$app->session->addFlash('error', 'No file uploaded.');
            return $this->redirect(['jury-application-import']);
        }

        $handle = fopen($file->tempName, 'r');
        if(!$handle){
            Yii::$app->session->addFlash('error', 'Could not read uploaded file.');
            return $this->redirect(['jury-application-import']);
        }

        $header = fgetcsv($handle);
        if(!$header){
            fclose($handle);
            Yii::$app->session->addFlash('error', 'Empty CSV.');
            return $this->redirect(['jury-application-import']);
        }

        $header = array_map(function($h){
            return strtolower(trim((string)$h));
        }, $header);

        $required = ['email', 'judging_session_id'];
        foreach($required as $col){
            if(!in_array($col, $header, true)){
                fclose($handle);
                Yii::$app->session->addFlash('error', 'Missing required column: ' . $col);
                return $this->redirect(['jury-application-import']);
            }
        }

        $idx = array_flip($header);
        $created = 0;
        $skipped = 0;
        $errors = 0;

        $tx = Yii::$app->db->beginTransaction();
        try{
            while(($row = fgetcsv($handle)) !== false){
                $email = isset($idx['email']) ? trim((string)($row[$idx['email']] ?? '')) : '';
                $sessionId = isset($idx['judging_session_id']) ? (int)trim((string)($row[$idx['judging_session_id']] ?? '')) : 0;
                $programId = isset($idx['program_id']) ? (int)trim((string)($row[$idx['program_id']] ?? '')) : 0;
                $programSubId = isset($idx['program_sub_id']) ? (int)trim((string)($row[$idx['program_sub_id']] ?? '')) : null;
                $fullNameCsv = isset($idx['fullname']) ? trim((string)($row[$idx['fullname']] ?? '')) : '';

                if($email === '' || $sessionId <= 0){
                    $skipped++;
                    continue;
                }

                if($programId <= 0){
                    $session = RubricJudgingSession::findOne($sessionId);
                    if(!$session){
                        $skipped++;
                        continue;
                    }

                    $prRows = ProgramRubric::find()
                        ->select(['program_id', 'program_sub'])
                        ->where(['rubric_id' => $session->rubric_id])
                        ->asArray()
                        ->all();

                    if(!$prRows){
                        $skipped++;
                        continue;
                    }

                    if(count($prRows) !== 1){
                        $errors++;
                        continue;
                    }

                    $programId = (int)$prRows[0]['program_id'];
                    $programSubId = !empty($prRows[0]['program_sub']) ? (int)$prRows[0]['program_sub'] : null;
                }

                $user = User::find()->where(['email' => $email])->one();
                if(!$user){
                    if($fullNameCsv === ''){
                        $skipped++;
                        continue;
                    }

                    $user = new User();
                    $user->scenario = 'create';
                    $user->email = $email;
                    $user->username = $email;
                    $user->fullname = $fullNameCsv;
                    $user->status = User::STATUS_ACTIVE;
                    $user->is_student = 0;
                    $user->is_internal = 0;
                    $user->generateAuthKey();
                    $user->setPassword($email);

                    if(!$user->save()){
                        $errors++;
                        continue;
                    }
                }

                $juryProfile = JuryProfile::find()->where(['user_id' => $user->id])->one();
                if(!$juryProfile){
                    $juryProfile = new JuryProfile();
                    $juryProfile->user_id = (int)$user->id;
                    $juryProfile->fullname = $fullNameCsv !== '' ? $fullNameCsv : ($user->fullname ?? '');
                    $juryProfile->category = isset($idx['category']) ? trim((string)($row[$idx['category']] ?? '')) : '';
                    $juryProfile->phone = isset($idx['phone']) ? trim((string)($row[$idx['phone']] ?? '')) : null;
                    $juryProfile->institution = isset($idx['institution']) ? trim((string)($row[$idx['institution']] ?? '')) : null;
                    $juryProfile->designation = isset($idx['designation']) ? trim((string)($row[$idx['designation']] ?? '')) : null;
                    $juryProfile->address = isset($idx['address']) ? trim((string)($row[$idx['address']] ?? '')) : null;
                    $juryProfile->created_at = time();
                    $juryProfile->updated_at = time();

                    if(!$juryProfile->save()){
                        $errors++;
                        continue;
                    }
                }

                $exists = JuryApplication::find()->where([
                    'jury_profile_id' => $juryProfile->id,
                    'program_id' => $programId,
                    'program_sub_id' => $programSubId,
                    'judging_session_id' => $sessionId,
                ])->exists();
                if($exists){
                    $skipped++;
                    continue;
                }

                $app = new JuryApplication();
                $app->jury_profile_id = (int)$juryProfile->id;
                $app->program_id = $programId;
                $app->program_sub_id = $programSubId ?: null;
                $app->judging_session_id = $sessionId;
                $app->declaration_accepted = 1;
                $app->status = 0;
                $app->created_at = time();

                if($app->save()){
                    $created++;
                }else{
                    $errors++;
                }
            }

            fclose($handle);
            $tx->commit();
        }catch(\Throwable $e){
            fclose($handle);
            $tx->rollBack();
            throw $e;
        }

        if($created > 0){
            Yii::$app->session->addFlash('success', 'Imported ' . $created . ' application(s).');
        }
        if($skipped > 0){
            Yii::$app->session->addFlash('warning', 'Skipped ' . $skipped . ' row(s).');
        }
        if($errors > 0){
            Yii::$app->session->addFlash('error', 'Failed ' . $errors . ' row(s).');
        }

        return $this->redirect(['admin-jury-applications-all']);
    }

    public function actionManagerDashboard($id, $sub = null)
    {
        if(!Yii::$app->user->identity->isManager) return false;

        $program = Program::findOne((int)$id);
        if(!$program){
            throw new NotFoundHttpException('Program not found.');
        }

        $roleQuery = UserRole::find()->where([
            'program_id' => $id,
            'user_id' => Yii::$app->user->identity->id,
            'role_name' => 'manager',
            'status' => 10,
        ]);
        if((int)$program->has_sub === 1){
            if(!$sub){
                throw new NotFoundHttpException('Please provide sub program.');
            }
            $roleQuery->andWhere(['program_sub' => $sub]);
        }

        $role = $roleQuery->one();

        if(!$role){
            return;
        }

        $programSub = null;

        if((int)$program->has_sub === 1 && $sub){
            $programSub = $role->programSub;
        }

        return $this->render('manager-dashboard', [
            'role' => $role,
            'program' => $program,
            'programSub' => $programSub,
            'dashboardStats' => $this->buildDashboardStats($program, $programSub),
        ]);
    }

    public function actionManagerParent($id)
    {
        if(!Yii::$app->user->identity->isManager) return false;

        $role = UserRole::find()->where([
            'program_id' => $id,
            'user_id' => Yii::$app->user->identity->id,
            'role_name' => 'manager',
        ])->andWhere(['not', ['status' => null]])->andWhere(['status' => 10])->one();

        if(!$role){
            return;
        }

        $program = $role->program;
        if(!$program){
            return;
        }

        if((int)$program->has_sub === 1){
            $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
            $hasSubActiveColumn = $subTable && $subTable->getColumn('is_active');
            $activeSubIds = [];
            foreach($program->programSubs as $sp){
                if($hasSubActiveColumn && (int)$sp->getAttribute('is_active') !== 1){
                    continue;
                }
                $activeSubIds[(int)$sp->id] = true;
            }

            $roles = UserRole::find()->where([
                'program_id' => $id,
                'user_id' => Yii::$app->user->identity->id,
                'role_name' => 'manager',
                'status' => 10,
            ])->all();

            $hasProgramLevel = false;
            $allowedSubs = [];
            if($roles){
                foreach($roles as $r){
                    if(empty($r->program_sub)){
                        $hasProgramLevel = true;
                        break;
                    }
                    $allowedSubs[(int)$r->program_sub] = true;
                }
            }

            if(!$hasProgramLevel){
                foreach(array_keys($activeSubIds) as $sid){
                    if(!array_key_exists((int)$sid, $allowedSubs)){
                        throw new ForbiddenHttpException('You do not have access to the parent dashboard for this program.');
                    }
                }
            }
        }

        $subs = $program->programSubs;
        $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
        $hasSubActiveColumn = $subTable && $subTable->getColumn('is_active');
        $subStats = [];

        $rubricCounts = ProgramRubric::find()
            ->select(['program_sub', 'COUNT(*) AS cnt'])
            ->where(['program_id' => (int)$program->id])
            ->andWhere(['not', ['program_sub' => null]])
            ->groupBy(['program_sub'])
            ->asArray()
            ->all();
        $rubricsBySub = [];
        foreach($rubricCounts as $row){
            $rubricsBySub[(int)$row['program_sub']] = (int)$row['cnt'];
        }

        if($subs){
            foreach($subs as $sp){
                if($hasSubActiveColumn && (int)$sp->getAttribute('is_active') !== 1){
                    continue;
                }
                $subStats[(int)$sp->id] = $this->buildDashboardStats($program, $sp);
                $subStats[(int)$sp->id]['rubrics_count'] = $rubricsBySub[(int)$sp->id] ?? 0;
            }
        }

        return $this->render('manager-parent', [
            'role' => $role,
            'program' => $program,
            'dashboardStats' => $this->buildDashboardStats($program, null),
            'subs' => $subs,
            'subStats' => $subStats,
        ]);
    }

    public function actionImportParticipants($id, $sub = null)
    {
        if(!Yii::$app->user->identity->isManager) return false;

        $roleQuery = UserRole::find()->where([
            'program_id' => $id,
            'user_id' => Yii::$app->user->identity->id,
            'role_name' => 'manager',
            'status' => 10,
        ]);
        if($sub !== null){
            $roleQuery->andWhere(['program_sub' => $sub]);
        }
        $role = $roleQuery->one();

        if(!$role){
            return;
        }

        $program = $role->program;
        $managerSub = null;

        if($program->has_sub == 1 && $sub){
            $managerSub = $role->programSub;
        }

        $enabledFields = ProgramRegistration::getProgramFields((int)$program->id);
        $requiredFields = ProgramRegistration::getProgramRequiredFields((int)$program->id);
        $fieldLabels = ProgramRegistration::availableRegistrationFields();
        $fieldTypes = ProgramRegistration::availableRegistrationFieldTypes();
        $showMemberMatric = ProgramRegistration::groupMemberShowMatric((int)$program->id);

        $importableFieldTypes = [
            'project_name' => 'Text',
            'project_desc' => 'Textarea',
            'participant_cat_local' => 'Integer option',
            'participant_cat_group' => 'Integer option',
            'participant_mode' => 'Integer option',
            'participant_cat_umk' => 'Integer option',
            'participant_program' => 'Integer option',
            'other_program' => 'Text',
            'program_sub' => 'Integer ID',
            'advisor_dropdown' => 'Integer option',
            'booth_number' => 'Text',
            'advisor' => 'Text',
            'institution' => 'Text',
            'contact_person' => 'Text',
            'contact_no' => 'Text',
            'contact_email' => 'Email',
            'group_code' => 'Text',
            'group_name' => 'Text',
            'nric' => 'Text',
            'competition_type' => 'Integer option',
            'participant_cat_program' => 'Integer option',
            'competition_cat_program' => 'Integer option',
        ];

        $importableFields = [];
        $unsupportedFields = [];
        foreach($enabledFields as $fieldName){
            if(array_key_exists($fieldName, $importableFieldTypes)){
                $importableFields[$fieldName] = [
                    'label' => array_key_exists($fieldName, $fieldLabels) ? $fieldLabels[$fieldName] : $fieldName,
                    'type' => $importableFieldTypes[$fieldName],
                    'required' => in_array($fieldName, $requiredFields, true),
                ];
            }else{
                $unsupportedFields[$fieldName] = [
                    'label' => array_key_exists($fieldName, $fieldLabels) ? $fieldLabels[$fieldName] : $fieldName,
                    'type' => array_key_exists($fieldName, $fieldTypes) ? $fieldTypes[$fieldName] : 'Input',
                    'required' => in_array($fieldName, $requiredFields, true),
                ];
            }
        }

        if($program->has_sub == 1 && !array_key_exists('program_sub', $importableFields)){
            $importableFields = array_merge([
                'program_sub' => [
                    'label' => array_key_exists('program_sub', $fieldLabels) ? $fieldLabels['program_sub'] : 'program_sub',
                    'type' => $importableFieldTypes['program_sub'],
                    'required' => true,
                ],
            ], $importableFields);
            unset($unsupportedFields['program_sub']);
        }

        $extraColumns = [];
        if(in_array('group_member', $enabledFields, true)){
            $extraColumns['member_names'] = [
                'label' => 'Additional Members',
                'type' => 'Text',
                'required' => in_array('group_member', $requiredFields, true),
            ];
            if($showMemberMatric){
                $extraColumns['member_matrics'] = [
                    'label' => 'Additional Member Matrics',
                    'type' => 'Text',
                    'required' => false,
                ];
            }
        }

        $sampleHeaders = array_merge(array_keys($importableFields), array_keys($extraColumns));
        $sampleRows = [];

        // Create sample data for a group with 3 members
        $baseSampleRow = [];
        foreach(array_keys($importableFields) as $fieldName){
            switch($fieldName){
                case 'project_name':
                    $baseSampleRow[] = 'Sample Project';
                    break;
                case 'project_desc':
                    $baseSampleRow[] = 'Short project description';
                    break;
                case 'participant_cat_group':
                case 'participant_cat_local':
                case 'participant_cat_umk':
                case 'participant_mode':
                case 'participant_program':
                case 'competition_type':
                case 'participant_cat_program':
                case 'competition_cat_program':
                case 'advisor_dropdown':
                    $baseSampleRow[] = '1';
                    break;
                case 'program_sub':
                    $baseSampleRow[] = $managerSub ? (string)$managerSub->id : '1';
                    break;
                case 'booth_number':
                case 'group_code':
                    $baseSampleRow[] = 'G01';
                    break;
                case 'group_name':
                    $baseSampleRow[] = 'Team Alpha';
                    break;
                case 'institution':
                    $baseSampleRow[] = 'UMK';
                    break;
                case 'contact_person':
                    $baseSampleRow[] = 'Aisyah';
                    break;
                case 'contact_no':
                    $baseSampleRow[] = '0123456789';
                    break;
                case 'contact_email':
                    $baseSampleRow[] = 'team@example.com';
                    break;
                case 'advisor':
                    $baseSampleRow[] = 'Dr. Advisor';
                    break;
                case 'nric':
                    $baseSampleRow[] = '900101101010';
                    break;
                default:
                    $baseSampleRow[] = 'Sample';
                    break;
            }
        }

        // Create sample rows for team members
        $teamMembers = [
            ['name' => 'John Doe', 'matric' => 'A25A1001'],
            ['name' => 'Jane Smith', 'matric' => 'A25A1002'],
            ['name' => 'Bob Johnson', 'matric' => 'A25A1003'],
        ];

        foreach($teamMembers as $member){
            $sampleRow = $baseSampleRow;
            if(array_key_exists('member_names', $extraColumns)){
                $sampleRow[] = $member['name'];
            }
            if(array_key_exists('member_matrics', $extraColumns)){
                $sampleRow[] = $member['matric'];
            }
            $sampleRows[] = $sampleRow;
        }

        if(Yii::$app->request->isPost){
            $csvFile = UploadedFile::getInstanceByName('csv_file');

            if(!$csvFile){
                Yii::$app->session->addFlash('error', 'Please upload a CSV file.');
                return $this->refresh();
            }

            $handle = fopen($csvFile->tempName, 'r');
            if($handle === false){
                Yii::$app->session->addFlash('error', 'Unable to read the uploaded CSV file.');
                return $this->refresh();
            }

            $headers = fgetcsv($handle);
            if(!$headers){
                fclose($handle);
                Yii::$app->session->addFlash('error', 'The CSV file is empty.');
                return $this->refresh();
            }

            $headers = array_map(function($header){
                return trim((string)$header);
            }, $headers);

            $validColumns = array_merge(array_keys($importableFields), array_keys($extraColumns));
            $unknownHeaders = array_diff($headers, $validColumns);
            if($unknownHeaders){
                fclose($handle);
                Yii::$app->session->addFlash('error', 'Unknown CSV column(s): ' . implode(', ', $unknownHeaders));
                return $this->refresh();
            }

            // Read all rows and group by group_name
            $groupedData = [];
            $rowNo = 1;
            $lastGroupName = '';
            while(($row = fgetcsv($handle)) !== false){
                $rowNo++;
                $rowAssoc = [];
                foreach($headers as $index => $header){
                    $rowAssoc[$header] = array_key_exists($index, $row) ? trim((string)$row[$index]) : '';
                }

                $hasContent = false;
                foreach($rowAssoc as $value){
                    if($value !== ''){
                        $hasContent = true;
                        break;
                    }
                }
                if(!$hasContent){
                    continue;
                }

                $groupName = isset($rowAssoc['group_name']) ? trim((string)$rowAssoc['group_name']) : '';
                if($groupName === ''){
                    if($lastGroupName === ''){
                        continue; // Skip rows without group_name (no previous row to refer to)
                    }
                    $groupName = $lastGroupName;
                    $rowAssoc['group_name'] = $groupName;
                }else{
                    $lastGroupName = $groupName;
                }

                if(!isset($groupedData[$groupName])){
                    $groupedData[$groupName] = [];
                }
                $groupedData[$groupName][] = $rowAssoc;
            }
            fclose($handle);

            if(empty($groupedData)){
                Yii::$app->session->addFlash('error', 'No valid data found in CSV file.');
                return $this->refresh();
            }

            $transaction = Yii::$app->db->beginTransaction();
            $created = 0;
            $skipped = 0;

            try{
                foreach($groupedData as $groupName => $groupRows){
                    // Use the first row for registration data
                    $firstRow = $groupRows[0];

                    // Create user account for the first member
                    $firstMemberMatric = isset($firstRow['member_matrics']) ? trim((string)$firstRow['member_matrics']) : '';
                    $firstMemberName = isset($firstRow['member_names']) ? trim((string)$firstRow['member_names']) : '';

                    if($firstMemberMatric === '' || $firstMemberName === ''){
                        throw new \RuntimeException('First member must have matric and name for group: ' . $groupName);
                    }

                    // Check if user already exists
                    $existingUser = User::findByUsername($firstMemberMatric);
                    if($existingUser){
                        $user = $existingUser;
                    }else{
                        // Create new user
                        $user = new User();
                        $user->username = $firstMemberMatric;
                        $user->fullname = $firstMemberName;
                        $user->matric = $firstMemberMatric;
                        // Use matric@dummy.com as email
                        $user->email = strtolower($firstMemberMatric) . '@dummy.com';
                        $user->is_student = 1;
                        $user->is_internal = 1;
                        $user->status = User::STATUS_ACTIVE;
                        $user->setPassword($firstMemberMatric); // Use matric as password
                        $user->generateAuthKey();

                        if(!$user->save(false)){
                            throw new \RuntimeException('Failed to create user account for matric: ' . $firstMemberMatric . '. Database error.');
                        }
                    }

                    $programSubValue = isset($firstRow['program_sub']) && $firstRow['program_sub'] !== '' ? (int)$firstRow['program_sub'] : null;
                    $existingRegistrationQuery = ProgramRegistration::find()->where([
                        'user_id' => $user->id,
                        'program_id' => (int)$program->id,
                    ]);
                    if($program->has_sub == 1){
                        $existingRegistrationQuery->andWhere(['program_sub' => $programSubValue]);
                    }
                    $existingRegistration = $existingRegistrationQuery->one();
                    if($existingRegistration){
                        $skipped++;
                        continue;
                    }

                    // Create program registration
                    $registration = new ProgramRegistration();
                    $registration->user_id = $user->id;
                    $registration->program_id = (int)$program->id;
                    $registration->program_sub = $programSubValue;
                    $registration->status = ProgramRegistration::STATUS_REGISTERED;
                    $registration->created_at = time();
                    $registration->updated_at = time();
                    $registration->submitted_at = new Expression('NOW()');

                    // Set registration fields from the first row
                    foreach(array_keys($importableFields) as $fieldName){
                        if(!array_key_exists($fieldName, $firstRow)){
                            continue;
                        }

                        $value = $firstRow[$fieldName];
                        $registration->$fieldName = ($value === '') ? null : $value;
                    }

                    // Set group_name from the group
                    $registration->group_name = $groupName;

                    if(!$registration->save(false)){
                        throw new \RuntimeException('Failed to save registration for group: ' . $groupName);
                    }

                    // Create participant role if not already present
                    $existingRole = UserRole::find()->where(['user_id' => $user->id, 'role_name' => 'participant'])->one();
                    if(!$existingRole){
                        $participantRole = new UserRole();
                        $participantRole->user_id = $user->id;
                        $participantRole->role_name = 'participant';
                        $participantRole->status = 10;
                        $participantRole->request_at = new Expression('NOW()');
                        $participantRole->approve_at = new Expression('NOW()');
                        if(!$participantRole->save(false)){
                            throw new \RuntimeException('Failed to create participant role for user: ' . $firstMemberMatric);
                        }
                    }

                    // Create member records for all members in the group
                    foreach($groupRows as $memberRow){
                        $memberMatric = isset($memberRow['member_matrics']) ? trim((string)$memberRow['member_matrics']) : '';
                        $memberName = isset($memberRow['member_names']) ? trim((string)$memberRow['member_names']) : '';

                        if($memberName === ''){
                            continue; // Skip empty member names
                        }

                        $member = new Member();
                        $member->program_reg_id = $registration->id;
                        $member->member_name = $memberName;
                        $member->member_matric = $memberMatric;
                        $member->save(false);
                    }

                    $created++;
                }

                $transaction->commit();
                $msg = $created . ' group registration(s) imported successfully.';
                if($skipped > 0){
                    $msg .= ' ' . $skipped . ' group(s) skipped (already registered).';
                }
                Yii::$app->session->addFlash('success', $msg);
                return $this->refresh();
            }catch(\Throwable $e){
                $transaction->rollBack();
                Yii::$app->session->addFlash('error', $e->getMessage());
            }
        }

        // Get available program subs for reference
        $subQuery = ProgramSub::find()->where(['program_id' => $program->id]);
        $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
        if($subTable && $subTable->getColumn('is_active')){
            $subQuery->andWhere(['is_active' => 1]);
        }
        $availableProgramSubs = $subQuery->orderBy('id')->all();

        return $this->render('import-participants', [
            'role' => $role,
            'program' => $program,
            'managerSub' => $managerSub,
            'importableFields' => $importableFields,
            'unsupportedFields' => $unsupportedFields,
            'extraColumns' => $extraColumns,
            'sampleHeaders' => $sampleHeaders,
            'sampleRows' => $sampleRows,
            'availableProgramSubs' => $availableProgramSubs,
        ]);
    }

    protected function buildDashboardStats($program, $programSub = null)
    {
        $programId = (int)$program->id;
        $subId = $programSub ? (int)$programSub->id : null;

        $registrationQuery = ProgramRegistration::find()->where(['program_id' => $programId]);
        if($subId){
            $registrationQuery->andWhere(['program_sub' => $subId]);
        }

        $registrationTotal = (clone $registrationQuery)->count();
        $registeredTotal = (clone $registrationQuery)->andWhere(['status' => ProgramRegistration::STATUS_REGISTERED])->count();
        $completeTotal = (clone $registrationQuery)->andWhere(['status' => ProgramRegistration::STATUS_COMPLETE])->count();

        $assignmentQuery = JuryAssign::find()->alias('j')
            ->innerJoin('program_reg r', 'r.id = j.reg_id')
            ->where(['r.program_id' => $programId]);
        if($subId){
            $assignmentQuery->andWhere(['r.program_sub' => $subId]);
        }

        $assignmentTotal = (clone $assignmentQuery)->count();
        $assignmentComplete = (clone $assignmentQuery)->andWhere(['j.status' => 20])->count();

        $rubricQuery = ProgramRubric::find()->where(['program_id' => $programId]);
        if($subId){
            $rubricQuery->andWhere(['program_sub' => $subId]);
        }else{
            $rubricQuery->andWhere(['program_sub' => null]);
        }
        $rubricCount = (clone $rubricQuery)->count();

        $achievementCount = ProgramAchievement::find()->where(['program_id' => $programId])->count();

        $awardedQuery = ParticipantAchieve::find()->alias('pa')
            ->innerJoin('program_reg r', 'r.id = pa.program_reg_id')
            ->where(['r.program_id' => $programId]);
        if($subId){
            $awardedQuery->andWhere(['r.program_sub' => $subId]);
        }
        $awardedCount = (clone $awardedQuery)->count();

        $fieldEnabledCount = ProgramRegField::find()
            ->where(['program_id' => $programId, 'is_enabled' => 1])
            ->count();
        $fieldRequiredCount = ProgramRegField::find()
            ->where(['program_id' => $programId, 'is_required' => 1])
            ->count();

        $memberQuery = Member::find()->alias('m')
            ->innerJoin('program_reg r', 'r.id = m.program_reg_id')
            ->where(['r.program_id' => $programId]);
        if($subId){
            $memberQuery->andWhere(['r.program_sub' => $subId]);
        }
        $memberTotal = (clone $memberQuery)->count();

        $juryAppQuery = JuryApplication::find()->where(['program_id' => $programId]);
        if($subId){
            $juryAppQuery->andWhere(['program_sub_id' => $subId]);
        }else{
            $juryAppQuery->andWhere(['program_sub_id' => null]);
        }
        $juryAppTotal = (clone $juryAppQuery)->count();
        $juryAppNew = (clone $juryAppQuery)->andWhere(['status' => 0])->count();
        $juryAppApproved = (clone $juryAppQuery)->andWhere(['status' => 10])->count();

        return [
            'registrations_total' => (int)$registrationTotal,
            'registrations_registered' => (int)$registeredTotal,
            'registrations_complete' => (int)$completeTotal,
            'members_total' => (int)$memberTotal,
            'assignments_total' => (int)$assignmentTotal,
            'assignments_complete' => (int)$assignmentComplete,
            'rubrics_count' => (int)$rubricCount,
            'achievements_count' => (int)$achievementCount,
            'awarded_count' => (int)$awardedCount,
            'jury_applications_total' => (int)$juryAppTotal,
            'jury_applications_new' => (int)$juryAppNew,
            'jury_applications_approved' => (int)$juryAppApproved,
            'fields_enabled_count' => (int)$fieldEnabledCount,
            'fields_required_count' => (int)$fieldRequiredCount,
            'registration_status' => ((int)$program->getAttribute('reg_closed') === 1) ? 'Closed' : 'Open',
            'date_start' => $program->date_start,
            'date_end' => $program->date_end,
        ];
    }

    public function actionManagerSession($id, $sub = null){
        $session = Yii::$app->session;
        //print_r($session->get('keep-data'));die();
        if(!Yii::$app->user->identity->isManager) return false;

        $role = UserRole::findOne(['program_id' => $id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager', 'program_sub' => $sub, 'status' => 10]);
        if(!$role){
            throw new ForbiddenHttpException('No access');
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

        if($role && $role->program){
        
            $searchModel = new ManagerSessionSearch();
            $searchModel->program_id = $role->program_id;
            $searchModel->program_sub = $sub;
            $dataProvider = $searchModel->search($this->request->queryParams);
    
            return $this->render('manager-session', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'role' => $role,
                'programSub' => $programSub
            ]);
        }

        
    }

    private function clearSession($session){
        $session->remove('keep-data');
        $session->remove('users');
        $session->remove('rubric_id');
        $session->remove('date_start');
        $session->remove('date_end');
        $session->remove('location');
        $session->remove('note');
        $session->remove('link');
        $session->remove('keep_open');
        $session->remove('keep_data');
    }

    public function actionManagerAnalysis($id, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;
        $role = UserRole::findOne(['program_id' => $id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager', 'program_sub' => $sub, 'status' => 10]);
        if(!$role){
            throw new ForbiddenHttpException('No access');
        }
        $programSub = null;
        $program = $role->program;
        $rubrics = $program->programRubrics;

        if($role->program->has_sub == 1){
            if($sub){
                $programSub = $role->programSub;
                $rubrics = $program->getProgramRubricsSub($sub)->all();
            }else{
                throw new NotFoundHttpException('Please provide sub program.');
            }
        }
        $firstRubric = null;
        if($rubrics){
            $firstRubric = $rubrics[0]->id;
        }
        $firstStage = null;
        $stages = $program->programStages;
            if($stages){
                $firstStage = $stages[0]->id;
            }
        

        if($role && $role->program){
            $model = new JuryAssign();
            //cari ada stage tak
            
        
            $searchModel = new ManagerAnalysisSearch();
            $searchModel->program_id = $role->program_id;
            $searchModel->program_sub = $sub;

            $searchModel->rubric = $firstRubric;
            $searchModel->stage = $firstStage;
            //TODO: set klu ada get request yg pilih lain
            
            $dataProvider = $searchModel->search($this->request->queryParams);

            $selectedRubric = Rubric::findOne($searchModel->rubric);
    
            return $this->render('manager-analysis', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'role' => $role,
                'model' => $model,
                'programSub' => $programSub,
                'rubrics' => $rubrics,
                'stages' =>$stages,
                'selectedRubric' => $selectedRubric
            ]);
        }

        
    }

    /**
     * Creates a new ProgramRegistration model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ProgramRegistration();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProgramRegistration model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProgramRegistration model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDeleteRegistration($id)
    {
        //kita delete member & mentor shj
        $model = $this->findModel($id);
        $program_id = $model->program_id;
        $program_sub = $model->program_sub;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            Member::deleteAll(['program_reg_id' => $id]);
            Mentor::deleteAll(['program_reg_id' => $id]);
            $model->delete();
            $transaction->commit();
            //$this->findModel($id)->delete();
            Yii::$app->session->addFlash('success', "Registration Deleted");
            //return $this->redirect(['index']);
        } catch(\yii\db\IntegrityException $e) {
            throw new \yii\web\ForbiddenHttpException('Could not delete this registration, other record related to it (jury or achievement)');
        }

        return $this->redirect(['manager', 'id' => $program_id, 'sub' => $program_sub]);

    }

    /**
     * Finds the ProgramRegistration model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ProgramRegistration the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProgramRegistration::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findUser($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist. (user)');
    }

    protected function findAssignment($id)
    {
        if (($model = JuryAssign::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    

    protected function findAssignmentByProgram($u, $p, $s = null)
    {
        $model = JuryAssign::find()->alias('a')
        ->joinWith(['registration r'])
        ->where(['a.user_id' => $u, 'r.program_id' => $p]);
        if($s){
            $model = $model->andWhere(['r.program_sub' => $s]);
        }
        $model = $model->one();

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findAchievement($id)
    {
        if (($model = ParticipantAchieve::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
