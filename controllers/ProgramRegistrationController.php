<?php

namespace app\controllers;

use app\models\CertificateJury;
use app\models\CertificateTemplate;
use app\models\JuryAssign;
use app\models\JuryAssignSearch;
use app\models\JuryResultSearch;
use app\models\ManagerAnalysisSearch;
use app\models\Member;
use app\models\Mentor;
use app\models\ParticipantAchieve;
use app\models\ProgramAchievement;
use app\models\ProgramRegistration;
use app\models\ProgramRegistrationManagerSearch;
use app\models\ProgramRegistrationSearch;
use app\models\ProgramSub;
use app\models\Rubric;
use app\models\RubricAnswer;
use app\models\Setting;
use app\models\User;
use app\models\UserRole;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
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
            ->select('a.*, r.program_id, r.program_sub')
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

        $role = UserRole::findOne(['program_id' => $reg->program_id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager', 'program_id' => $p]);

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
        $role = UserRole::findOne(['program_id' => $model->program_id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager', 'program_sub' => $sub]);

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
        $role = UserRole::findOne(['program_id' => $model->program_id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager']);

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

        $role = UserRole::findOne(['program_id' => $id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager', 'program_sub' => $sub]);
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

        $role = UserRole::findOne(['program_id' => $id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager', 'program_sub' => $sub]);
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
            
            $model = new JuryAssign();
            $model->scenario = 'assign';
            if ($session->has('keep-data') && $session->get('keep-data') == 1){
                $model->users = $session->get('users');
                $model->rubric_id = $session->get('rubric_id');
                $model->date_start = $session->get('date_start');
                $model->date_end = $session->get('date_end');
                $model->location = $session->get('location');
                $model->note = $session->get('note');
                $model->link = $session->get('link');
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
                        $session->set('date_start', $model->date_start);
                        $session->set('date_end', $model->date_end);
                        $session->set('location', $model->location);
                        $session->set('note', $model->note);
                        $session->set('link', $model->link);
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
                'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
                'role' => $role,
                'model' => $model,
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
        $role = UserRole::findOne(['program_id' => $id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager', 'program_sub' => $sub]);
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
                'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
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
