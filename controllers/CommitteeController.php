<?php

namespace app\controllers;

use app\models\CertificateCommittee;
use app\models\CertificateTemplate;
use app\models\Committee;
use app\models\CommitteeRequestSearch;
use app\models\CommitteeStudentSearch;
use app\models\Setting;
use app\models\User;
use app\models\UserRole;
use app\models\LetterPdf;
use app\models\RoleRequestSearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * CommitteeController handles committee requests, certificates, and committee CRUD.
 */
class CommitteeController extends Controller
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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists user role requests.
     *
     * @return string
     */
    public function actionRequest()
    {
        if(!Yii::$app->user->identity->isAdmin) return false;

        $searchModel = new RoleRequestSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);


        if (Yii::$app->request->post()) {
            $post = Yii::$app->request->post();

            if(isset($post['selection'])){

                $selection = $post['selection'];
                foreach($selection as $select){
                    
                    $kd = UserRole::findOne($select);
                    if($post['actiontype'] == 'approve'){
                        $kd->status = 10;
                    }
                    else{
                        $kd->status = 0;
                    }
                    if(!$kd->save()){
                        $kd->flashError();
                    }
                }
                Yii::$app->session->addFlash('success', "Data Updated");
                return $this->refresh();
            }
        }

        return $this->render('request', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionActionCommittee()
    {
        //if(!Yii::$app->user->identity->isAdmin) return false;
        //adakah dia boleh approve
        $qry = UserRole::find()->alias('a')
          ->joinWith(['committee c'])
          ->where(['a.user_id' => Yii::$app->user->identity->id, 
          'a.role_name' => 'committee', 
          'a.status' => 10,
          ]);

          if($qry->one()){
            $canApprove = UserRole::find()->alias('a')
            ->joinWith(['committee c'])
            ->where(['a.user_id' => Yii::$app->user->identity->id, 
            'a.role_name' => 'committee', 
            'a.status' => 10,
            ])->andWhere(['c.can_approve' => 1])->one();
            
            if($canApprove){

                $searchModel = new CommitteeRequestSearch(['canApprove' => true]);

            }else if($head = $qry->andWhere(['a.is_leader' => 1, 'c.is_jawatankuasa' => 1])->one()){

                $searchModel = new CommitteeRequestSearch(['isHead' => true, 'committee_id' => $head->committee_id]);

            }else{
                throw new ForbiddenHttpException('No access');
            }
    
            $dataProvider = $searchModel->search($this->request->queryParams);

            $searchStudent = new CommitteeStudentSearch();
            $dataProviderStudent = $searchStudent->search($this->request->queryParams);
            
            if (Yii::$app->request->post()) {
                $post = Yii::$app->request->post();
    
                if(isset($post['selection'])){
    
                    $selection = $post['selection'];
                    foreach($selection as $select){
                        
                        $kd = UserRole::findOne($select);
                        if($post['actiontype'] == 'approve'){
                            $kd->status = 10;
                        }
                        else{
                            $kd->status = 0;
                        }
                        if(!$kd->save()){
                            $kd->flashError();
                        }
                    }
                    Yii::$app->session->addFlash('success', "Data Updated");
                    return $this->refresh();
                }
            }
    
            return $this->render('request-com', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'dataProviderStudent' => $dataProviderStudent,
                'searchStudent' => $searchStudent,
            ]);
            
          }

        
    }

    /**
     * Displays a single Committee model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $this->ensureCanManageCommittee();

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionIndex()
    {
        $this->ensureCanManageCommittee();

        $dataProvider = new ActiveDataProvider([
            'query' => Committee::find()->orderBy([
                '(CASE WHEN committee_order IS NULL OR committee_order = 0 THEN 1 ELSE 0 END)' => SORT_ASC,
                'committee_order' => SORT_ASC,
                'id' => SORT_ASC,
                'com_name_en' => SORT_ASC,
                'com_name' => SORT_ASC,
            ]),
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionImport()
    {
        $this->ensureCanManageCommittee();

        if(Yii::$app->request->isPost){
            $csvFile = UploadedFile::getInstanceByName('csv_file');
            if(!$csvFile){
                Yii::$app->session->addFlash('error', 'Please upload a CSV file.');
                return $this->refresh();
            }

            $handle = $this->openImportedCsvFile($csvFile->tempName);
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
                return $this->normalizeImportedCsvHeader($header);
            }, $headers);

            $requiredColumns = [
                'is_jawatankuasa',
                'is_student',
                'committee_name',
                'is_pengarah',
                'can_approve',
                'cert_only',
                'member_name',
                'role',
                'is_leader',
                'username',
            ];
            foreach($requiredColumns as $column){
                if(!in_array($column, $headers, true)){
                    fclose($handle);
                    Yii::$app->session->addFlash('error', 'Missing required column: ' . $column);
                    return $this->refresh();
                }
            }

            $idx = array_flip($headers);
            $rowNo = 1;
            $createdCommittees = 0;
            $updatedCommittees = 0;
            $createdUsers = 0;
            $createdRoles = 0;
            $updatedRoles = 0;
            $unchangedRoles = 0;
            $skipped = 0;
            $messages = [];
            $warnings = [];

            $transaction = Yii::$app->db->beginTransaction();
            try{
                $lastCommitteeData = null;
                while(($row = fgetcsv($handle)) !== false){
                    $rowNo++;

                    $committeeNameBm = $this->normalizeImportedCsvValue($row[$idx['committee_name_bm']] ?? '');
                    $committeeNameEn = $this->normalizeImportedCsvValue($row[$idx['committee_name']] ?? '');

                    $committeeData = [
                        'com_name' => $committeeNameBm,
                        'is_jawatankuasa' => $this->normalizeImportedIntegerValue($row[$idx['is_jawatankuasa']] ?? 0),
                        'is_student' => $this->normalizeImportedIntegerValue($row[$idx['is_student']] ?? 0),
                        'com_name_en' => $committeeNameEn,
                        'is_pengarah' => $this->normalizeImportedIntegerValue($row[$idx['is_pengarah']] ?? 0),
                        'can_approve' => $this->normalizeImportedIntegerValue($row[$idx['can_approve']] ?? 0),
                        'cert_only' => $this->normalizeImportedIntegerValue($row[$idx['cert_only']] ?? 0),
                    ];

                    if(array_key_exists('committee_order', $idx)){
                        $committeeData['committee_order'] = $this->normalizeImportedIntegerValue($row[$idx['committee_order']] ?? 0);
                    }
                    $name = $this->normalizeImportedCsvValue($row[$idx['member_name']] ?? '');
                    $role = $this->normalizeImportedCsvValue($row[$idx['role']] ?? '');
                    $isLeader = $this->normalizeImportedIntegerValue($row[$idx['is_leader']] ?? 2);
                    $identifier = $this->normalizeImportedCsvValue($row[$idx['username']] ?? '');
                    $isStudent = (int)$committeeData['is_student'] === 1;
                    $isLeader = strtolower($role) === 'head' ? 1 : 0;

                    if($committeeData['com_name'] === '' && $committeeData['com_name_en'] === '' && $lastCommitteeData !== null){
                        $committeeData = $lastCommitteeData;
                    }

                    if($committeeData['com_name'] !== '' || $committeeData['com_name_en'] !== ''){
                        $lastCommitteeData = $committeeData;
                    }

                    if($committeeData['com_name'] === '' && $committeeData['com_name_en'] === '' && $name === '' && $identifier === ''){
                        continue;
                    }

                    if($committeeData['com_name_en'] === '' && $committeeData['com_name'] === ''){
                        $skipped++;
                        $messages[] = 'Row ' . $rowNo . ': committee_name is required (or leave empty to reuse previous row).';
                        continue;
                    }

                    if($name === ''){
                        $skipped++;
                        $messages[] = 'Row ' . $rowNo . ': member name is required.';
                        continue;
                    }

                    $committeeResult = $this->findOrCreateImportedCommittee($committeeData);
                    $committee = $committeeResult['model'];
                    if($committeeResult['created']){
                        $createdCommittees++;
                    }else if($committeeResult['updated']){
                        $updatedCommittees++;
                    }

                    $userResult = $this->findOrCreateImportedCommitteeUser($name, $identifier, $isStudent);
                    $user = $userResult['model'];
                    if(!$user){
                        $skipped++;
                        $messages[] = 'Row ' . $rowNo . ': ' . $userResult['message'];
                        continue;
                    }
                    if($userResult['created']){
                        $createdUsers++;
                    }

                    $roleResult = $this->ensureImportedCommitteeRole($user, $committee, $isLeader);
                    if(!$roleResult['ok']){
                        $skipped++;
                        $messages[] = 'Row ' . $rowNo . ': ' . $roleResult['message'];
                        continue;
                    }
                    if($roleResult['created']){
                        $createdRoles++;
                    }else if($roleResult['updated']){
                        $updatedRoles++;
                    }else{
                        $unchangedRoles++;
                    }
                }

                fclose($handle);
                $transaction->commit();

                Yii::$app->session->addFlash('success', 'Committee import completed. Committees created: ' . $createdCommittees . ', updated: ' . $updatedCommittees . '. Roles created: ' . $createdRoles . ', updated: ' . $updatedRoles . ', unchanged: ' . $unchangedRoles . '.');
                if($createdUsers > 0){
                    Yii::$app->session->addFlash('success', 'Created ' . $createdUsers . ' user account(s). Student default password: matric. Staff default password: email.');
                }
                if($warnings){
                    Yii::$app->session->addFlash('warning', implode('<br>', array_slice($warnings, 0, 10)));
                }
                if($skipped > 0){
                    Yii::$app->session->addFlash('warning', $skipped . ' row(s) skipped.');
                }
                if($messages){
                    Yii::$app->session->addFlash('error', implode('<br>', array_slice($messages, 0, 10)));
                }

                return $this->refresh();
            }catch(\Throwable $e){
                fclose($handle);
                $transaction->rollBack();
                Yii::$app->session->addFlash('error', $e->getMessage());
                return $this->refresh();
            }
        }

        return $this->render('import');
    }

    public function actionSummary()
    {
        $this->ensureCanViewCommitteeSummary();

        $roles = UserRole::find()->alias('ur')
            ->joinWith(['committee c', 'user u'])
            ->where([
                'ur.role_name' => 'committee',
                'ur.status' => 10,
            ])
            ->orderBy([
                '(CASE WHEN c.committee_order IS NULL OR c.committee_order = 0 THEN 1 ELSE 0 END)' => SORT_ASC,
                'c.committee_order' => SORT_ASC,
                'c.id' => SORT_ASC,
                'c.com_name_en' => SORT_ASC,
                'c.com_name' => SORT_ASC,
                'ur.is_leader' => SORT_ASC,
                'u.fullname' => SORT_ASC,
                'ur.id' => SORT_ASC,
            ])
            ->all();

        $groups = [];
        foreach($roles as $role){
            $committeeKey = $role->committee ? (int)$role->committee->id : 0;
            if(!isset($groups[$committeeKey])){
                $groups[$committeeKey] = [
                    'committee' => $role->committee,
                    'roles' => [],
                ];
            }
            $groups[$committeeKey]['roles'][] = $role;
        }

        return $this->render('summary', [
            'groups' => array_values($groups),
        ]);
    }

    public function actionLetter(){
        if(!Yii::$app->user->identity->isCommittee) return;

        $dataProvider = new ActiveDataProvider([
            'query' => UserRole::find()
            ->where(['=', 'role_name', 'committee'])
            ->andWhere(['user_id' => Yii::$app->user->identity->id])
        ]);

        return $this->render('letter', [
            'dataProvider' => $dataProvider,
                'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
        ]);
    }

    public function actionLetterPdf($id){
        if(!$this->canAccessDoc($id)) return false;

        $model = $this->findRole($id);
        $pdf = new LetterPdf;
        $pdf->model = $model;
        $pdf->generatePdf();
        exit;
    }

    public function actionCertificate($id){
        if(!$this->ensureCertificatesReleased(true, 2)){
            return $this->render('empty');
        }

        if(!$this->canAccessDoc($id)) return false;

        $pdf = new CertificateCommittee;
        $role = $this->findRole($id);
        $pdf->template = CertificateTemplate::findOne(2);
        $pdf->model = $role;
        $pdf->generatePdf();
        exit;
    }

    private function canAccessDoc($id){
        if(Yii::$app->user->identity->isManager || Yii::$app->user->identity->isAdmin){
            return true;
        }else{
            $role = $this->findRole($id);
            if($role->user_id == Yii::$app->user->identity->id){
                return true;
            }
        }
        return false;
    }

    private function ensureCertificatesReleased($allowPrivileged = true, $templateId = null)
    {
        $certificateName = $templateId === null ? 'Certificates and awards' : CertificateTemplate::displayName($templateId, 'Certificate');

        if($allowPrivileged && !Yii::$app->user->isGuest && (Yii::$app->user->identity->isManager || Yii::$app->user->identity->isAdmin)){
            return true;
        }

        if(!Setting::areCertificatesReleased()){
            $releaseDate = Setting::certificateReleaseText();
            $message = $releaseDate
                ? $certificateName . ' will be released from ' . $releaseDate . '.'
                : $certificateName . ' has not been published.';
            Yii::$app->session->addFlash('info', $message);

            return false;
        }

        if($templateId !== null && !CertificateTemplate::isPublished($templateId)){
            Yii::$app->session->addFlash('info', $certificateName . ' has not been published.');
            return false;
        }

        return true;
    }

    public function actionCertificatePage()
    {
        if(!Yii::$app->user->identity->isCommittee) return false;
        if(!$this->ensureCertificatesReleased(false, 2)){
            return $this->render('empty');
        }

        $list = UserRole::find()
        ->where([
            'user_id' => Yii::$app->user->identity->id,
            'role_name' => 'committee',
            'status' => 10
            ])
        ->all();

        return $this->render('certificate-page', [
            'list' => $list,
        ]);

    }


    /**
     * Creates a new Committee model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $this->ensureCanManageCommittee();

        $model = new Committee();

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
     * Updates an existing Committee model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $this->ensureCanManageCommittee();

        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Committee model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->ensureCanManageCommittee();

        $model = $this->findModel($id);
        if($model->getUserRoles()->exists()){
            Yii::$app->session->addFlash('error', 'This committee cannot be deleted because it has assigned users.');
            return $this->redirect(['index']);
        }

        $model->delete();
        Yii::$app->session->addFlash('success', 'Committee deleted.');

        return $this->redirect(['index']);
    }

    public function actionDeleteRole($id)
    {
        $this->findRole($id)->delete();
        Yii::$app->session->addFlash('success', "Role Deleted");

        return $this->redirect(['request']);
    }

    /**
     * Finds the Committee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Committee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Committee::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function ensureCanManageCommittee()
    {
        if(!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdminRegistration){
            return true;
        }

        throw new ForbiddenHttpException('You are not allowed to manage committees.');
    }

    protected function ensureCanViewCommitteeSummary()
    {
        if(!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin){
            return true;
        }

        throw new ForbiddenHttpException('You are not allowed to view committee summary.');
    }

    protected function findOrCreateImportedCommittee($committeeData)
    {
        $committeeName = $committeeData['com_name'];
        $committeeNameEn = $committeeData['com_name_en'];
        $query = Committee::find();
        if($committeeNameEn !== ''){
            $query->where(['com_name_en' => $committeeNameEn]);
        }else{
            $query->where(['com_name' => $committeeName]);
        }

        $committee = $query->one();
        $created = false;
        $updated = false;

        if(!$committee){
            $committee = new Committee();
            foreach($committeeData as $attribute => $value){
                $committee->$attribute = $value;
            }
            if(!$committee->save()){
                throw new \RuntimeException('Failed to create committee "' . ($committeeNameEn ?: $committeeName) . '" - ' . implode('; ', $committee->getFirstErrors()));
            }
            $created = true;
        }else{
            $dirty = [];
            foreach($committeeData as $attribute => $value){
                if($attribute === 'com_name' || $attribute === 'com_name_en'){
                    if($value === ''){
                        continue;
                    }
                    if((string)$committee->$attribute !== (string)$value){
                        $committee->$attribute = $value;
                        $dirty[] = $attribute;
                    }
                }else if((int)$committee->$attribute !== (int)$value){
                    $committee->$attribute = $value;
                    $dirty[] = $attribute;
                }
            }
            if($dirty && !$committee->save(false, array_unique($dirty))){
                throw new \RuntimeException('Failed to update committee "' . ($committeeNameEn ?: $committeeName) . '".');
            }
            $updated = !empty($dirty);
        }

        return [
            'model' => $committee,
            'created' => $created,
            'updated' => $updated,
        ];
    }

    protected function findOrCreateImportedCommitteeUser($name, $identifier, $isStudent)
    {
        if($isStudent){
            if($identifier === ''){
                return [
                    'model' => null,
                    'created' => false,
                    'warning' => '',
                    'message' => 'student matric is required.',
                ];
            }

            $existing = User::findAccountForRegistration($identifier, User::dummyEmailForMatric($identifier));
            $user = User::findOrCreateImportedStudentAccount($identifier, $name);
            return [
                'model' => $user ?: null,
                'created' => !$existing && $user,
                'warning' => '',
                'message' => $user ? '' : 'failed to create student user for matric ' . $identifier . '.',
            ];
        }

        $email = strtolower(trim($identifier));
        if($email !== '' && strpos($email, '@') !== false){
            $user = User::find()->where('LOWER(email) = :email', [':email' => $email])->one();
            $created = false;
            if(!$user){
                $user = new User();
                $user->scenario = 'create';
                $user->email = $email;
                $user->username = $email;
                $user->fullname = $name;
                $user->status = User::STATUS_ACTIVE;
                $user->is_student = 0;
                $user->is_internal = 1;
                $user->setPassword($email);
                $user->generateAuthKey();
                if(!$user->save()){
                    return [
                        'model' => null,
                        'created' => false,
                        'warning' => '',
                        'message' => 'failed to create staff user for email ' . $email . ' - ' . implode('; ', $user->getFirstErrors()),
                    ];
                }
                $created = true;
            }else{
                $dirty = [];
                if(!$user->fullname){
                    $user->fullname = $name;
                    $dirty[] = 'fullname';
                }
                if(!$user->username){
                    $user->username = $email;
                    $dirty[] = 'username';
                }
                if((int)$user->status !== (int)User::STATUS_ACTIVE){
                    $user->status = User::STATUS_ACTIVE;
                    $dirty[] = 'status';
                }
                if($user->is_student === null){
                    $user->is_student = 0;
                    $dirty[] = 'is_student';
                }
                if($user->is_internal === null){
                    $user->is_internal = 1;
                    $dirty[] = 'is_internal';
                }
                if($dirty){
                    $dirty[] = 'updated_at';
                    $user->save(false, array_unique($dirty));
                }
            }

            return [
                'model' => $user,
                'created' => $created,
                'warning' => '',
                'message' => '',
            ];
        }

        return [
            'model' => null,
            'created' => false,
            'warning' => '',
            'message' => 'staff email is required. Please add staff email in the CSV.',
        ];
    }

    protected function ensureImportedCommitteeRole(User $user, Committee $committee, $isLeader)
    {
        $committeeRole = UserRole::findOne([
            'user_id' => (int)$user->id,
            'role_name' => 'committee',
            'committee_id' => (int)$committee->id,
        ]);

        $leaderValue = (int)$isLeader === 1 ? 1 : 0;
        if(!$committeeRole){
            $committeeRole = new UserRole();
            $committeeRole->user_id = (int)$user->id;
            $committeeRole->role_name = 'committee';
            $committeeRole->committee_id = (int)$committee->id;
            $committeeRole->status = 10;
            $committeeRole->is_deleted = 0;
            $committeeRole->is_leader = $leaderValue;
            $committeeRole->request_at = new Expression('NOW()');
            $committeeRole->approve_at = new Expression('NOW()');
            if(!$committeeRole->save()){
                return [
                    'ok' => false,
                    'created' => false,
                    'updated' => false,
                    'message' => 'failed to create committee role - ' . implode('; ', $committeeRole->getFirstErrors()),
                ];
            }

            return [
                'ok' => true,
                'created' => true,
                'updated' => false,
                'message' => '',
            ];
        }

        $dirty = [];
        if((int)$committeeRole->status !== 10){
            $committeeRole->status = 10;
            $committeeRole->approve_at = new Expression('NOW()');
            $dirty[] = 'status';
            $dirty[] = 'approve_at';
        }
        if($leaderValue !== null && (int)$committeeRole->is_leader !== (int)$leaderValue){
            $committeeRole->is_leader = $leaderValue;
            $dirty[] = 'is_leader';
        }
        if((int)$committeeRole->is_deleted !== 0){
            $committeeRole->is_deleted = 0;
            $dirty[] = 'is_deleted';
        }

        if($dirty && !$committeeRole->save(false, array_unique($dirty))){
            return [
                'ok' => false,
                'created' => false,
                'updated' => false,
                'message' => 'failed to update committee role.',
            ];
        }

        return [
            'ok' => true,
            'created' => false,
            'updated' => !empty($dirty),
            'message' => '',
        ];
    }

    protected function normalizeImportedIntegerValue($value)
    {
        $value = strtolower($this->normalizeImportedCsvValue($value));
        if(in_array($value, ['yes', 'true', 'y'], true)){
            return 1;
        }
        if(in_array($value, ['no', 'false', 'n', ''], true)){
            return 0;
        }

        return (int)$value;
    }

    protected function normalizeImportedCsvHeader($header)
    {
        $header = strtolower($this->normalizeImportedCsvValue($header));
        $header = preg_replace('/[^a-z0-9]+/', '_', $header);
        $header = trim($header, '_');

        $aliases = [
            'jawatankuasa' => 'committee',
            'committee_english' => 'committee_english',
            'name' => 'member_name',
            'member' => 'member_name',
            'role' => 'role',
            'student_matric_staff_email' => 'username',
            'staff_email' => 'username',
            'matric' => 'username',
            'com_name_en' => 'committee_name',
            'committee_english' => 'committee_name',
            'committee_name_en' => 'committee_name',
            'com_name' => 'committee_name_bm',
            'jawatankuasa' => 'committee_name_bm',
            'committee_name_malay' => 'committee_name_bm',
        ];

        return $aliases[$header] ?? $header;
    }

    protected function normalizeImportedCsvValue($value)
    {
        $value = trim((string)$value);
        return str_replace([
            "\xEF\xBF\xBD",
            "\xE2\x80\x98",
            "\xE2\x80\x99",
            "\x91",
            "\x92",
        ], "'", $value);
    }

    protected function openImportedCsvFile($path)
    {
        $content = file_get_contents($path);
        if($content === false){
            return false;
        }

        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $handle = fopen('php://temp', 'r+');
        if($handle === false){
            return false;
        }

        fwrite($handle, $content);
        rewind($handle);
        return $handle;
    }

    protected function findRole($id)
    {
        if (($model = UserRole::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
