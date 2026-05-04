<?php

namespace app\controllers;

use app\models\Certificate;
use app\models\CertificateAchievement;
use app\models\CertificateExcellence;
use app\models\CertificateSession;
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
use app\models\ProgramSub;
use app\models\ProgramAchievement;
use app\models\ProgramRegistration;
use app\models\ProgramRubric;
use app\models\PublicRegistrationAccessForm;
use app\models\Questionnaire;
use app\models\QuestionnaireAnswer;
use app\models\QuestionnaireAnswerPost;
use app\models\Rubric;
use app\models\RubricAnswer;
use app\models\RubricCategory;
use app\models\RubricItem;
use app\models\RubricJudgingSession;
use app\models\Session;
use app\models\Setting;
use app\models\Upload;
use app\models\User;
use app\models\UserRole;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

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
                        'actions' => ['public-programs', 'public-register-form', 'public-register', 'public-edit-login', 'public-edit-auth', 'public-view-register', 'download-poster-file', 'download-payment-file', 'download-abstract-file'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
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

        $programQuery = Program::find();
        $programTable = Yii::$app->db->schema->getTableSchema(Program::tableName());
        if($programTable && $programTable->getColumn('is_active')){
            $programQuery->andWhere(['is_active' => 1]);
        }else if($programTable && $programTable->getColumn('status')){
            $programQuery->andWhere(['status' => 10]);
        }

        $programs = $programQuery
        //->where(['NOT IN', 'id', $arr])
        ->all();

        return $this->render('index',[
            'programs' => $programs,
            'registered' => $registered
        ]);
    }

    public function actionCreate()
    {
        if (!Yii::$app->user->identity->isManager) return false;

        $model = new Program();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->addFlash('success', "Program Created");
                return $this->redirect(['info', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

    public function actionInfo($id, $sub = null){
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
        if($sub){
            $roleQuery->andWhere(['or', ['program_sub' => $sub], ['program_sub' => null]]);
        }
        $role = $roleQuery->one();

        if(!$role){
            throw new NotFoundHttpException('Manager role not found for this program.');
        }

        $programSub = null;
        $model = null;

        if((int)$program->has_sub === 1){
            if($sub){
                $programSub = ProgramSub::findOne(['id' => (int)$sub, 'program_id' => (int)$id]);
                if(!$programSub){
                    throw new NotFoundHttpException('Sub program not found.');
                }
                $model = $programSub;
            }else{
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
                    $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
                    $hasSubActiveColumn = $subTable && $subTable->getColumn('is_active');
                    $activeSubIds = [];
                    foreach($program->programSubs as $sp){
                        if($hasSubActiveColumn && (int)$sp->getAttribute('is_active') !== 1){
                            continue;
                        }
                        $activeSubIds[(int)$sp->id] = true;
                    }

                    foreach(array_keys($activeSubIds) as $sid){
                        if(!array_key_exists((int)$sid, $allowedSubs)){
                            throw new ForbiddenHttpException('You do not have access to update this program.');
                        }
                    }
                }

                $model = $this->findModel($id);
            }
        }else{
            $model = $this->findModel($id);
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->addFlash('success', 'Program info updated.');
                return $this->refresh();
            }

            if($model->getErrors()){
                foreach($model->getErrors() as $errors){
                    foreach($errors as $e){
                        Yii::$app->session->addFlash('error', $e);
                    }
                }
            }else{
                Yii::$app->session->addFlash('error', 'Failed to update program info.');
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('info', [
            'model' => $model,
            'program' => $program,
            'programSub' => $programSub,
        ]);
    }

    public function actionAdminProgramSubs()
    {
        if(Yii::$app->user->isGuest || !Yii::$app->user->identity->isAdmin) return false;

        $programs = Program::find()->orderBy(['date_start' => SORT_ASC, 'id' => SORT_ASC])->all();

        if(Yii::$app->request->isPost){
            if((int)Yii::$app->request->post('toggle_active', 0) === 1){
                $programId = (int)Yii::$app->request->post('program_id');
                $program = Program::findOne($programId);

                if($program){
                    $programTable = Yii::$app->db->schema->getTableSchema(Program::tableName());

                    if($programTable && $programTable->getColumn('is_active')){
                        $val = (int)Yii::$app->request->post('is_active', 0) === 1 ? 1 : 0;
                        $program->setAttribute('is_active', $val);
                        $program->save(false, ['is_active']);
                        Yii::$app->session->addFlash('success', 'Program updated.');
                    }else if($programTable && $programTable->getColumn('status')){
                        $val = (int)Yii::$app->request->post('status', 0) === 10 ? 10 : 0;
                        $program->setAttribute('status', $val);
                        $program->save(false, ['status']);
                        Yii::$app->session->addFlash('success', 'Program updated.');
                    }
                }

                return $this->redirect(['admin-program-subs']);
            }

            $programId = (int)Yii::$app->request->post('program_id');
            $subName = trim((string)Yii::$app->request->post('sub_name'));
            $advisor = trim((string)Yii::$app->request->post('advisor'));

            if($programId > 0 && $subName !== ''){
                $exists = ProgramSub::find()
                    ->where(['program_id' => $programId, 'sub_name' => $subName])
                    ->one();

                if(!$exists){
                    $model = new ProgramSub();
                    $model->program_id = $programId;
                    $model->sub_name = $subName;
                    $model->advisor = ($advisor !== '') ? $advisor : null;

                    if($model->save()){
                        Yii::$app->session->addFlash('success', 'Sub program added.');
                        return $this->redirect(['admin-program-subs']);
                    }

                    if($model->getErrors()){
                        foreach($model->getErrors() as $errors){
                            foreach($errors as $e){
                                Yii::$app->session->addFlash('error', $e);
                            }
                        }
                    }else{
                        Yii::$app->session->addFlash('error', 'Unable to add sub program.');
                    }
                }else{
                    Yii::$app->session->addFlash('info', 'Sub program already exists for this program.');
                }
            }else{
                Yii::$app->session->addFlash('error', 'Please provide a sub program name.');
            }
        }

        $subs = ProgramSub::find()
            ->orderBy(['program_id' => SORT_ASC, 'id' => SORT_ASC])
            ->all();

        $subsByProgram = [];
        foreach($subs as $sub){
            $subsByProgram[(int)$sub->program_id][] = $sub;
        }

        return $this->render('admin_competition_categories', [
            'programs' => $programs,
            'subsByProgram' => $subsByProgram,
        ]);
    }

    public function actionAdminProgramAdd()
    {
        if(Yii::$app->user->isGuest || !Yii::$app->user->identity->isAdmin) return false;
        if(!Yii::$app->request->isPost) return false;

        $name = trim((string)Yii::$app->request->post('program_name'));
        $abbr = trim((string)Yii::$app->request->post('program_abbr'));

        if($name === ''){
            Yii::$app->session->addFlash('error', 'Program name cannot be blank.');
            return $this->redirect(['admin-program-subs']);
        }

        $today = date('Y-m-d');

        $model = new Program();
        $model->program_name = $name;
        if($abbr !== ''){
            $model->program_abbr = $abbr;
        }
        $model->date_start = $today;
        $model->date_end = $today;
        $model->reg_info = '-';

        if($model->save()){
            Yii::$app->session->addFlash('success', 'Program added.');
        }else{
            if($model->getErrors()){
                foreach($model->getErrors() as $errors){
                    foreach($errors as $e){
                        Yii::$app->session->addFlash('error', $e);
                    }
                }
            }else{
                Yii::$app->session->addFlash('error', 'Failed to add program.');
            }
        }

        return $this->redirect(['admin-program-subs']);
    }

    public function actionAdminProgramSubDelete($id)
    {
        if(Yii::$app->user->isGuest || !Yii::$app->user->identity->isAdmin) return false;
        if(!Yii::$app->request->isPost) return false;

        $model = ProgramSub::findOne((int)$id);
        if($model){
            $model->delete();
            Yii::$app->session->addFlash('success', 'Sub program removed.');
        }

        return $this->redirect(['admin-program-subs']);
    }

    public function actionAdminProgramSubToggle($id)
    {
        if(Yii::$app->user->isGuest || !Yii::$app->user->identity->isAdmin) return false;
        if(!Yii::$app->request->isPost) return false;

        $model = ProgramSub::findOne((int)$id);
        if($model){
            $table = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
            if($table && $table->getColumn('is_active')){
                $val = (int)Yii::$app->request->post('is_active', 0) === 1 ? 1 : 0;
                $model->setAttribute('is_active', $val);
                $model->save(false, ['is_active']);
                Yii::$app->session->addFlash('success', 'Sub program updated.');
            }
        }

        return $this->redirect(['admin-program-subs']);
    }

    public function actionAdminProgramUpdateName($id)
    {
        if(Yii::$app->user->isGuest || !Yii::$app->user->identity->isAdmin) return false;
        if(!Yii::$app->request->isPost) return false;

        $model = Program::findOne((int)$id);
        if(!$model){
            throw new NotFoundHttpException('Program not found.');
        }

        $name = trim((string)Yii::$app->request->post('program_name'));
        if($name === ''){
            Yii::$app->session->addFlash('error', 'Program name cannot be blank.');
            return $this->redirect(['admin-program-subs']);
        }

        $model->program_name = $name;
        if(!$model->save(false, ['program_name'])){
            Yii::$app->session->addFlash('error', 'Failed to update program name.');
            return $this->redirect(['admin-program-subs']);
        }

        Yii::$app->session->addFlash('success', 'Program name updated.');
        return $this->redirect(['admin-program-subs']);
    }

    public function actionAdminProgramSubUpdate($id)
    {
        if(Yii::$app->user->isGuest || !Yii::$app->user->identity->isAdmin) return false;
        if(!Yii::$app->request->isPost) return false;

        $model = ProgramSub::findOne((int)$id);
        if(!$model){
            throw new NotFoundHttpException('Sub program not found.');
        }

        $subName = trim((string)Yii::$app->request->post('sub_name'));
        $advisor = trim((string)Yii::$app->request->post('advisor'));

        if($subName === ''){
            Yii::$app->session->addFlash('error', 'Sub program name cannot be blank.');
            return $this->redirect(['admin-program-subs']);
        }

        $model->sub_name = $subName;
        $model->advisor = ($advisor !== '') ? $advisor : null;

        if(!$model->save(false, ['sub_name', 'advisor'])){
            Yii::$app->session->addFlash('error', 'Failed to update sub program.');
            return $this->redirect(['admin-program-subs']);
        }

        Yii::$app->session->addFlash('success', 'Sub program updated.');
        return $this->redirect(['admin-program-subs']);
    }

    public function actionViewRubric($id, $edit = null, $cat = null){
        if(!Yii::$app->user->identity->isManager) return false;

        $rubric = $this->findRubric($id);
        $this->ensureManagerRubricAccess($rubric->id);

        $program = null;
        $programSub = null;
        $prs = ProgramRubric::find()->where(['rubric_id' => $rubric->id])->all();
        if($prs){
            foreach($prs as $pr){
                $role = UserRole::findOne([
                    'program_id' => $pr->program_id,
                    'user_id' => Yii::$app->user->identity->id,
                    'role_name' => 'manager',
                    'program_sub' => $pr->program_sub,
                    'status' => 10,
                ]);
                if($role){
                    $program = Program::findOne($pr->program_id);
                    if($pr->program_sub){
                        $programSub = ProgramSub::findOne($pr->program_sub);
                    }
                    break;
                }
            }
        }

        $assign = new JuryAssign();
        $assign->rubric_id = $rubric->id;

        $model = new RubricAnswer();

        return $this->render('../program-registration/jury-judge',[
            'model' => $model,
            'assign' => $assign,
            'plain' => true,
            'title' => 'View Rubric',
            'write' => false,
            'edit' => ((int)$edit === 1),
            'cat' => $cat,
            'program' => $program,
            'programSub' => $programSub,
        ]);
    }

    public function actionRubricUpdateName($id)
    {
        if(!Yii::$app->user->identity->isManager) return false;
        if(!Yii::$app->request->isPost){
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        $rubric = $this->findRubric($id);
        $this->ensureManagerRubricAccess($rubric->id);

        $rubric->rubric_name = Yii::$app->request->post('rubric_name');
        $rubric->rubric_description = Yii::$app->request->post('rubric_description', $rubric->rubric_description);
        if($rubric->rubric_name === null || trim($rubric->rubric_name) === ''){
            Yii::$app->session->addFlash('error', 'Rubric name cannot be blank.');
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        if(!$rubric->save()){
            Yii::$app->session->addFlash('error', 'Failed to update rubric name.');
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        $this->saveRubricJudgingSessions($rubric);

        Yii::$app->session->addFlash('success', 'Rubric name updated.');
        return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
    }

    protected function saveRubricJudgingSessions($rubric)
    {
        $sessionIds = Yii::$app->request->post('session_id', []);
        $sessionNames = Yii::$app->request->post('session_name', []);
        $sessionStarts = Yii::$app->request->post('datetime_start', []);
        $sessionEnds = Yii::$app->request->post('datetime_end', []);
        $sessionLocations = Yii::$app->request->post('location', []);
        $sessionModes = Yii::$app->request->post('mode', []);
        $deleteIds = Yii::$app->request->post('delete_session', []);

        if(!$sessionIds && !$sessionNames && !$sessionStarts && !$sessionEnds && !$sessionLocations && !$sessionModes && !$deleteIds){
            return;
        }

        $deleteLookup = [];
        if(is_array($deleteIds)){
            foreach($deleteIds as $did){
                $did = (int)$did;
                if($did > 0){
                    $deleteLookup[$did] = true;
                }
            }
        }

        $existing = RubricJudgingSession::find()->where(['rubric_id' => $rubric->id])->indexBy('id')->all();
        $nextOrder = (int)RubricJudgingSession::find()->where(['rubric_id' => $rubric->id])->max('sort_order');
        $sortOrder = 0;

        $count = max(
            is_array($sessionIds) ? count($sessionIds) : 0,
            is_array($sessionNames) ? count($sessionNames) : 0
        );

        for($i = 0; $i < $count; $i++){
            $id = isset($sessionIds[$i]) ? (int)$sessionIds[$i] : 0;
            $name = isset($sessionNames[$i]) ? trim((string)$sessionNames[$i]) : '';
            $start = isset($sessionStarts[$i]) ? trim((string)$sessionStarts[$i]) : '';
            $end = isset($sessionEnds[$i]) ? trim((string)$sessionEnds[$i]) : '';
            $loc = isset($sessionLocations[$i]) ? trim((string)$sessionLocations[$i]) : '';
            $mode = isset($sessionModes[$i]) ? (int)$sessionModes[$i] : 1;

            $allEmpty = ($name === '' && $start === '' && $end === '' && $loc === '');
            if($id === 0 && $allEmpty){
                continue;
            }

            if($id > 0){
                if(!isset($existing[$id])){
                    continue;
                }
                $model = $existing[$id];
                if(isset($deleteLookup[$id])){
                    $model->delete();
                    continue;
                }
            }else{
                $model = new RubricJudgingSession();
                $model->rubric_id = $rubric->id;
                $nextOrder++;
            }

            if($name !== ''){
                $model->session_name = $name;
            }else if($id === 0){
                $model->session_name = 'Session ' . ($nextOrder);
            }

            if($start !== ''){
                $start = str_replace('T', ' ', $start);
                if(strlen($start) === 16){
                    $start .= ':00';
                }
            }
            if($end !== ''){
                $end = str_replace('T', ' ', $end);
                if(strlen($end) === 16){
                    $end .= ':00';
                }
            }

            $model->datetime_start = $start !== '' ? $start : null;
            $model->datetime_end = $end !== '' ? $end : null;
            $model->location = $loc !== '' ? $loc : null;
            $model->mode = in_array($mode, [1,2], true) ? $mode : 1;
            $model->sort_order = $sortOrder;
            $model->save();

            $sortOrder++;
        }
    }

    public function actionRubricCategoryAdd($id)
    {
        if(!Yii::$app->user->identity->isManager) return false;
        if(!Yii::$app->request->isPost){
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        $rubric = $this->findRubric($id);
        $this->ensureManagerRubricAccess($rubric->id);

        $cat = new RubricCategory();
        $cat->rubric_id = $rubric->id;
        $cat->category_name = Yii::$app->request->post('category_name');
        $cat->category_description = Yii::$app->request->post('category_description');
        $cat->cat_order = Yii::$app->request->post('cat_order');
        if($cat->cat_order === null || $cat->cat_order === '' ){
            $max = (int)RubricCategory::find()->where(['rubric_id' => $rubric->id])->max('cat_order');
            $cat->cat_order = $max + 1;
        }
        $cat->is_recommend = (int)Yii::$app->request->post('is_recommend', 0) === 1 ? 1 : 0;

        if($cat->category_name === null || trim($cat->category_name) === ''){
            Yii::$app->session->addFlash('error', 'Category name cannot be blank.');
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        if(!$cat->save()){
            Yii::$app->session->addFlash('error', 'Failed to add category.');
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        Yii::$app->session->addFlash('success', 'Category added.');
        return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
    }

    public function actionRubricCategoryEdit($id, $cat)
    {
        if(!Yii::$app->user->identity->isManager) return false;
        if(!Yii::$app->request->isPost){
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        $rubric = $this->findRubric($id);
        $this->ensureManagerRubricAccess($rubric->id);

        $category = RubricCategory::findOne(['id' => $cat, 'rubric_id' => $rubric->id]);
        if(!$category){
            throw new NotFoundHttpException('Category not found.');
        }

        $category->category_name = Yii::$app->request->post('category_name', $category->category_name);
        $category->category_description = Yii::$app->request->post('category_description', $category->category_description);
        $category->cat_order = Yii::$app->request->post('cat_order', $category->cat_order);
        $category->is_recommend = (int)Yii::$app->request->post('is_recommend', $category->is_recommend) === 1 ? 1 : 0;

        if($category->category_name === null || trim($category->category_name) === ''){
            Yii::$app->session->addFlash('error', 'Category name cannot be blank.');
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        if(!$category->save()){
            Yii::$app->session->addFlash('error', 'Failed to update category.');
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        Yii::$app->session->addFlash('success', 'Category updated.');
        return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
    }

    public function actionRubricCategoryDelete($id, $cat)
    {
        if(!Yii::$app->user->identity->isManager) return false;
        if(!Yii::$app->request->isPost){
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        $rubric = $this->findRubric($id);
        $this->ensureManagerRubricAccess($rubric->id);

        $category = RubricCategory::findOne(['id' => $cat, 'rubric_id' => $rubric->id]);
        if(!$category){
            throw new NotFoundHttpException('Category not found.');
        }

        RubricItem::deleteAll(['category_id' => $category->id]);
        $category->delete();

        Yii::$app->session->addFlash('success', 'Category deleted.');
        return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
    }

    public function actionRubricItemAdd($id, $cat)
    {
        if(!Yii::$app->user->identity->isManager) return false;
        if(!Yii::$app->request->isPost){
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        $rubric = $this->findRubric($id);
        $this->ensureManagerRubricAccess($rubric->id);

        $category = RubricCategory::findOne(['id' => $cat, 'rubric_id' => $rubric->id]);
        if(!$category){
            throw new NotFoundHttpException('Category not found.');
        }

        $item = new RubricItem();
        $item->category_id = $category->id;
        $item->item_text = Yii::$app->request->post('item_text');
        $item->item_description = Yii::$app->request->post('item_description');
        $item->item_short = Yii::$app->request->post('item_short');
        $item->item_type = (int)Yii::$app->request->post('item_type', 1);
        $item->option_number = Yii::$app->request->post('option_number');
        $postedRequired = Yii::$app->request->post('is_required', null);
        $item->is_required = ((int)$postedRequired === 1) ? 1 : 0;
        $item->item_order = Yii::$app->request->post('item_order');
        if($item->item_order === null || $item->item_order === ''){
            $max = (int)RubricItem::find()->where(['category_id' => $category->id])->max('item_order');
            $item->item_order = $max + 1;
        }
        $item->colum_ans = $this->generateRubricAnswerColumn($rubric->id, (int)$item->item_type);

        if($item->item_text === null || trim($item->item_text) === ''){
            Yii::$app->session->addFlash('error', 'Item text cannot be blank.');
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        if($item->item_type == 1){
            $opt = (int)$item->option_number;
            if($opt <= 0){
                Yii::$app->session->addFlash('error', 'Likert item must have option_number > 0.');
                return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
            }
        }

        if(!$item->save()){
            Yii::$app->session->addFlash('error', 'Failed to add item.');
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        Yii::$app->session->addFlash('success', 'Item added.');
        return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
    }

    public function actionRubricItemEdit($id, $item)
    {
        if(!Yii::$app->user->identity->isManager) return false;
        if(!Yii::$app->request->isPost){
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        $rubric = $this->findRubric($id);
        $this->ensureManagerRubricAccess($rubric->id);

        $it = RubricItem::findOne(['id' => $item]);
        if(!$it){
            throw new NotFoundHttpException('Item not found.');
        }

        $category = RubricCategory::findOne(['id' => $it->category_id, 'rubric_id' => $rubric->id]);
        if(!$category){
            throw new NotFoundHttpException('Item does not belong to this rubric.');
        }

        $it->item_text = Yii::$app->request->post('item_text', $it->item_text);
        $it->item_description = Yii::$app->request->post('item_description', $it->item_description);
        $it->item_short = Yii::$app->request->post('item_short', $it->item_short);
        $it->item_type = (int)Yii::$app->request->post('item_type', $it->item_type);
        $it->option_number = Yii::$app->request->post('option_number', $it->option_number);
        $postedRequired = Yii::$app->request->post('is_required', null);
        $it->is_required = ((int)$postedRequired === 1) ? 1 : 0;
        $it->item_order = Yii::$app->request->post('item_order', $it->item_order);

        if($it->item_text === null || trim($it->item_text) === ''){
            Yii::$app->session->addFlash('error', 'Item text cannot be blank.');
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        if($it->item_type == 1){
            $opt = (int)$it->option_number;
            if($opt <= 0){
                Yii::$app->session->addFlash('error', 'Likert item must have option_number > 0.');
                return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
            }
        }

        if(!$it->save()){
            Yii::$app->session->addFlash('error', 'Failed to update item.');
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        Yii::$app->session->addFlash('success', 'Item updated.');
        return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
    }

    public function actionRubricItemDelete($id, $item)
    {
        if(!Yii::$app->user->identity->isManager) return false;
        if(!Yii::$app->request->isPost){
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        $rubric = $this->findRubric($id);
        $this->ensureManagerRubricAccess($rubric->id);

        $it = RubricItem::findOne(['id' => $item]);
        if(!$it){
            throw new NotFoundHttpException('Item not found.');
        }

        $category = RubricCategory::findOne(['id' => $it->category_id, 'rubric_id' => $rubric->id]);
        if(!$category){
            throw new NotFoundHttpException('Item does not belong to this rubric.');
        }

        $it->delete();
        Yii::$app->session->addFlash('success', 'Item deleted.');
        return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
    }

    public function actionRubricCategorySort($id)
    {
        if(!Yii::$app->user->identity->isManager) return false;
        Yii::$app->response->format = Response::FORMAT_JSON;

        if(!Yii::$app->request->isPost){
            throw new BadRequestHttpException('Invalid request.');
        }

        $rubric = $this->findRubric($id);
        $this->ensureManagerRubricAccess($rubric->id);

        $order = Yii::$app->request->post('order', []);
        if(!is_array($order) || empty($order)){
            return ['success' => false];
        }

        $cats = RubricCategory::find()->where(['rubric_id' => $rubric->id])->indexBy('id')->all();
        $pos = 1;
        foreach($order as $catId){
            $catId = (int)$catId;
            if(isset($cats[$catId])){
                $cats[$catId]->cat_order = $pos;
                $cats[$catId]->save(false, ['cat_order']);
                $pos++;
            }
        }

        return ['success' => true];
    }

    public function actionRubricRearrangeColumns($id)
    {
        if(!Yii::$app->user->identity->isManager) return false;
        if(!Yii::$app->request->isPost){
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        $rubric = $this->findRubric($id);
        $this->ensureManagerRubricAccess($rubric->id);

        $items = RubricItem::find()
            ->alias('i')
            ->innerJoin(RubricCategory::tableName() . ' c', 'c.id = i.category_id')
            ->where(['c.rubric_id' => $rubric->id])
            ->orderBy(['c.cat_order' => SORT_ASC, 'i.item_order' => SORT_ASC, 'i.id' => SORT_ASC])
            ->all();

        $numIdx = 1;
        $textIdx = 1;
        foreach($items as $it){
            $type = (int)$it->item_type;
            if($type === 1 || $type === 2){
                if($numIdx > 30){
                    Yii::$app->session->addFlash('error', 'Cannot rearrange: exceeded available numeric columns (item_no1..item_no30).');
                    return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
                }
                $it->colum_ans = 'item_no' . $numIdx;
                $numIdx++;
            }else{
                if($textIdx > 10){
                    Yii::$app->session->addFlash('error', 'Cannot rearrange: exceeded available text columns (item_text1..item_text10).');
                    return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
                }
                $it->colum_ans = 'item_text' . $textIdx;
                $textIdx++;
            }

            $it->save(false, ['colum_ans']);
        }

        Yii::$app->session->addFlash('success', 'colum_ans rearranged according to current sorting.');
        return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
    }

    public function actionRubricImportCsv($id)
    {
        if(!Yii::$app->user->identity->isManager) return false;
        if(!Yii::$app->request->isPost){
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        $rubric = $this->findRubric($id);
        $this->ensureManagerRubricAccess($rubric->id);

        $file = UploadedFile::getInstanceByName('csv_file');
        if(!$file){
            Yii::$app->session->addFlash('error', 'Please choose a CSV file.');
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        $handle = @fopen($file->tempName, 'r');
        if(!$handle){
            Yii::$app->session->addFlash('error', 'Unable to read the uploaded CSV file.');
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $header = fgetcsv($handle);
            if(!$header){
                throw new BadRequestHttpException('CSV is empty.');
            }

            $header = array_map(function($h){
                return strtolower(trim((string)$h));
            }, $header);

            $idx = array_flip($header);
            if(!isset($idx['category_name']) || !isset($idx['item_text'])){
                throw new BadRequestHttpException('CSV header must contain at least: category_name, item_text');
            }

            $existingCats = RubricCategory::find()->where(['rubric_id' => $rubric->id])->all();
            $catsByName = [];
            foreach($existingCats as $c){
                $key = mb_strtolower(trim((string)$c->category_name));
                if($key !== ''){
                    $catsByName[$key] = $c;
                }
            }

            $catOrder = (int)RubricCategory::find()->where(['rubric_id' => $rubric->id])->max('cat_order');
            $itemOrderCache = [];

            $lastCategoryName = '';

            $createdCats = 0;
            $createdItems = 0;
            $rowNo = 1;
            while(($row = fgetcsv($handle)) !== false){
                $rowNo++;
                if(!is_array($row)){
                    continue;
                }

                $categoryName = trim((string)($row[$idx['category_name']] ?? ''));
                $itemText = trim((string)($row[$idx['item_text']] ?? ''));
                if($categoryName === '' && $itemText === ''){
                    continue;
                }

                if($categoryName === ''){
                    $categoryName = $lastCategoryName;
                }
                if($categoryName === ''){
                    throw new BadRequestHttpException('Row ' . $rowNo . ': category_name is required (or leave blank only after a category has been set).');
                }
                if($itemText === ''){
                    throw new BadRequestHttpException('Row ' . $rowNo . ': item_text is required.');
                }

                $lastCategoryName = $categoryName;

                $catKey = mb_strtolower($categoryName);
                $category = $catsByName[$catKey] ?? null;
                if(!$category){
                    $category = new RubricCategory();
                    $category->rubric_id = $rubric->id;
                    $category->category_name = $categoryName;
                    $category->is_recommend = 0;
                    $category->cat_order = ++$catOrder;

                    if(isset($idx['is_recommend'])){
                        $isRec = trim((string)($row[$idx['is_recommend']] ?? ''));
                        $category->is_recommend = ((int)$isRec === 1) ? 1 : 0;
                    }

                    if(!$category->save()){
                        throw new BadRequestHttpException('Row ' . $rowNo . ': failed to create category.');
                    }
                    $catsByName[$catKey] = $category;
                    $createdCats++;
                } else {
                    if(isset($idx['is_recommend'])){
                        $isRec = trim((string)($row[$idx['is_recommend']] ?? ''));
                        $newIsRec = ((int)$isRec === 1) ? 1 : 0;
                        if((int)$category->is_recommend !== $newIsRec){
                            $category->is_recommend = $newIsRec;
                            $category->save(false, ['is_recommend']);
                        }
                    }
                }

                if(!isset($itemOrderCache[$category->id])){
                    $itemOrderCache[$category->id] = (int)RubricItem::find()->where(['category_id' => $category->id])->max('item_order');
                }

                $itemTypeRaw = isset($idx['item_type']) ? trim((string)($row[$idx['item_type']] ?? '')) : '';
                $itemType = 1;
                if($itemTypeRaw !== ''){
                    if(is_numeric($itemTypeRaw)){
                        $itemType = (int)$itemTypeRaw;
                    }else{
                        $t = strtolower($itemTypeRaw);
                        if($t === 'likert') $itemType = 1;
                        else if($t === 'scale') $itemType = 1;
                        else if($t === 'yesno') $itemType = 2;
                        else if($t === 'boolean') $itemType = 2;
                        else if($t === 'shorttext') $itemType = 3;
                        else if($t === 'longtext') $itemType = 4;
                        else if($t === 'textarea') $itemType = 4;
                    }
                }

                $optionNumber = isset($idx['option_number']) ? (int)trim((string)($row[$idx['option_number']] ?? '')) : 0;
                if($itemType === 1 && $optionNumber <= 0){
                    throw new BadRequestHttpException('Row ' . $rowNo . ': option_number is required for likert/scale items.');
                }
                if(($itemType === 2) && $optionNumber <= 0){
                    $optionNumber = 1;
                }
                if($itemType === 3 || $itemType === 4){
                    $optionNumber = null;
                }

                $item = new RubricItem();
                $item->category_id = $category->id;
                $item->item_text = $itemText;
                $item->item_type = $itemType;
                $item->option_number = $optionNumber;
                $item->item_short = isset($idx['item_short']) ? trim((string)($row[$idx['item_short']] ?? '')) : null;
                $item->item_description = isset($idx['item_description']) ? trim((string)($row[$idx['item_description']] ?? '')) : null;
                $defaultRequired = ($itemType === 1 || $itemType === 2) ? 1 : 0;
                $item->is_required = $defaultRequired;
                if(isset($idx['is_required'])){
                    $req = trim((string)($row[$idx['is_required']] ?? ''));
                    $item->is_required = ((int)$req === 1) ? 1 : 0;
                }
                $item->item_order = ++$itemOrderCache[$category->id];
                $item->colum_ans = $this->generateRubricAnswerColumn($rubric->id, (int)$item->item_type);

                if(!$item->save()){
                    throw new BadRequestHttpException('Row ' . $rowNo . ': failed to create item.');
                }
                $createdItems++;
            }

            fclose($handle);
            $transaction->commit();

            Yii::$app->session->addFlash('success', 'CSV imported. Categories added: ' . $createdCats . ', Items added: ' . $createdItems . '.');
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        } catch (\Throwable $e) {
            @fclose($handle);
            $transaction->rollBack();
            Yii::$app->session->addFlash('error', $e->getMessage());
            return $this->redirect(['view-rubric', 'id' => $id, 'edit' => 1]);
        }
    }

    public function actionRubricItemSort($id, $cat)
    {
        if(!Yii::$app->user->identity->isManager) return false;
        Yii::$app->response->format = Response::FORMAT_JSON;

        if(!Yii::$app->request->isPost){
            throw new BadRequestHttpException('Invalid request.');
        }

        $rubric = $this->findRubric($id);
        $this->ensureManagerRubricAccess($rubric->id);

        $category = RubricCategory::findOne(['id' => $cat, 'rubric_id' => $rubric->id]);
        if(!$category){
            throw new NotFoundHttpException('Category not found.');
        }

        $order = Yii::$app->request->post('order', []);
        if(!is_array($order) || empty($order)){
            return ['success' => false];
        }

        $items = RubricItem::find()->where(['category_id' => $category->id])->indexBy('id')->all();
        $pos = 1;
        foreach($order as $itemId){
            $itemId = (int)$itemId;
            if(isset($items[$itemId])){
                $items[$itemId]->item_order = $pos;
                $items[$itemId]->save(false, ['item_order']);
                $pos++;
            }
        }

        return ['success' => true];
    }

    protected function generateRubricAnswerColumn($rubricId, $itemType)
    {
        $rubricId = (int)$rubricId;
        $itemType = (int)$itemType;

        $used = RubricItem::find()
            ->alias('i')
            ->select(['i.colum_ans'])
            ->innerJoin(RubricCategory::tableName() . ' c', 'c.id = i.category_id')
            ->where(['c.rubric_id' => $rubricId])
            ->andWhere(['not', ['i.colum_ans' => null]])
            ->column();

        $used = array_flip(array_filter(array_map('trim', $used)));

        $pool = [];
        if($itemType === 1 || $itemType === 2){
            for($i = 1; $i <= 30; $i++){
                $pool[] = 'item_no' . $i;
            }
        }else{
            for($i = 1; $i <= 10; $i++){
                $pool[] = 'item_text' . $i;
            }
        }

        foreach($pool as $col){
            if(!isset($used[$col])){
                return $col;
            }
        }

        throw new BadRequestHttpException('No available answer column slot for this rubric.');
    }

    public function actionRubrics($id, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;
        $role = UserRole::findOne(['program_id' => $id, 'user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager', 'program_sub' => $sub, 'status' => 10]);

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

    public function actionRubricAdd($id, $sub = null)
    {
        if(!Yii::$app->user->identity->isManager) return false;

        if(!Yii::$app->request->isPost){
            return $this->redirect(['rubrics', 'id' => $id, 'sub' => $sub]);
        }

        $role = UserRole::findOne([
            'program_id' => $id,
            'user_id' => Yii::$app->user->identity->id,
            'role_name' => 'manager',
            'program_sub' => $sub,
            'status' => 10,
        ]);

        if(!$role){
            return;
        }

        $program = $role->program;
        if($program->has_sub == 1 && !$sub){
            throw new NotFoundHttpException('Please provide sub program.');
        }

        $rubric = new Rubric();
        $rubric->rubric_name = Yii::$app->request->post('rubric_name');
        if($rubric->rubric_name === null || trim($rubric->rubric_name) === ''){
            Yii::$app->session->addFlash('error', 'Rubric name cannot be blank.');
            return $this->redirect(['rubrics', 'id' => $id, 'sub' => $sub]);
        }

        if(!$rubric->save()){
            Yii::$app->session->addFlash('error', 'Failed to create rubric.');
            return $this->redirect(['rubrics', 'id' => $id, 'sub' => $sub]);
        }

        $pr = new ProgramRubric();
        $pr->program_id = $id;
        $pr->rubric_id = $rubric->id;
        $pr->program_sub = $sub;

        if(!$pr->save()){
            $rubric->delete();
            Yii::$app->session->addFlash('error', 'Failed to assign rubric to program.');
            return $this->redirect(['rubrics', 'id' => $id, 'sub' => $sub]);
        }

        Yii::$app->session->addFlash('success', 'Rubric added.');
        return $this->redirect(['rubrics', 'id' => $id, 'sub' => $sub]);
    }

    public function actionRubricEdit($id, $sub = null, $pr = null, $rubric = null)
    {
        if(!Yii::$app->user->identity->isManager) return false;

        if(!Yii::$app->request->isPost){
            return $this->redirect(['rubrics', 'id' => $id, 'sub' => $sub]);
        }

        $role = UserRole::findOne([
            'program_id' => $id,
            'user_id' => Yii::$app->user->identity->id,
            'role_name' => 'manager',
            'program_sub' => $sub,
            'status' => 10,
        ]);

        if(!$role){
            return;
        }

        if(!$rubric){
            throw new NotFoundHttpException('Rubric not found.');
        }

        $rubricModel = $this->findRubric($rubric);

        if($pr){
            $assign = ProgramRubric::findOne(['id' => $pr, 'program_id' => $id, 'program_sub' => $sub, 'rubric_id' => $rubricModel->id]);
            if(!$assign){
                throw new NotFoundHttpException('Rubric assignment not found.');
            }
        }

        $rubricModel->rubric_name = Yii::$app->request->post('rubric_name', $rubricModel->rubric_name);
        $rubricModel->rubric_description = Yii::$app->request->post('rubric_description', $rubricModel->rubric_description);
        if($rubricModel->rubric_name === null || trim($rubricModel->rubric_name) === ''){
            Yii::$app->session->addFlash('error', 'Rubric name cannot be blank.');
            return $this->redirect(['rubrics', 'id' => $id, 'sub' => $sub]);
        }
        if(!$rubricModel->save()){
            Yii::$app->session->addFlash('error', 'Failed to update rubric.');
            return $this->redirect(['rubrics', 'id' => $id, 'sub' => $sub]);
        }

        $this->saveRubricJudgingSessions($rubricModel);

        Yii::$app->session->addFlash('success', 'Rubric updated.');
        return $this->redirect(['rubrics', 'id' => $id, 'sub' => $sub]);
    }

    public function actionRubricDelete($id, $sub = null, $pr)
    {
        if(!Yii::$app->user->identity->isManager) return false;

        if(!Yii::$app->request->isPost){
            return $this->redirect(['rubrics', 'id' => $id, 'sub' => $sub]);
        }

        $role = UserRole::findOne([
            'program_id' => $id,
            'user_id' => Yii::$app->user->identity->id,
            'role_name' => 'manager',
            'program_sub' => $sub,
            'status' => 10,
        ]);

        if(!$role){
            return;
        }

        $assign = ProgramRubric::findOne(['id' => $pr, 'program_id' => $id, 'program_sub' => $sub]);
        if(!$assign){
            throw new NotFoundHttpException('Rubric assignment not found.');
        }

        $assign->delete();
        Yii::$app->session->addFlash('success', 'Rubric removed.');
        return $this->redirect(['rubrics', 'id' => $id, 'sub' => $sub]);
    }

    public function actionAchievement($id, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;
        $role = UserRole::findOne([
            'program_id' => $id,
            'user_id' => Yii::$app->user->identity->id,
            'role_name' => 'manager',
            'program_sub' => $sub,
            'status' => 10,
        ]);

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
        return $this->redirect(['/program-reg-field/index', 'id' => $program->id, 'sub' => $sub]);
    }

    public function actionPublicRegisterForm($id, $reg = null, $edit = false)
    {
        $model = $this->findModel($id);
        $defaultMember = new Member();
        $members = [$defaultMember];
        $registrationClosed = false;

        if($reg){
            $register = $this->findRegistration($reg);
            $this->ensurePublicRegistrationAccess($register, $id);
            $members = $register->members;
            if(empty($members)){
                $members = [new Member()];
            }
        }else{
            $this->ensurePublicRegistrationEnabled($model);
            $registrationClosed = $this->isPublicRegistrationClosed($model);
            $register = new ProgramRegistration();
            $register->program_id = $model->id;
            $register->status = 0;
        }

        return $this->render('register', [
            'model' => $model,
            'register' => $register,
            'err' => false,
            'members' => $members,
            'demo' => false,
            'edit' => $edit,
            'publicMode' => true,
            'storageEntry' => (bool)Yii::$app->request->post('storage_entry', false),
            'registrationClosed' => $registrationClosed,
        ]);
    }

    public function actionPublicPrograms()
    {
        $programs = Program::find();

        $programTable = Yii::$app->db->schema->getTableSchema(Program::tableName());
        if($programTable && $programTable->getColumn('is_active')){
            $programs->andWhere(['is_active' => 1]);
        }else if($programTable && $programTable->getColumn('status')){
            $programs->andWhere(['status' => 10]);
        }

        $programs = $programs
            ->orderBy(['date_start' => SORT_ASC, 'id' => SORT_ASC])
            ->all();

        return $this->render('public_programs', [
            'programs' => $programs,
            'storageEntry' => (bool)Yii::$app->request->post('storage_entry', false),
        ]);
    }

    public function actionPublicRegister()
    {
        $id = Yii::$app->request->post('program_id');
        $reg = Yii::$app->request->post('reg_id');
        $edit = (bool)Yii::$app->request->post('edit');
        $model = $this->findModel($id);
        $defaultMember = new Member();
        $registrationClosed = false;

        if($reg){
            $register = $this->findRegistration($reg);
            $this->ensurePublicRegistrationAccess($register, $id);
            $members = $register->members;
            if(empty($members)){
                $members = [new Member()];
            }
        }else{
            $this->ensurePublicRegistrationEnabled($model);
            $registrationClosed = $this->isPublicRegistrationClosed($model);
            if($registrationClosed){
                Yii::$app->session->addFlash('error', 'Registration is closed for this program.');

                $register = new ProgramRegistration();
                $register->program_id = $model->id;
                $register->status = 0;
                $members = [$defaultMember];

                return $this->render('register', [
                    'model' => $model,
                    'register' => $register,
                    'err' => false,
                    'members' => $members,
                    'demo' => false,
                    'edit' => false,
                    'publicMode' => true,
                    'storageEntry' => (bool)Yii::$app->request->post('storage_entry', false),
                    'registrationClosed' => true,
                ]);
            }
            $register = new ProgramRegistration();
            $register->program_id = $model->id;
            $register->status = 0;
            $members = [$defaultMember];
        }

        $isUpdate = !$register->isNewRecord;
        $register->scenario = 'public_program' . $id . ($isUpdate ? '_update' : '_create');

        if($register->load(Yii::$app->request->post())){
            if(!$reg && $registrationClosed){
                Yii::$app->session->addFlash('error', 'Registration is closed for this program.');

                return $this->render('register', [
                    'model' => $model,
                    'register' => $register,
                    'err' => false,
                    'members' => $members,
                    'demo' => false,
                    'edit' => false,
                    'publicMode' => true,
                    'storageEntry' => (bool)Yii::$app->request->post('storage_entry', false),
                    'registrationClosed' => true,
                ]);
            }
            $register->status = 10;
            if(!$isUpdate){
                $register->submitted_at = new Expression('NOW()');
                $register->created_at = time();
            }
            $register->updated_at = time();
            $register->group_member = 1;
            $register->project_name = $this->myTrim($register->project_name);

            $register->abstract_instance = \yii\web\UploadedFile::getInstance($register, 'abstract_instance');
            $register->poster_instance = \yii\web\UploadedFile::getInstance($register, 'poster_instance');
            $register->payment_instance = \yii\web\UploadedFile::getInstance($register, 'payment_instance');

            if(!$isUpdate && $register->edit_password){
                $register->setEditPassword($register->edit_password);
            }

            if(!$isUpdate && empty($register->user_id) && $register->contact_email){
                $publicUser = User::findByEmail($register->contact_email);

                if(!$publicUser){
                    $publicUser = new User();
                    $publicUser->scenario = 'create';
                    $publicUser->email = $register->contact_email;
                    $publicUser->username = $register->contact_email;
                    $publicUser->fullname = $register->contact_person ?: $register->contact_email;
                    $publicUser->status = User::STATUS_ACTIVE;
                    $publicUser->generateAuthKey();
                    $publicUser->setPassword(Yii::$app->security->generateRandomString(16));

                    if(!$publicUser->save()){
                        Yii::$app->session->addFlash('error', 'Unable to prepare a public participant account for this email.');

                        return $this->render('register', [
                            'model' => $model,
                            'register' => $register,
                            'err' => true,
                            'members' => (empty($members)) ? [$defaultMember] : $members,
                            'demo' => false,
                            'edit' => $edit,
                            'publicMode' => true,
                            'storageEntry' => (bool)Yii::$app->request->post('storage_entry', false),
                        ]);
                    }
                }

                $register->user_id = $publicUser->id;
            }

            $oldIDs = [];
            $deletedIDs = [];
            if($isUpdate){
                $oldIDs = ArrayHelper::map($members, 'id', 'id');
            }

            $members = Model::createMultiple(Member::class);
            Model::loadMultiple($members, Yii::$app->request->post());

            if($isUpdate){
                $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($members, 'id', 'id')));
            }

            $valid = $register->validate();
            $valid = Model::validateMultiple($members) && $valid;

            if($valid){
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $register->uploadFile('payment');
                    $register->uploadFile('poster');
                    $register->uploadFile('abstract');

                    if($flag = $register->save(false)) {
                        if($isUpdate && !empty($deletedIDs)) {
                            Member::deleteAll(['id' => $deletedIDs]);
                        }

                        foreach ($members as $i => $member) {
                            if ($flag === false) {
                                break;
                            }

                            $member->member_name = strtoupper($member->member_name);
                            $member->program_reg_id = $register->id;

                            if (!($flag = $member->save(false))) {
                                break;
                            }
                        }
                    }

                    if($flag){
                        $transaction->commit();
                        $this->grantPublicRegistrationAccess($register);
                        Yii::$app->session->addFlash('success', $isUpdate ? 'The information has been successfully updated.' : 'Registration successful.');
                        if(Yii::$app->request->post('storage_entry')){
                            return $this->render('view_register', [
                                'model' => $model,
                                'register' => $register,
                                'members' => $register->members,
                                'edit' => false,
                                'publicMode' => true,
                                'storageEntry' => true,
                            ]);
                        }
                        return $this->redirect(['public-view-register', 'id' => $register->program_id, 'reg' => $register->id]);
                    }

                    $transaction->rollBack();
                } catch (\Exception $e) {
                    Yii::$app->session->addFlash('error', $e->getMessage());
                    $transaction->rollBack();
                }
            } else {
                Yii::$app->session->addFlash('error', 'Please correct the highlighted fields and try again.');
            }
        }

        return $this->render('register', [
            'model' => $model,
            'register' => $register,
            'err' => true,
            'members' => (empty($members)) ? [$defaultMember] : $members,
            'demo' => false,
            'edit' => $edit,
            'publicMode' => true,
            'storageEntry' => (bool)Yii::$app->request->post('storage_entry', false),
        ]);
    }

    public function actionPublicEditLogin($id)
    {
        $model = $this->findModel($id);
        $accessForm = new PublicRegistrationAccessForm();

        return $this->render('public_edit_login', [
            'model' => $model,
            'accessForm' => $accessForm,
            'storageEntry' => (bool)Yii::$app->request->post('storage_entry', false),
        ]);
    }

    public function actionPublicEditAuth($id)
    {
        $model = $this->findModel($id);
        $accessForm = new PublicRegistrationAccessForm();

        if($accessForm->load(Yii::$app->request->post())){
            $registration = $accessForm->loadRegistration($id);

            if($accessForm->validate()){
                $this->grantPublicRegistrationAccess($registration);
                Yii::$app->session->addFlash('success', 'Edit access granted.');
                if(Yii::$app->request->post('storage_entry')){
                    return $this->actionPublicRegisterForm($id, $registration->id, true);
                }
                return $this->redirect(['public-register-form', 'id' => $id, 'reg' => $registration->id, 'edit' => 1]);
            }

            Yii::$app->session->addFlash('error', 'Incorrect email or password / PIN.');
        }

        return $this->render('public_edit_login', [
            'model' => $model,
            'accessForm' => $accessForm,
            'storageEntry' => (bool)Yii::$app->request->post('storage_entry', false),
        ]);
    }

    public function actionPublicViewRegister($id, $reg)
    {
        $model = $this->findModel($id);
        $register = $this->findRegistration($reg);
        $this->ensurePublicRegistrationAccess($register, $id);

        return $this->render('view_register', [
            'model' => $model,
            'register' => $register,
            'members' => $register->members,
            'edit' => false,
            'publicMode' => true,
            'storageEntry' => (bool)Yii::$app->request->post('storage_entry', false),
        ]);
    }

    public function actionCertificate() //program
    {
        $setting = Setting::findOne(1);
        $allow_from = $setting->allow_cert_from;
        if(time() < strtotime($allow_from)){
            Yii::$app->session->addFlash('info', "The certificates are expected to be released soon.");
            return $this->render('empty');
        }

        //ni utk participantion
        $registered = ProgramRegistration::find()
        ->joinWith(['program p'])
        ->where(['user_id' => Yii::$app->user->identity->id, 'status' => 10, 'p.program_type' => 1])
        ->all();

        $session = Session::find()->alias('a')
        ->select('a.*, r.id as reg_id')
        ->joinWith(['program p', 'sessionAttendances t'])
        ->innerJoin('program_reg r', 'r.program_id = p.id')
        ->where(['r.user_id' => Yii::$app->user->identity->id, 'r.status' => 10, 'p.program_type' => 2, 't.user_id' => Yii::$app->user->identity->id])
        ->all();

        //ni utk achievement /medal

        $medals = ProgramRegistration::find()->alias('a')
        ->joinWith(['program p'])
        ->where(['a.user_id' => Yii::$app->user->identity->id, 'a.status' => 10, 'p.program_type' => 1])
        ->andWhere(['>', 'a.score', 0])
        ->andWhere(['>', 'a.award', 0])
        ->all();

        // ni utk excellence

        $query = new Query();
        $excel = $query->select('a.*, v.name as achieve_name')
        ->from('program_reg a')
        ->innerJoin('program_reg_achieve e', 'e.program_reg_id = a.id')
        ->innerJoin('program_achievement v', 'v.id = e.achieve_id')
        ->where(['a.user_id' => Yii::$app->user->identity->id, 'a.status' => 10])
        ->all();
        
       // echo count($excel);die();
        return $this->render('certificate',[
            'registered' => $registered,
            'medals' => $medals,
            'excel' => $excel,
            'sessions' => $session
        ]);
    }

    public function actionPrequestion($fresh = false)
    {
        $check = QuestionnaireAnswer::findOne(['user_id' => Yii::$app->user->identity->id]);
        if($check){
            if(!$fresh){
                Yii::$app->session->addFlash('info', "You have answered this pre-event question.");
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
                Yii::$app->session->addFlash('info', "You have answered this post-event question.");
            }
            
            return $this->render('empty');
        }

        $quest_likert = Questionnaire::findAll(['pre_post' => 2, 'question_type' => 1]);
        $quest_essay = Questionnaire::findAll(['pre_post' => 2, 'question_type' => 2]);
        $quest_checkbox = Questionnaire::find()
        ->where(['pre_post' => 2, 'question_type' => 3])
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


    public function actionRegisterForm($id, $reg = null, $edit = false){
        $end = Setting::findOne(1)->date_end;
        if(time() > strtotime($end)) {
            Yii::$app->session->addFlash('error', "Registration closed, the program had ended");
            return $this->render('empty');
        }
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
            $set = Setting::findOne(1);
            $due = strtotime($set->allow_edit_reg_until.' 23:59:59');
            if(Yii::$app->user->identity->id == $register->user_id){
                if($edit){
                    if(time() > $due){
                        return;
                    }
                }
            }else{
                return;
            }
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
            'demo' => false,
            'edit' => $edit
        ]);
    }

    /**
     * method ni hanya utk post - khusus untuk store data
     * tp perlu jgk render utk case error
     */
    public function actionRegister(){
        $id = Yii::$app->request->post('program_id');
        $reg = Yii::$app->request->post('reg_id');
        $edit = Yii::$app->request->post('edit');
        date_default_timezone_set("Asia/Kuala_Lumpur");
        $check = QuestionnaireAnswer::findOne(['user_id' => Yii::$app->user->identity->id]);
        if(!$check){
            Yii::$app->session->addFlash('info', "You need to answer pre-event questionnaire before registering to the program.");
            return $this->redirect(['prequestion']);
        }
        
        $model = $this->findModel($id);
        
        if($reg){
            //update 
            $register = $this->findRegistration($reg);
            $members = $register->members;
        }else{
            //new record
            $register = new ProgramRegistration();
            $defaultMember = new Member();
            $defaultMember->member_name = Yii::$app->user->identity->fullname;
            $defaultMember->member_matric = Yii::$app->user->identity->matric;
            $members = [$defaultMember];
            $register->program_id = $model->id;
            $register->user_id = Yii::$app->user->identity->id;
            $register->scenario = 'draft';
            $register->status = 0;
        }
        
        


        if($register->load(Yii::$app->request->post())){

            //verify dia nk register ke belum
            $p = $register->program_id;
            $sub = $register->program_sub;

            if($register->isNewRecord){
                //check dah ada ke belum untuk new record shj
                $ada = ProgramRegistration::find()->where(['program_id' => $p, 'user_id' => Yii::$app->user->identity->id]);
                if($sub){
                    $ada = $ada->andWhere(['program_sub' => $sub]);
                }
                $ada = $ada->one();
            }else{
                $ada = false;
            }
            

            if($ada){
                Yii::$app->session->addFlash('error', "Sorry, registration failed. You have registered to this program");
            }else{
                $action =  Yii::$app->request->post('action');
                if($action == 'submit'){
                    $register->status = 10;
                    $register->scenario = 'program'.$id;
                    $register->submitted_at = new Expression('NOW()');
                }else if($action == 'draft'){
                    $register->scenario = 'draft';
                }
    
                $register->group_member = 1;
                $register->project_name = $this->myTrim($register->project_name);
                if($register->isNewRecord){
                    $register->created_at = time();
                }
                
                $register->updated_at = time();
    
                $oldIDs = [];
                $deletedIDs = [];
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
                        $register->uploadFile('payment');
                        $register->uploadFile('poster');
                        $register->uploadFile('abstract');
                        
                        if ($flag = $register->save(false)) {
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
                            }else if($action == 'update'){
                                Yii::$app->session->addFlash('success', "The information has been successfully updated.");
                            }

                            return $this->redirect(['view-register', 'id' => $register->program_id, 'reg' => $register->id]);
                            
    
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
                    if(!$edit){
                        $register->status = 0;
                    }
                    
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
            'edit' => $edit
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
        
        return $this->render('view_register', [
            'model' => $model,
            'register' => $register,
            'members' => (empty($members)) ? [new Member()] : $members,
            'edit' => false,
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

    protected function ensureManagerRubricAccess($rubricId)
    {
        $prs = ProgramRubric::find()->where(['rubric_id' => $rubricId])->all();
        if(!$prs){
            throw new NotFoundHttpException('Rubric is not assigned to any program.');
        }

        foreach($prs as $pr){
            $role = UserRole::findOne([
                'program_id' => $pr->program_id,
                'user_id' => Yii::$app->user->identity->id,
                'role_name' => 'manager',
                'program_sub' => $pr->program_sub,
                'status' => 10,
            ]);

            if($role){
                return true;
            }
        }

        throw new NotFoundHttpException('You do not have access to this rubric.');
    }

    protected function publicRegistrationSessionKey($registrationId)
    {
        return 'public_reg_access_' . (int)$registrationId;
    }

    protected function grantPublicRegistrationAccess($registration)
    {
        Yii::$app->session->set($this->publicRegistrationSessionKey($registration->id), 1);
    }

    protected function hasPublicRegistrationAccess($registration)
    {
        return (bool)Yii::$app->session->get($this->publicRegistrationSessionKey($registration->id), false);
    }

    protected function ensurePublicRegistrationAccess($registration, $programId)
    {
        if((int)$registration->program_id !== (int)$programId || !$this->hasPublicRegistrationAccess($registration)){
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function ensurePublicRegistrationEnabled($program)
    {
        $programTable = Yii::$app->db->schema->getTableSchema(Program::tableName());
        $isEnabled = true;

        if($programTable && $programTable->getColumn('is_active')){
            $isEnabled = ((int)$program->getAttribute('is_active') === 1);
        }else if($programTable && $programTable->getColumn('status')){
            $isEnabled = ((int)$program->getAttribute('status') === 10);
        }

        if(!$isEnabled){
            Yii::$app->session->addFlash('error', 'Public registration is currently closed for this program.');
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function isPublicRegistrationClosed($program)
    {
        $programTable = Yii::$app->db->schema->getTableSchema(Program::tableName());
        if(!$programTable || !$programTable->getColumn('reg_closed')){
            return false;
        }

        return ((int)$program->getAttribute('reg_closed') === 1);
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

    protected function findSessionReg($s, $reg, $u)
    {
        $model = Session::find()->alias('a')
        ->select('a.*, r.id as reg_id, t.user_id, u.fullname, p.program_name')
        ->joinWith(['program p', 'sessionAttendances t'])
        ->innerJoin('program_reg r', 'r.program_id = p.id')
        ->innerJoin('user u', 'u.id = r.user_id')
        ->where(['a.id' => $s, 'r.id' => $reg, 'r.user_id' => $u, 'r.status' => 10, 'p.program_type' => 2, 't.user_id' => $u])
        ->one();

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findRegistrationAchievement($id)
    {
        $model = ProgramRegistration::find()->alias('a')
        ->joinWith(['program p'])
        ->where(['a.id' => $id])
        ->andWhere(['>', 'a.award', 0])
        ->one();
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findRegistrationExcellence($id)
    {
        $model = ProgramRegistration::find()->alias('a')
        ->select('a.*, v.name as achieve_name')
        ->innerJoin('program_reg_achieve e', 'e.program_reg_id = a.id')
        ->innerJoin('program_achievement v', 'v.id = e.achieve_id')
        ->where(['a.id' => $id])
        ->one();
        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDownloadPosterFile($id){
        $model = $this->findRegistration($id);
        $this->authorizeRegistrationDownload($model);
        Upload::download($model, 'poster', 'Poster_iCreate');
    }

    public function actionDownloadPaymentFile($id){
        $model = $this->findRegistration($id);
        $this->authorizeRegistrationDownload($model);
        Upload::download($model, 'payment', 'Payment_iCreate');
    }

    public function actionDownloadAbstractFile($id){
        $model = $this->findRegistration($id);
        $this->authorizeRegistrationDownload($model);
        Upload::download($model, 'abstract', 'Abstract_iCreate');
    }

    protected function authorizeRegistrationDownload($registration)
    {
        if(Yii::$app->user->isGuest){
            $this->ensurePublicRegistrationAccess($registration, $registration->program_id);
        }
    }

    private function meAsMentor($reg){
        $mentor = false;
        $mentors = $reg->mentors;
        if($mentors){
            foreach($mentors as $m){
                if($m->user_id == Yii::$app->user->identity->id){
                    $mentor = true;
                }
            }
        }
        return $mentor;
    }

    public function actionCertParticipation($reg){
        $pdf = new Certificate;
        
        $reg = $this->findRegistration($reg);
        $mentor = $this->meAsMentor($reg);
        
        if($reg->user_id == Yii::$app->user->identity->id || Yii::$app->user->identity->isManager || $mentor){ // atau mentor

            $pdf->template = CertificateTemplate::findOne(1);
            $pdf->model = $reg;
            $pdf->generatePdf();
        }
        exit;
    }

    public function actionCertParticipationSession($reg, $s, $u){
        $pdf = new CertificateSession;
        $session = $this->findSessionReg($s, $reg, $u);
        
        if($session->user_id == Yii::$app->user->identity->id || Yii::$app->user->identity->isManager){
            $pdf->template = CertificateTemplate::findOne(7);
            $pdf->model = $session;
            $pdf->generatePdf();
        }
        exit;
    }

    public function actionCertAchievement($reg){
        $pdf = new CertificateAchievement;
        $reg = $this->findRegistrationAchievement($reg);
        $mentor = $this->meAsMentor($reg);
        if($reg->user_id == Yii::$app->user->identity->id || Yii::$app->user->identity->isManager || $mentor){
            $pdf->template = CertificateTemplate::findOne(4);
            $pdf->model = $reg;
            $pdf->generatePdf();
        }
        exit;
    }

    public function actionCertExcellence($reg){
        $pdf = new CertificateExcellence;
        $reg = $this->findRegistrationExcellence($reg);
        $mentor = $this->meAsMentor($reg);
        if($reg->user_id == Yii::$app->user->identity->id || Yii::$app->user->identity->isManager || $mentor){
            $pdf->template = CertificateTemplate::findOne(5);
            $pdf->model = $reg;
            $pdf->generatePdf();
        }
        exit;
    }

}
