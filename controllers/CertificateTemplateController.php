<?php

namespace app\controllers;

use app\models\CertificateTemplate;
use app\models\JuryAssign;
use app\models\ParticipantAchieve;
use app\models\Program;
use app\models\ProgramAchievement;
use app\models\ProgramRegistration;
use app\models\ProgramSub;
use app\models\ProgramWinnerTitle;
use app\models\SessionAttendanceSearch;
use app\models\Setting;
use app\models\UserRole;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class CertificateTemplateController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['achievement-config', 'achievement-import'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return !Yii::$app->user->isGuest
                                && (Yii::$app->user->identity->isAdmin || Yii::$app->user->identity->isAdminJury);
                        },
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return !Yii::$app->user->isGuest
                                && (Yii::$app->user->identity->isAdmin || Yii::$app->user->identity->isAdminCertificate);
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $setting = Setting::findOne(1);
        if ($setting && Yii::$app->request->isPost) {
            $setting->allow_cert_from = Yii::$app->request->post('allow_cert_from');
            $published = (array)Yii::$app->request->post('published', []);

            foreach (CertificateTemplate::find()->all() as $template) {
                $template->published = array_key_exists($template->id, $published) ? 1 : 0;
                $template->updated_at = date('Y-m-d H:i:s');
                $template->save(false);
            }

            if ($setting->save(false)) {
                Yii::$app->session->addFlash('success', 'Certificate release settings updated.');
            } else {
                Yii::$app->session->addFlash('error', 'Unable to update certificate release settings.');
            }

            return $this->redirect(['index']);
        }

        $models = CertificateTemplate::find()->orderBy(['id' => SORT_ASC])->all();

        return $this->render('index', [
            'models' => $models,
            'setting' => $setting,
        ]);
    }

    public function actionParticipants()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ProgramRegistration::find()->alias('a')
                ->joinWith(['user u', 'program p', 'programSub ps'])
                ->where(['>', 'a.status', 0])
                ->orderBy(['p.id' => SORT_ASC, 'ps.id' => SORT_ASC, 'a.group_name' => SORT_ASC, 'a.id' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        return $this->render('participants', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionJuries()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => JuryAssign::find()->alias('a')
                ->select([
                    'a.user_id',
                    'r.program_id AS program_id',
                    'r.program_sub AS program_sub',
                    'MIN(a.status) AS status',
                    'MIN(a.id) AS id',
                ])
                ->joinWith(['registration r', 'user u'])
                ->groupBy(['a.user_id', 'r.program_id', 'r.program_sub'])
                ->orderBy(['u.fullname' => SORT_ASC, 'r.program_id' => SORT_ASC, 'r.program_sub' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        return $this->render('juries', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCommittees()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => UserRole::find()->alias('a')
                ->joinWith(['user u', 'committee c'])
                ->where([
                    'a.role_name' => 'committee',
                    'a.status' => 10,
                ])
                ->orderBy(['u.fullname' => SORT_ASC, 'c.com_name_en' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        return $this->render('committees', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSessionParticipants()
    {
        $searchModel = new SessionAttendanceSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('session-participants', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAchievementConfig()
    {
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $action = (string)($post['action_type'] ?? 'bulk-delete');
            $achievementId = (int)($post['achievement_id'] ?? 0);
            $hasWinnerCountColumn = $this->hasProgramAchievementWinnerCountColumn();
            $winnerCountRaw = trim((string)($post['winner_count'] ?? ''));
            $winnerCount = $winnerCountRaw === '' ? null : (int)$winnerCountRaw;

            if($action === 'bulk-winner-title-update'){
                if(!$hasWinnerCountColumn || !$this->hasProgramWinnerTitleAchievementColumn()){
                    Yii::$app->session->addFlash('error', 'Winner title bulk update needs the achievement-based winner title table.');
                    return $this->redirect(['achievement-config']);
                }

                $bulkWinnerCount = (int)($post['bulk_winner_count'] ?? 0);
                if($bulkWinnerCount < 2 || $bulkWinnerCount > 5){
                    Yii::$app->session->addFlash('error', 'Please choose a number of winners between 2 and 5.');
                    return $this->redirect(['achievement-config']);
                }

                $result = $this->bulkSaveWinnerTitlesByCount($bulkWinnerCount, $post['bulk_winner_titles'] ?? []);
                if($result['updated'] > 0 && $result['errors'] === 0){
                    Yii::$app->session->addFlash('success', 'Winner titles updated for ' . $result['updated'] . ' achievement(s).');
                }else if($result['updated'] > 0){
                    Yii::$app->session->addFlash('error', 'Winner titles updated for ' . $result['updated'] . ' achievement(s), but ' . $result['errors'] . ' row(s) could not be saved.');
                }else{
                    Yii::$app->session->addFlash('error', 'No achievements found with ' . $bulkWinnerCount . ' winner(s).');
                }

                return $this->redirect(['achievement-config']);
            }

            if($action === 'add'){
                $target = $this->parseAchievementTarget((string)($post['achievement_target'] ?? ''));
                $name = trim((string)($post['name'] ?? ''));
                if(!$target || $name === ''){
                    Yii::$app->session->addFlash('error', 'Please select a program/sub and enter an achievement name.');
                    return $this->redirect(['achievement-config']);
                }

                $duplicateQuery = ProgramAchievement::find()->where([
                    'program_id' => $target['program_id'],
                    'name' => $name,
                ]);
                if($target['program_sub'] === null){
                    $duplicateQuery->andWhere(['program_sub' => null]);
                }else{
                    $duplicateQuery->andWhere(['program_sub' => $target['program_sub']]);
                }
                if($duplicateQuery->exists()){
                    Yii::$app->session->addFlash('error', 'Achievement already exists for the selected program/sub.');
                    return $this->redirect(['achievement-config']);
                }

                $achievement = new ProgramAchievement([
                    'program_id' => $target['program_id'],
                    'program_sub' => $target['program_sub'],
                    'name' => $name,
                ]);
                if($hasWinnerCountColumn){
                    $achievement->winner_count = $winnerCount;
                }
                if($achievement->save()){
                    Yii::$app->session->addFlash('success', 'Achievement added.');
                }else{
                    Yii::$app->session->addFlash('error', implode('<br>', $achievement->getFirstErrors()));
                }

                return $this->redirect(['achievement-config']);
            }

            if($action === 'update' || $action === 'delete'){
                $achievement = ProgramAchievement::findOne($achievementId);
                if(!$achievement){
                    throw new NotFoundHttpException('Achievement not found.');
                }

                if($action === 'update'){
                    $achievement->name = trim((string)($post['name'] ?? ''));
                    if($hasWinnerCountColumn
                        && $this->hasProgramWinnerTitleAchievementColumn()
                        && $this->hasAssignedWinnerTitleBeyondCount($achievement, $winnerCount)
                    ){
                        Yii::$app->session->addFlash('error', 'Number of winner cannot be reduced because one or more removed winner titles are already assigned.');
                        return $this->redirect(['achievement-config']);
                    }
                    if($hasWinnerCountColumn){
                        $achievement->winner_count = $winnerCount;
                    }
                    if($achievement->save()){
                        $titlesSaved = true;
                        if($this->hasProgramWinnerTitleAchievementColumn()){
                            $titlesSaved = $this->saveAchievementWinnerTitles($achievement, $post['winner_titles'] ?? []);
                        }
                        if(!$titlesSaved){
                            Yii::$app->session->addFlash('error', 'Achievement updated, but one or more winner titles could not be saved.');
                        }
                        Yii::$app->session->addFlash('success', 'Achievement updated.');
                    }else{
                        Yii::$app->session->addFlash('error', implode('<br>', $achievement->getFirstErrors()));
                    }
                }else{
                    $usedCount = ParticipantAchieve::find()->where(['achieve_id' => $achievement->id])->count();
                    if($usedCount > 0){
                        Yii::$app->session->addFlash('error', 'Achievement cannot be deleted because it is already used.');
                    }else if($achievement->delete()){
                        Yii::$app->session->addFlash('success', 'Achievement deleted.');
                    }else{
                        Yii::$app->session->addFlash('error', 'Unable to delete achievement.');
                    }
                }

                return $this->redirect(['achievement-config']);
            }

            $selected = (array)Yii::$app->request->post('selection', []);
            if(!$selected){
                Yii::$app->session->addFlash('error', 'Please select achievement(s) to delete.');
                return $this->redirect(['achievement-config']);
            }

            $deleted = 0;
            $skipped = 0;
            foreach($selected as $id){
                $achievement = ProgramAchievement::findOne((int)$id);
                if(!$achievement){
                    continue;
                }

                $usedCount = ParticipantAchieve::find()->where(['achieve_id' => $achievement->id])->count();
                if($usedCount > 0){
                    $skipped++;
                    continue;
                }

                if($achievement->delete()){
                    $deleted++;
                }
            }

            if($deleted > 0){
                Yii::$app->session->addFlash('success', 'Deleted ' . $deleted . ' achievement(s).');
            }
            if($skipped > 0){
                Yii::$app->session->addFlash('error', 'Skipped ' . $skipped . ' achievement(s) because they are already used.');
            }

            return $this->redirect(['achievement-config']);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => ProgramAchievement::find()->alias('a')
                ->joinWith(['program p', 'programSub ps'])
                ->orderBy(['p.id' => SORT_ASC, 'ps.id' => SORT_ASC, 'a.name' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        $achievementModels = $dataProvider->getModels();
        $hasWinnerTitleTable = $this->hasProgramWinnerTitleTable();
        $hasWinnerTitleAchievementColumn = $this->hasProgramWinnerTitleAchievementColumn();
        $winnerTitlesByAchievement = [];
        if($hasWinnerTitleAchievementColumn && $achievementModels){
            $achievementIds = array_map(function($item){
                return (int)$item->id;
            }, $achievementModels);
            $winnerTitles = ProgramWinnerTitle::find()
                ->where(['achievement_id' => $achievementIds])
                ->orderBy(['achievement_id' => SORT_ASC, 'winner_order' => SORT_ASC])
                ->all();
            foreach($winnerTitles as $winnerTitle){
                $winnerTitlesByAchievement[(int)$winnerTitle->achievement_id][(int)$winnerTitle->winner_order] = $winnerTitle;
            }
        }

        return $this->render('achievement-config', [
            'dataProvider' => $dataProvider,
            'hasWinnerCountColumn' => $this->hasProgramAchievementWinnerCountColumn(),
            'hasWinnerTitleTable' => $hasWinnerTitleTable,
            'hasWinnerTitleAchievementColumn' => $hasWinnerTitleAchievementColumn,
            'winnerTitlesByAchievement' => $winnerTitlesByAchievement,
            'targetOptions' => $this->achievementTargetOptions(),
        ]);
    }

    public function actionAchievementImport()
    {
        if(Yii::$app->request->isPost){
            $file = UploadedFile::getInstanceByName('csv_file');
            if(!$file){
                Yii::$app->session->addFlash('error', 'Please choose a CSV file.');
                return $this->redirect(['achievement-import']);
            }

            $handle = fopen($file->tempName, 'r');
            if(!$handle){
                Yii::$app->session->addFlash('error', 'Unable to read CSV file.');
                return $this->redirect(['achievement-import']);
            }

            $header = fgetcsv($handle);
            if(!$header){
                fclose($handle);
                Yii::$app->session->addFlash('error', 'CSV file is empty.');
                return $this->redirect(['achievement-import']);
            }

            $header = array_map(function($value){
                return trim(strtolower(preg_replace('/^\xEF\xBB\xBF/', '', (string)$value)));
            }, $header);
            $index = array_flip($header);
            $required = ['program_id', 'program_sub', 'achievement_name', 'winner_count'];
            foreach($required as $field){
                if(!array_key_exists($field, $index)){
                    fclose($handle);
                    Yii::$app->session->addFlash('error', 'CSV header must be: program_id, program_sub, achievement_name, winner_count.');
                    return $this->redirect(['achievement-import']);
                }
            }

            $created = 0;
            $skipped = 0;
            $errors = [];
            $line = 1;
            $lastProgramId = null;
            $lastProgramSubId = null;
            while(($row = fgetcsv($handle)) !== false){
                $line++;
                $programRaw = trim((string)($row[$index['program_id']] ?? ''));
                if($programRaw !== ''){
                    $lastProgramId = (int)$programRaw;
                    $lastProgramSubId = null;
                }
                $programId = $lastProgramId;
                $programSubRaw = trim((string)($row[$index['program_sub']] ?? ''));
                if($programSubRaw !== ''){
                    $lastProgramSubId = (int)$programSubRaw;
                }
                $programSubId = $lastProgramSubId;
                $name = trim((string)($row[$index['achievement_name']] ?? ''));
                $winnerCount = null;
                if(array_key_exists('winner_count', $index)){
                    $winnerRaw = trim((string)($row[$index['winner_count']] ?? ''));
                    $winnerCount = $winnerRaw === '' ? null : (int)$winnerRaw;
                }

                if(!$programId && $name === ''){
                    continue;
                }

                if(!$programId || $name === ''){
                    $errors[] = 'Line ' . $line . ': program_id is required unless a previous row already has one, and achievement_name is required.';
                    continue;
                }

                $program = Program::findOne($programId);
                if(!$program || !$this->isProgramActive($program)){
                    $errors[] = 'Line ' . $line . ': program_id is not active or does not exist.';
                    continue;
                }

                if($programSubId !== null){
                    $programSub = ProgramSub::findOne($programSubId);
                    if(!$programSub || (int)$programSub->program_id !== $programId || !$this->isProgramSubActive($programSub)){
                        $errors[] = 'Line ' . $line . ': program_sub is not active, does not exist, or does not belong to the program.';
                        continue;
                    }
                }

                $duplicateQuery = ProgramAchievement::find()->where([
                    'program_id' => $programId,
                    'name' => $name,
                ]);
                if($programSubId === null){
                    $duplicateQuery->andWhere(['program_sub' => null]);
                }else{
                    $duplicateQuery->andWhere(['program_sub' => $programSubId]);
                }

                if($duplicateQuery->exists()){
                    $skipped++;
                    continue;
                }

                $achievement = new ProgramAchievement([
                    'program_id' => $programId,
                    'program_sub' => $programSubId,
                    'name' => $name,
                ]);
                if($this->hasProgramAchievementWinnerCountColumn()){
                    $achievement->winner_count = $winnerCount;
                }
                if($achievement->save()){
                    $created++;
                }else{
                    $errors[] = 'Line ' . $line . ': ' . implode(', ', $achievement->getFirstErrors());
                }
            }
            fclose($handle);

            Yii::$app->session->addFlash('success', 'CSV import completed. Created: ' . $created . '. Skipped duplicates: ' . $skipped . '.');
            if($errors){
                Yii::$app->session->addFlash('error', implode('<br>', array_slice($errors, 0, 20)) . (count($errors) > 20 ? '<br>Only first 20 errors shown.' : ''));
            }

            return $this->redirect(['achievement-import']);
        }

        return $this->render('achievement-import', [
            'guidelines' => $this->activeProgramSubGuidelines(),
            'hasWinnerCountColumn' => $this->hasProgramAchievementWinnerCountColumn(),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $submitAction = Yii::$app->request->post('submit_action', 'save');
            $model->template_upload = UploadedFile::getInstance($model, 'template_upload');

            if ($model->validate()) {
                if ($model->template_upload) {
                    $uploadBase = Yii::getAlias('@webroot/images');
                    if (!is_dir($uploadBase)) {
                        @mkdir($uploadBase, 0775, true);
                    }

                    $fileName = 'cert_template_' . $model->id . '_' . date('YmdHis') . '_' . Yii::$app->security->generateRandomString(8) . '.' . $model->template_upload->extension;
                    $fullPath = $uploadBase . DIRECTORY_SEPARATOR . $fileName;
                    if ($model->template_upload->saveAs($fullPath)) {
                        $model->template_file = $fileName;
                    } else {
                        $model->addError('template_upload', 'Unable to save uploaded template file.');
                    }
                }

                if (!$model->hasErrors()) {
                    $model->updated_at = date('Y-m-d H:i:s');
                    if ($model->save(false)) {
                        Yii::$app->session->addFlash('success', 'Certificate template updated.');

                        if ($submitAction === 'save-back') {
                            return $this->redirect(['index']);
                        }

                        return $this->redirect(['update', 'id' => $model->id]);
                    }

                    $model->addError('id', 'Unable to save certificate template to the database.');
                }
            }

            if ($model->hasErrors()) {
                Yii::$app->session->addFlash('error', implode('<br>', $model->getFirstErrors()));
            } else {
                Yii::$app->session->addFlash('error', 'Failed to update certificate template.');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    protected function findModel($id)
    {
        $model = CertificateTemplate::findOne((int)$id);
        if ($model === null) {
            throw new NotFoundHttpException('The requested certificate template does not exist.');
        }

        return $model;
    }

    protected function isProgramActive($program)
    {
        $table = Yii::$app->db->schema->getTableSchema(Program::tableName());
        if($table && $table->getColumn('is_active')){
            return (int)$program->getAttribute('is_active') === 1;
        }
        if($table && $table->getColumn('status')){
            return (int)$program->getAttribute('status') === 10;
        }

        return true;
    }

    protected function isProgramSubActive($programSub)
    {
        $table = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
        if($table && $table->getColumn('is_active')){
            return (int)$programSub->getAttribute('is_active') === 1;
        }

        return true;
    }

    protected function activeProgramSubGuidelines()
    {
        $programQuery = Program::find()->orderBy(['id' => SORT_ASC]);
        $programTable = Yii::$app->db->schema->getTableSchema(Program::tableName());
        if($programTable && $programTable->getColumn('is_active')){
            $programQuery->andWhere(['is_active' => 1]);
        }else if($programTable && $programTable->getColumn('status')){
            $programQuery->andWhere(['status' => 10]);
        }

        $rows = [];
        foreach($programQuery->all() as $program){
            if((int)$program->has_sub === 1){
                $subQuery = $program->getProgramSubs()->orderBy(['id' => SORT_ASC]);
                $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
                if($subTable && $subTable->getColumn('is_active')){
                    $subQuery->andWhere(['is_active' => 1]);
                }
                foreach($subQuery->all() as $sub){
                    $rows[] = [
                        'program_id' => $program->id,
                        'program_sub' => $sub->id,
                        'program' => $program->program_abbr ?: $program->program_name,
                        'sub' => $sub->sub_name,
                    ];
                }
            }else{
                $rows[] = [
                    'program_id' => $program->id,
                    'program_sub' => '',
                    'program' => $program->program_abbr ?: $program->program_name,
                ];
            }
        }

        return $rows;
    }

    protected function hasProgramAchievementWinnerCountColumn()
    {
        $table = Yii::$app->db->schema->getTableSchema(ProgramAchievement::tableName());
        return $table && $table->getColumn('winner_count');
    }

    protected function hasProgramWinnerTitleTable()
    {
        return Yii::$app->db->schema->getTableSchema(ProgramWinnerTitle::tableName()) !== null;
    }

    protected function hasProgramWinnerTitleAchievementColumn()
    {
        $table = Yii::$app->db->schema->getTableSchema(ProgramWinnerTitle::tableName());
        return $table && $table->getColumn('achievement_id') && $table->getColumn('winner_order');
    }

    protected function saveAchievementWinnerTitles(ProgramAchievement $achievement, $titles)
    {
        $winnerCount = max(0, (int)$achievement->winner_count);
        if($this->hasAssignedWinnerTitleBeyondCount($achievement, $winnerCount)){
            return false;
        }

        ProgramWinnerTitle::deleteAll([
            'and',
            ['achievement_id' => (int)$achievement->id],
            ['>', 'winner_order', $winnerCount],
        ]);

        $existingTitles = ProgramWinnerTitle::find()
            ->where(['achievement_id' => (int)$achievement->id])
            ->indexBy('winner_order')
            ->all();

        $ok = true;
        for($i = 1; $i <= $winnerCount; $i++){
            $titleName = trim((string)($titles[$i] ?? ''));
            $winnerTitle = $existingTitles[$i] ?? null;
            if(!$winnerTitle){
                $winnerTitle = new ProgramWinnerTitle([
                    'achievement_id' => (int)$achievement->id,
                    'winner_order' => $i,
                ]);
            }
            $winnerTitle->title_name = $titleName;
            if(!$winnerTitle->save()){
                $ok = false;
            }
        }

        return $ok;
    }

    protected function hasAssignedWinnerTitleBeyondCount(ProgramAchievement $achievement, $winnerCount)
    {
        if(!$this->hasParticipantAchieveWinnerTitleColumn()){
            return false;
        }

        $winnerCount = max(0, (int)$winnerCount);
        return ParticipantAchieve::find()->alias('pa')
            ->innerJoin(ProgramWinnerTitle::tableName() . ' pwt', 'pwt.id = pa.winner_title_id')
            ->where(['pwt.achievement_id' => (int)$achievement->id])
            ->andWhere(['>', 'pwt.winner_order', $winnerCount])
            ->exists();
    }

    protected function hasParticipantAchieveWinnerTitleColumn()
    {
        $table = Yii::$app->db->schema->getTableSchema(ParticipantAchieve::tableName());
        return $table && $table->getColumn('winner_title_id');
    }

    protected function bulkSaveWinnerTitlesByCount($winnerCount, $titles)
    {
        $winnerCount = max(0, (int)$winnerCount);
        $achievements = ProgramAchievement::find()
            ->where(['winner_count' => $winnerCount])
            ->all();

        $updated = 0;
        $errors = 0;
        foreach($achievements as $achievement){
            $existingTitles = ProgramWinnerTitle::find()
                ->where(['achievement_id' => (int)$achievement->id])
                ->indexBy('winner_order')
                ->all();

            $achievementOk = true;
            for($i = 1; $i <= $winnerCount; $i++){
                $winnerTitle = $existingTitles[$i] ?? null;
                if(!$winnerTitle){
                    $winnerTitle = new ProgramWinnerTitle([
                        'achievement_id' => (int)$achievement->id,
                        'winner_order' => $i,
                    ]);
                }
                $winnerTitle->title_name = trim((string)($titles[$i] ?? ''));
                if(!$winnerTitle->save()){
                    $achievementOk = false;
                    $errors++;
                }
            }

            if($achievementOk){
                $updated++;
            }
        }

        return [
            'updated' => $updated,
            'errors' => $errors,
        ];
    }

    protected function achievementTargetOptions()
    {
        $options = [];
        foreach($this->activeProgramSubGuidelines() as $row){
            $value = (int)$row['program_id'] . '|' . (string)$row['program_sub'];
            $label = $row['program'] . (isset($row['sub']) && $row['sub'] !== '' ? ' / ' . $row['sub'] : '');
            $options[$value] = $label;
        }

        return $options;
    }

    protected function parseAchievementTarget($target)
    {
        $parts = explode('|', $target, 2);
        if(count($parts) !== 2 || trim($parts[0]) === ''){
            return null;
        }

        $programId = (int)$parts[0];
        $program = Program::findOne($programId);
        if(!$program || !$this->isProgramActive($program)){
            return null;
        }

        $programSubId = trim($parts[1]) === '' ? null : (int)$parts[1];
        if($programSubId !== null){
            $programSub = ProgramSub::findOne($programSubId);
            if(!$programSub || (int)$programSub->program_id !== $programId || !$this->isProgramSubActive($programSub)){
                return null;
            }
        }

        return [
            'program_id' => $programId,
            'program_sub' => $programSubId,
        ];
    }
}
