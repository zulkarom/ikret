<?php

namespace app\controllers;

use app\models\JuryAssign;
use app\models\JuryAssignSearch;
use app\models\JuryResultSearch;
use app\models\ManagerAnalysisSearch;
use app\models\ParticipantAchieve;
use app\models\ProgramAchievement;
use app\models\ProgramRegistration;
use app\models\ProgramRegistrationManagerSearch;
use app\models\ProgramRegistrationSearch;
use app\models\ProgramSub;
use app\models\Rubric;
use app\models\RubricAnswer;
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
    public function actionJuryDelete($id){
        if(!Yii::$app->user->identity->isManager) return false;
        $assign = $this->findAssignment($id);
        $reg = $assign->registration;

        $role = UserRole::findOne(['program_id' => $reg->program_id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager']);

        if($role && $role->program){
            
            if($assign->status == 0){
                if($assign->delete()){
                    Yii::$app->session->addFlash('success', "Assignment Deleted");
                    
                }
            }
        }

        return $this->redirect(['manager', 'id' => $reg->program_id]);
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

        if ($assign->status <= 10 && $this->request->isPost && $model->load($this->request->post())) {
            
            $action = $this->request->post('action');
            $model->updated_at = new Expression('NOW()');
            if($action == 'submit'){
                
                if($model->isComplete){
                    $model->submitted_at = new Expression('NOW()');
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if($model->save()){
                            $assign->status = 20;
                            //put score in jury assign
                            $assign->score = $model->scoreValue;
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
                        
                    }
                    catch (\Exception $e) 
                    {
                        $transaction->rollBack();
                        Yii::$app->session->addFlash('error', $e->getMessage());
                    }
                    
                }else{
                    Yii::$app->session->addFlash('error', "You need to complete all first before submitting.");
                }
            }else{
                if($model->save()){
                    Yii::$app->session->addFlash('success', "Data Updated");
                    return $this->refresh();
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

    public function actionManagerFlag($id,$flag){
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
        return $this->redirect(['manager', 'id' => $reg->program_id]);
    }

    public function actionJuryResult($id, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;

        $role = UserRole::findOne(['program_id' => $id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager']);
        $programSub = null;
        $program = $role->program;

        $programSub = null;
        $program = $role->program;

        if($role->program->has_sub == 1){
            if($sub){
                $programSub = $role->programSub;
            }else{
                throw new NotFoundHttpException('Please provide sub program.');
            }
        }

        $searchModel = new JuryResultSearch();
        $searchModel->program_id = $id;
        $searchModel->program_sub = $sub;
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('jury-result', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'program' => $program,
            'programSub' => $programSub,
        ]);
    }

    public function actionManager($id, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;
        $role = UserRole::findOne(['program_id' => $id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager']);
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
            //cari ada stage tak
            $stages = $program->programStages;
            if($stages){
                $model->scenario = 'stage';
            }
            $model->stage = 0;
                if ($this->request->isPost && $model->load($this->request->post())) {
                    //echo '<pre>';
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
                                        Yii::$app->session->addFlash('error', $name . ' had been assigned to ' . $peserta);
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

    public function actionManagerAnalysis($id, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;
        $role = UserRole::findOne(['program_id' => $id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager']);
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

    protected function findAssignment($id)
    {
        if (($model = JuryAssign::findOne(['id' => $id])) !== null) {
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
