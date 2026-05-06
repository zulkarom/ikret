<?php

namespace app\controllers;

use app\models\ChangePasswordForm;
use app\models\JuryApplication;
use app\models\JuryAssign;
use app\models\JuryProfile;
use app\models\JurySearch;
use app\models\MentorSearch;
use app\models\ProgramSub;
use app\models\RegisterForm;
use Yii;
use yii\web\Controller;
use app\models\User;
use app\models\UserRole;
use app\models\UserSearch;
use yii\db\Expression;
use yii\db\IntegrityException;
use yii\db\Query;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class UserController extends Controller
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
                    ]
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
        $model = User::findOne(Yii::$app->user->identity->id);
        if ($model->load(Yii::$app->request->post())) {
            $model->fullname = strtoupper($model->fullname);

            if($model->save()){
                Yii::$app->session->addFlash('success', "Profile Updated");
                return $this->refresh();
            }

        }

        return $this->render('index',[
            'model' => $model
        ]);
    }



    public function actionAll(){
        if(Yii::$app->user->identity->isAdmin or Yii::$app->user->identity->isManager){
            $searchModel = new UserSearch();
            $dataProvider = $searchModel->search($this->request->queryParams);
    
            return $this->render('all', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
            ]);
        }
        
    }

    public function actionJury(){
        if(!Yii::$app->user->identity->isManager) return false;

        $userRole = new UserRole();

        if ($this->request->isPost && $userRole->load($this->request->post())) {
            //verify dia dah add ke belum
            $ada = UserRole::findOne(['user_id' => $userRole->user_id, 'role_name' => 'jury']);
            if($ada){
                Yii::$app->session->addFlash('error', "The user already a jury");
            }else{
                $userRole->status = 10;
                $userRole->role_name = 'jury';
                $userRole->approve_at = new Expression('NOW()');
                if($userRole->save()){
                    Yii::$app->session->addFlash('success', "Jury Added");
                    return $this->refresh();
                }else{
                    $userRole->flashError();
                }
            }
            

        }
        $userRole->user_id = null;

        $newUser = new RegisterForm(['self_register' => false, 'button_label' => 'Register & Add Jury']);

        if ($this->request->isPost && $newUser->load($this->request->post())) {
            if($newUser->signup()){
                Yii::$app->session->addFlash('success', "The new registered user has been added with role selected");
                return $this->refresh();
            }
            //set password
        }
        

        $searchModel = new JurySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('jury', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'userRole' => $userRole,
            'newUser' => $newUser
        ]);
    }

    public function actionMentor(){
        if(!Yii::$app->user->identity->isManager) return false;
        $searchModel = new MentorSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('mentor', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUserListJson($q = null, $id = null) {
        
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

    public function actionUpdate($id){
        if(!Yii::$app->user->identity->isAdmin) return false;

        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {

            if($model->passwordRaw){
                $model->setPassword($model->passwordRaw);
            }

            if($model->save()){
                Yii::$app->session->addFlash('success', "Data Updated");
                return $this->refresh();
            }
            
        }

        return $this->render('update', [
            'model' => $model,
            'deleteBlockers' => $this->findUserDeleteBlockers($model->id),
        ]);
    }

    public function actionView($id)
    {
        if(!Yii::$app->user->identity->isAdmin && !Yii::$app->user->identity->isManager) return false;

        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
            'deleteBlockers' => $this->findUserDeleteBlockers($model->id),
            'userRoles' => $this->findUserRoles($model->id),
            'committeeRoles' => $this->findUserCommitteeRoles($model->id),
            'juryProfile' => $this->findUserJuryProfile($model->id),
            'juryApplications' => $this->findUserJuryApplications($model->id),
            'juryAssignments' => $this->findUserJuryAssignments($model->id),
            'relatedData' => $this->findUserRelatedData($model->id),
        ]);
    }

    public function actionDelete($id)
    {
        if(!Yii::$app->user->identity->isAdmin) return false;

        if(!$this->request->isPost){
            throw new \yii\web\MethodNotAllowedHttpException('Method Not Allowed.');
        }

        if((int)$id === (int)Yii::$app->user->identity->id){
            Yii::$app->session->addFlash('error', "You cannot delete your own account");
            return $this->redirect(['update', 'id' => $id]);
        }

        $model = $this->findModel($id);
        $deleteBlockers = $this->findUserDeleteBlockers($model->id);

        if($deleteBlockers){
            Yii::$app->session->addFlash('error', "Could not delete this user because related records exist: " . implode(', ', $deleteBlockers));
            return $this->redirect(['update', 'id' => $id]);
        }

        try {
            if($model->delete()){
                Yii::$app->session->addFlash('success', "User Deleted");
                return $this->redirect(['all']);
            }
        } catch (IntegrityException $e) {
            Yii::$app->session->addFlash('error', "Could not delete this user because other records are related to it");
            return $this->redirect(['update', 'id' => $id]);
        }

        Yii::$app->session->addFlash('error', "Could not delete this user");
        return $this->redirect(['update', 'id' => $id]);
    }

    protected function findUserDeleteBlockers($id)
    {
        $schema = Yii::$app->db->schema;
        $userTable = $schema->getRawTableName(User::tableName());
        $labels = [
            'user_role' => 'User Role',
            'jury_profiles' => 'Jury Profile',
            'program_reg_jury' => 'Jury Assignment',
            'program_reg_mentor' => 'Mentor Assignment',
            'program_reg' => 'Program Registration',
            'questionnaire_ans' => 'Questionnaire Answer',
            'questionnaire_ans_post' => 'Post Questionnaire Answer',
            'session_attendance' => 'Session Attendance',
            'auth_assignment' => 'Auth Assignment',
        ];
        $blockers = [];

        foreach($schema->getTableNames('', true) as $tableName){
            if($tableName === $userTable){
                continue;
            }

            $table = $schema->getTableSchema($tableName);
            if(!$table || !$table->getColumn('user_id')){
                continue;
            }

            $count = (new Query())
                ->from($tableName)
                ->where(['user_id' => (string)$id])
                ->count();

            if((int)$count > 0){
                $label = array_key_exists($tableName, $labels) ? $labels[$tableName] : $tableName;
                $blockers[] = $label . ' (' . (int)$count . ')';
            }
        }

        return $blockers;
    }

    protected function findUserRoles($id)
    {
        return UserRole::find()
            ->with(['program', 'programSub', 'committee'])
            ->where(['user_id' => $id])
            ->orderBy(['id' => SORT_DESC])
            ->all();
    }

    protected function findUserCommitteeRoles($id)
    {
        return UserRole::find()
            ->with(['committee'])
            ->where(['user_id' => $id, 'role_name' => 'committee'])
            ->andWhere(['is not', 'committee_id', null])
            ->orderBy(['is_leader' => SORT_ASC, 'id' => SORT_DESC])
            ->all();
    }

    protected function findUserJuryProfile($id)
    {
        return JuryProfile::find()
            ->where(['user_id' => $id])
            ->one();
    }

    protected function findUserJuryApplications($id)
    {
        $profile = $this->findUserJuryProfile($id);
        if(!$profile){
            return [];
        }

        return JuryApplication::find()
            ->with(['program', 'programSub', 'judgingSession'])
            ->where(['jury_profile_id' => $profile->id])
            ->orderBy(['id' => SORT_DESC])
            ->all();
    }

    protected function findUserJuryAssignments($id)
    {
        return JuryAssign::find()
            ->with(['registration.program', 'registration.programSub', 'rubric', 'judgingSession'])
            ->where(['user_id' => $id])
            ->orderBy(['id' => SORT_DESC])
            ->all();
    }

    protected function findUserRelatedData($id)
    {
        $schema = Yii::$app->db->schema;
        $userTable = $schema->getRawTableName(User::tableName());
        $labels = [
            'user_role' => 'User Role',
            'jury_profiles' => 'Jury Profile',
            'program_reg_jury' => 'Jury Assignment',
            'program_reg_mentor' => 'Mentor Assignment',
            'program_reg' => 'Program Registration',
            'questionnaire_ans' => 'Questionnaire Answer',
            'questionnaire_ans_post' => 'Post Questionnaire Answer',
            'session_attendance' => 'Session Attendance',
            'auth_assignment' => 'Auth Assignment',
        ];
        $preferredColumns = [
            'id', 'role_name', 'status', 'program_id', 'program_sub', 'program_sub_id',
            'committee_id', 'reg_id', 'program_reg_id', 'rubric_id', 'judging_session_id',
            'stage', 'score', 'group_name', 'project_name', 'session_id', 'scanned_at',
            'fullname', 'category', 'institution', 'created_at', 'updated_at', 'submitted_at',
        ];
        $related = [];

        foreach($schema->getTableNames('', true) as $tableName){
            if($tableName === $userTable){
                continue;
            }

            $table = $schema->getTableSchema($tableName);
            if(!$table || !$table->getColumn('user_id')){
                continue;
            }

            $count = (new Query())
                ->from($tableName)
                ->where(['user_id' => (string)$id])
                ->count();

            if((int)$count === 0){
                continue;
            }

            $columns = [];
            foreach($preferredColumns as $column){
                if($table->getColumn($column)){
                    $columns[] = $column;
                }
            }

            if(!$columns){
                foreach(array_keys($table->columns) as $column){
                    if($column !== 'user_id'){
                        $columns[] = $column;
                    }
                    if(count($columns) >= 8){
                        break;
                    }
                }
            }

            $query = (new Query())
                ->select($columns)
                ->from($tableName)
                ->where(['user_id' => (string)$id])
                ->limit(20);

            if(!empty($table->primaryKey)){
                $query->orderBy([$table->primaryKey[0] => SORT_DESC]);
            }

            $related[] = [
                'label' => array_key_exists($tableName, $labels) ? $labels[$tableName] : $tableName,
                'table' => $tableName,
                'count' => (int)$count,
                'columns' => $columns,
                'rows' => $query->all(),
            ];
        }

        return $related;
    }

    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionRemoveRole($id){
        $role = $this->findUserRole($id);
        if($role->delete()){
            Yii::$app->session->addFlash('info', "User role deleted");
            return $this->redirect(['add-role']);
        }

    }

    protected function findUserRole($id)
    {
        if (($model = UserRole::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionSubProgramOptions($program){
        $query = ProgramSub::find()->where(['program_id' => $program]);
        $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
        if($subTable && $subTable->getColumn('is_active')){
            $query->andWhere(['is_active' => 1]);
        }
        $list = $query->all();
        $html = '<option value="-1">N/A</option>';
        if($list){
            $html = '<option>Select Competition</option>';
            foreach($list as $sub){
                $html .= '<option value="'.$sub->id .'">'.$sub->sub_name.'</option>';
            }
        }
        return $html;
    }

    public function actionAddRole(){
        $model = new UserRole();
        $model->user_id = Yii::$app->user->identity->id;
        $roles = UserRole::find()
        ->where(['user_id' => Yii::$app->user->identity->id])
        ->all();

        if ($model->load(Yii::$app->request->post())) {
            $model->status = 0;
            $model->request_at = new Expression('NOW()');

            if($model->role_name == 'participant'){
                $model->status = 10;
                if(UserRole::findOne(['user_id' => Yii::$app->user->identity->id, 'role_name' => 'participant'])){
                    Yii::$app->session->addFlash('error', "Duplicate request!");
                    return $this->refresh();
                }
            }

            if($model->role_name == 'jury'){
                $model->status = 10;
                if(UserRole::findOne(['user_id' => Yii::$app->user->identity->id, 'role_name' => 'jury'])){
                    Yii::$app->session->addFlash('error', "Duplicate request!");
                    return $this->refresh();
                }
            }

            if($model->role_name == 'mentor'){
                $model->status = 10;
                if(UserRole::findOne(['user_id' => Yii::$app->user->identity->id, 'role_name' => 'mentor'])){
                    Yii::$app->session->addFlash('error', "Duplicate request!");
                    return $this->refresh();
                }
            }

            if($model->role_name == 'manager'){
                if(!$model->program_id){
                    Yii::$app->session->addFlash('error', "Please select a program");
                    return $this->refresh();
                }else{
                    if($model->program->has_sub == 1 and !$model->program_sub){
                        Yii::$app->session->addFlash('error', "Please select a competition");
                        return $this->refresh();
                    }
                    if(UserRole::findOne(['user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager', 'program_id' => $model->program_id, 'program_sub' => $model->program_sub])){
                        Yii::$app->session->addFlash('error', "Duplicate request!");
                        return $this->refresh();
                    }
                }
            }

            if($model->role_name == 'committee'){
                if(!$model->committee_id){
                    Yii::$app->session->addFlash('error', "Please select a committee");
                    return $this->refresh();
                }else{
                    if(UserRole::findOne(['user_id' => Yii::$app->user->identity->id, 'role_name' => 'committee', 'committee_id' => $model->committee_id])){
                        Yii::$app->session->addFlash('error', "Duplicate request!");
                        return $this->refresh();
                    }

                    if($model->committee->is_jawatankuasa == 1){
                        if(!$model->is_leader){
                            Yii::$app->session->addFlash('error', "Please choose whether you are a leader or a member");
                            return $this->refresh();
                        }
                    }
                }
            }

            if($model->program_sub == -1){
                $model->program_sub = null; 
            }

            if($model->save()){
                Yii::$app->session->addFlash('success', "Role Added");
                return $this->refresh();
            }else{
                $model->flashError();
            }

        }

        return $this->render('add-role',[
            'model' => $model,
            'roles' => $roles
        ]);
    }

    public function actionChangePassword()
	{
		$id = Yii::$app->user->id;
	 
		try {
			$model = new ChangePasswordForm($id);
		} catch (\InvalidArgumentException $e) {
			throw new \yii\web\BadRequestHttpException($e->getMessage());
		}
	 
		if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->changePassword()) {
			Yii::$app->session->setFlash('success', 'Password Changed!');
		}
	 
		return $this->render('change-password', [
			'model' => $model,
		]);
	}

    public function actionLoginAs($id){
		$user = User::findIdentity($id);
		$original = Yii::$app->user->identity->id;
		if(Yii::$app->user->login($user)){
			$session = Yii::$app->session;
			$session->set('or-usr', $original);
			return $this->redirect(['site/index']);
		}
	}

    public function actionReturnRole(){
		$session = Yii::$app->session;
		if ($session->has('or-usr')){
			$id = $session->get('or-usr');
			$user = User::findIdentity($id);
				if(Yii::$app->user->login($user)){
					$session->remove('or-usr');
					return $this->redirect(['site/index']);
				}
		}else{
			throw new NotFoundHttpException('The requested page does not exist..');
		}
	}

    public function actionModifyStudentData294(){
        die();
        if(!Yii::$app->user->identity->isAdmin) return false;

        $list = User::find()->all();
        foreach ($list as $user) {
            $matric = $user->matric;
            if($matric){ // cari first char
                $c = substr($matric, 0, 1);
                if($c == "A"){
                    $user->is_student = 1;
                    $user->save();
                    echo $matric . ' = student <br />';
                }else if($c == '0'){
                    $user->is_student = 0;
                    $user->save();
                    echo $matric . ' = staff <br />';
                }
            }
        }
        exit;
    }


}
