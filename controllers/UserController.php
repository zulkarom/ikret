<?php

namespace app\controllers;

use app\models\ChangePasswordForm;
use app\models\Committee;
use app\models\JuryApplication;
use app\models\JuryAssign;
use app\models\JuryProfile;
use app\models\JurySearch;
use app\models\MentorSearch;
use app\models\Program;
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
use yii\helpers\ArrayHelper;
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

    public function actionAdminManagers()
    {
        if(Yii::$app->user->isGuest || !Yii::$app->user->identity->isSuperadmin){
            throw new ForbiddenHttpException('You are not allowed to view this page.');
        }

        $rolesToShow = [
            'superadmin',
            'admin-registration',
            'admin-jury',
            'admin-certificate',
            'manager',
        ];

        $roles = UserRole::find()->alias('ur')
            ->joinWith(['user u', 'program p', 'programSub ps'])
            ->where([
                'ur.status' => 10,
                'ur.role_name' => $rolesToShow,
            ])
            ->orderBy([
                'ur.role_name' => SORT_ASC,
                'u.fullname' => SORT_ASC,
                'ur.id' => SORT_ASC,
            ])
            ->all();

        $groups = [];
        foreach($roles as $role){
            $key = (string)$role->role_name;
            if(!isset($groups[$key])){
                $groups[$key] = [];
            }
            $groups[$key][] = $role;
        }

        return $this->render('admin-managers', [
            'groups' => $groups,
        ]);
    }

    public function actionRoleList()
    {
        if(Yii::$app->user->isGuest || !Yii::$app->user->identity->isSuperadmin){
            throw new ForbiddenHttpException('You are not allowed to view this page.');
        }

        $roles = UserRole::listRoles();

        $descriptions = [
            'participant' => 'Access to attendance scanner page and participant certificates (when released/published).',
            'manager' => 'Manage program registrations and manager dashboards for assigned program/sub.',
            'jury' => 'Access to jury assignments and (when released/published) jury certificates.',
            'committee' => 'Access to committee menu and (when released/published) committee certificates.',
            'mentor' => 'Access to mentor pages (mentees and related mentor features).',
            'admin-jury' => 'Admin access for jury management: jury list, profiles, applications, judging sessions and achievement config.',
            'admin-registration' => 'Admin access for registration management: registrations, program/sub registration status, sessions and attendance list.',
            'admin-certificate' => 'Admin access for certificate configuration and certificate-related listings (participants/juries/committees).',
            'superadmin' => 'Full administrative access: user & access management, configuration, reports, and all admin features.',
        ];

        return $this->render('role-list', [
            'roles' => $roles,
            'descriptions' => $descriptions,
        ]);
    }

    public function actionJury(){
        if(!Yii::$app->user->identity->isManager && !Yii::$app->user->identity->isAdminJury) return false;

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
        $roleModel = new UserRole(['user_id' => $model->id, 'status' => 10]);

        return $this->render('view', [
            'model' => $model,
            'roleModel' => $roleModel,
            'roleOptions' => $this->assignableRoleOptions(),
            'programOptions' => $this->programOptions(),
            'programSubOptions' => $this->programSubOptions(),
            'programSubOptionAttributes' => $this->programSubOptionAttributes(),
            'committeeOptions' => $this->committeeOptions(),
            'deleteBlockers' => $this->findUserDeleteBlockers($model->id),
            'userRoles' => $this->findUserRoles($model->id),
            'committeeRoles' => $this->findUserCommitteeRoles($model->id),
            'juryProfile' => $this->findUserJuryProfile($model->id),
            'juryApplications' => $this->findUserJuryApplications($model->id),
            'juryAssignments' => $this->findUserJuryAssignments($model->id),
            'relatedData' => $this->findUserRelatedData($model->id),
        ]);
    }

    public function actionAssignRole($id)
    {
        if(!Yii::$app->user->identity->isSuperadmin) return false;

        if(!$this->request->isPost){
            throw new \yii\web\MethodNotAllowedHttpException('Method Not Allowed.');
        }

        $user = $this->findModel($id);
        $role = new UserRole(['user_id' => $user->id]);

        if(!$role->load($this->request->post())){
            Yii::$app->session->addFlash('error', "Invalid role data");
            return $this->redirect(['view', 'id' => $user->id]);
        }

        $allowedRoles = $this->assignableRoleOptions();
        if(!array_key_exists($role->role_name, $allowedRoles)){
            Yii::$app->session->addFlash('error', "Invalid role selected");
            return $this->redirect(['view', 'id' => $user->id]);
        }

        $role->user_id = $user->id;
        $role->status = 10;
        $role->approve_at = new Expression('NOW()');
        $role->request_at = new Expression('NOW()');

        if($role->program_sub == -1 || $role->program_sub === ''){
            $role->program_sub = null;
        }
        if($role->program_id === ''){
            $role->program_id = null;
        }
        if($role->committee_id === ''){
            $role->committee_id = null;
        }

        if(!$this->prepareAssignedRole($role)){
            return $this->redirect(['view', 'id' => $user->id]);
        }

        if($this->roleAlreadyAssigned($role)){
            Yii::$app->session->addFlash('warning', "This role access already exists");
            return $this->redirect(['view', 'id' => $user->id]);
        }

        if($role->save()){
            Yii::$app->session->addFlash('success', $role->roleText . " access assigned");
        }else{
            $role->flashError();
        }

        return $this->redirect(['view', 'id' => $user->id]);
    }

    public function actionAssignManager($id)
    {
        if(!Yii::$app->user->identity->isAdmin) return false;

        $model = $this->findModel($id);
        $assignments = $this->managerAssignmentOptions();
        $existingKeys = $this->existingManagerAssignmentKeys($model->id);

        if($this->request->isPost){
            $selected = (array)$this->request->post('assignments', []);
            $created = 0;

            foreach($selected as $key){
                if(array_key_exists($key, $existingKeys)){
                    continue;
                }

                $parts = explode(':', $key);
                $programId = isset($parts[0]) ? (int)$parts[0] : 0;
                $programSub = isset($parts[1]) && (int)$parts[1] > 0 ? (int)$parts[1] : null;

                if(!$this->isValidManagerAssignment($assignments, $programId, $programSub)){
                    continue;
                }

                $role = new UserRole();
                $role->user_id = $model->id;
                $role->role_name = 'manager';
                $role->program_id = $programId;
                $role->program_sub = $programSub;
                $role->status = 10;
                $role->approve_at = new Expression('NOW()');

                if($role->save()){
                    $created++;
                    $existingKeys[$key] = true;
                }else{
                    $role->flashError();
                }
            }

            if($created > 0){
                Yii::$app->session->addFlash('success', $created . " manager access assigned");
            }else{
                Yii::$app->session->addFlash('info', "No new manager access assigned");
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('assign-manager', [
            'model' => $model,
            'assignments' => $assignments,
            'existingKeys' => $existingKeys,
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

    protected function assignableRoleOptions()
    {
        return UserRole::listRoles();
    }

    protected function programOptions()
    {
        return ArrayHelper::map(
            Program::find()->orderBy(['program_name' => SORT_ASC])->all(),
            'id',
            'program_name'
        );
    }

    protected function programSubOptions()
    {
        $subs = ProgramSub::find()
            ->with('program')
            ->orderBy(['program_id' => SORT_ASC, 'sub_name' => SORT_ASC])
            ->all();

        $options = [];
        foreach($subs as $sub){
            $programName = $sub->program ? $sub->program->program_name : 'Program ' . $sub->program_id;
            $options[$sub->id] = $programName . ' / ' . $sub->sub_name;
        }

        return $options;
    }

    protected function programSubOptionAttributes()
    {
        $subs = ProgramSub::find()->select(['id', 'program_id'])->all();
        $options = [];
        foreach($subs as $sub){
            $options[$sub->id] = ['data-program' => (string)$sub->program_id];
        }

        return $options;
    }

    protected function committeeOptions()
    {
        return ArrayHelper::map(
            Committee::find()->orderBy(['com_name_en' => SORT_ASC])->all(),
            'id',
            'com_name_en'
        );
    }

    protected function prepareAssignedRole(UserRole $role)
    {
        if($role->role_name === 'manager'){
            if(!$role->program_id){
                Yii::$app->session->addFlash('error', "Please select a program for manager access");
                return false;
            }

            $program = Program::findOne((int)$role->program_id);
            if(!$program){
                Yii::$app->session->addFlash('error', "Program not found");
                return false;
            }

            if((int)$program->has_sub === 1){
                if(!$role->program_sub){
                    Yii::$app->session->addFlash('error', "Please select a competition for this manager access");
                    return false;
                }
                $programSub = ProgramSub::findOne(['id' => (int)$role->program_sub, 'program_id' => (int)$program->id]);
                if(!$programSub){
                    Yii::$app->session->addFlash('error', "Competition does not belong to the selected program");
                    return false;
                }
            }else{
                $role->program_sub = null;
            }

            $role->committee_id = null;
            return true;
        }

        if($role->role_name === 'committee'){
            if(!$role->committee_id){
                Yii::$app->session->addFlash('error', "Please select a committee");
                return false;
            }
            $role->program_id = null;
            $role->program_sub = null;
            return true;
        }

        $role->program_id = null;
        $role->program_sub = null;
        $role->committee_id = null;
        $role->is_leader = null;

        return true;
    }

    protected function roleAlreadyAssigned(UserRole $role)
    {
        $query = UserRole::find()->where([
            'user_id' => $role->user_id,
            'role_name' => $role->role_name,
            'status' => 10,
        ]);

        if($role->role_name === 'manager'){
            $query->andWhere([
                'program_id' => $role->program_id,
                'program_sub' => $role->program_sub,
            ]);
        }else if($role->role_name === 'committee'){
            $query->andWhere(['committee_id' => $role->committee_id]);
        }

        return $query->exists();
    }

    protected function managerAssignmentOptions()
    {
        $query = Program::find()->with('programSubs')->orderBy(['id' => SORT_ASC]);
        $programTable = Yii::$app->db->schema->getTableSchema(Program::tableName());
        if($programTable && $programTable->getColumn('is_active')){
            $query->andWhere(['is_active' => 1]);
        }

        $programs = [];
        foreach($query->all() as $program){
            $items = [];
            $hasSub = $programTable && $programTable->getColumn('has_sub') && (int)$program->has_sub === 1;

            if($hasSub){
                $subs = $program->programSubs;
                foreach($subs as $sub){
                    $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
                    if($subTable && $subTable->getColumn('is_active') && (int)$sub->is_active !== 1){
                        continue;
                    }

                    $items[] = [
                        'key' => $program->id . ':' . $sub->id,
                        'program_id' => (int)$program->id,
                        'program_sub' => (int)$sub->id,
                        'label' => $sub->sub_name,
                    ];
                }
            }else{
                $items[] = [
                    'key' => $program->id . ':0',
                    'program_id' => (int)$program->id,
                    'program_sub' => null,
                    'label' => 'All / N/A',
                ];
            }

            $programs[] = [
                'id' => (int)$program->id,
                'name' => $program->program_name,
                'items' => $items,
            ];
        }

        return $programs;
    }

    protected function existingManagerAssignmentKeys($id)
    {
        $roles = UserRole::find()
            ->where(['user_id' => $id, 'role_name' => 'manager'])
            ->all();
        $keys = [];

        foreach($roles as $role){
            $keys[$role->program_id . ':' . ((int)$role->program_sub > 0 ? $role->program_sub : 0)] = true;
        }

        return $keys;
    }

    protected function isValidManagerAssignment($assignments, $programId, $programSub)
    {
        $key = $programId . ':' . ($programSub ? $programSub : 0);
        foreach($assignments as $program){
            foreach($program['items'] as $item){
                if($item['key'] === $key){
                    return true;
                }
            }
        }

        return false;
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
        $excludedTables = [
            'user_role',
            'program_reg_jury',
        ];
        $related = [];

        foreach($schema->getTableNames('', true) as $tableName){
            if($tableName === $userTable || in_array($tableName, $excludedTables)){
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

    public function actionRemoveSelectedRoles($id)
    {
        if(!Yii::$app->user->identity->isAdmin) return false;

        if(!$this->request->isPost){
            throw new \yii\web\MethodNotAllowedHttpException('Method Not Allowed.');
        }

        $model = $this->findModel($id);
        $roleIds = (array)$this->request->post('role_ids', []);
        $roleIds = array_filter(array_map('intval', $roleIds));

        if(!$roleIds){
            Yii::$app->session->addFlash('warning', "Please select at least one role to remove");
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $deleted = UserRole::deleteAll(['and', ['user_id' => $model->id], ['id' => $roleIds]]);
        Yii::$app->session->addFlash('success', $deleted . " user role access removed");

        return $this->redirect(['view', 'id' => $model->id]);
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
