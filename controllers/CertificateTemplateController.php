<?php

namespace app\controllers;

use app\models\CertificateTemplate;
use app\models\JuryAssign;
use app\models\ParticipantAchieve;
use app\models\Program;
use app\models\ProgramAchievement;
use app\models\ProgramRegistration;
use app\models\ProgramSub;
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
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin;
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

        return $this->render('achievement-config', [
            'dataProvider' => $dataProvider,
            'hasWinnerCountColumn' => $this->hasProgramAchievementWinnerCountColumn(),
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
}
