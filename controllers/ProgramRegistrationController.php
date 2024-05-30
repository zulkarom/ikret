<?php

namespace app\controllers;

use app\models\JuryAssign;
use app\models\JuryAssignSearch;
use app\models\ProgramRegistration;
use app\models\ProgramRegistrationManagerSearch;
use app\models\ProgramRegistrationSearch;
use app\models\RubricAnswer;
use app\models\User;
use app\models\UserRole;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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
            'model' => $model
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

    public function actionManagerView($id){

        return $this->render('manager-view', [
            'model' => $this->findModel($id),
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

    public function actionManager($id){
        if(!Yii::$app->user->identity->isManager) return false;
        $role = UserRole::findOne(['program_id' => $id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager']);

        if($role && $role->program){

            $model = new JuryAssign();

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
                                    $ada = JuryAssign::findOne(['user_id' => $u, 'reg_id' => $select]);
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
            $searchModel->programx_id = $role->program_id;
            $dataProvider = $searchModel->search($this->request->queryParams);
    
            return $this->render('manager', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'role' => $role,
                'model' => $model
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
}
