<?php

namespace app\controllers;

use app\models\JuryRequirement;
use app\models\AppSetting;
use app\models\Program;
use app\models\ProgramRubric;
use app\models\ProgramSub;
use app\models\RubricJudgingSession;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class JuryRequirementController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdminJury;
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => JuryRequirement::find()->orderBy(['program_id' => SORT_ASC, 'program_sub_id' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'callForJuriesEnabled' => AppSetting::getBool('call_for_juries_enabled', true),
        ]);
    }

    public function actionToggleCallForJuries()
    {
        if(!Yii::$app->request->isPost){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $enabled = AppSetting::getBool('call_for_juries_enabled', true);
        $newValue = $enabled ? '0' : '1';

        if(AppSetting::setValue('call_for_juries_enabled', $newValue)){
            Yii::$app->session->addFlash('success', 'Updated');
        }
        return $this->redirect(['index']);
    }

    public function actionCreate()
    {
        $model = new JuryRequirement();

        if(!Yii::$app->request->isPost){
            $model->program_id = Yii::$app->request->get('program_id');
            $model->program_sub_id = Yii::$app->request->get('program_sub_id');
            $model->judging_session_id = Yii::$app->request->get('judging_session_id');
        }

        if(Yii::$app->request->isPost && $model->load(Yii::$app->request->post())){
            $model->created_at = time();
            $model->updated_at = time();
            if($model->save()){
                Yii::$app->session->addFlash('success', 'Saved');
                return $this->redirect(['index']);
            }
            $model->flashError();
        }

        $programSubCombinedList = $this->buildProgramSubCombinedList();
        $sessionList = $this->buildSessionList($model->program_id, $model->program_sub_id);

        return $this->render('create', [
            'model' => $model,
            'programSubCombinedList' => $programSubCombinedList,
            'sessionList' => $sessionList,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if(!Yii::$app->request->isPost){
            $getProgramId = Yii::$app->request->get('program_id');
            $getProgramSubId = Yii::$app->request->get('program_sub_id');
            if($getProgramId !== null){
                $model->program_id = $getProgramId;
                $model->program_sub_id = $getProgramSubId;
                $model->judging_session_id = Yii::$app->request->get('judging_session_id');
            }
        }

        if(Yii::$app->request->isPost && $model->load(Yii::$app->request->post())){
            $model->updated_at = time();
            if($model->save()){
                Yii::$app->session->addFlash('success', 'Saved');
                return $this->redirect(['index']);
            }
            $model->flashError();
        }

        $programSubCombinedList = $this->buildProgramSubCombinedList();
        $sessionList = $this->buildSessionList($model->program_id, $model->program_sub_id);

        return $this->render('update', [
            'model' => $model,
            'programSubCombinedList' => $programSubCombinedList,
            'sessionList' => $sessionList,
        ]);
    }

    private function buildProgramSubCombinedList(): array
    {
        $query = Program::find();
        $programTable = Yii::$app->db->schema->getTableSchema(Program::tableName());
        if($programTable && $programTable->getColumn('is_active')){
            $query->andWhere(['is_active' => 1]);
        }else if($programTable && $programTable->getColumn('status')){
            $query->andWhere(['status' => 10]);
        }

        $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
        $query->with(['programSubs' => function($q) use ($subTable){
            if($subTable && $subTable->getColumn('is_active')){
                $q->andWhere(['is_active' => 1]);
            }
            $q->orderBy(['sub_name' => SORT_ASC]);
        }]);

        $programs = $query->orderBy(['program_name' => SORT_ASC])->all();
        $out = [];
        foreach($programs as $program){
            $out['p:' . $program->id] = $program->program_name;
            if($program->programSubs){
                foreach($program->programSubs as $sub){
                    $out['s:' . $sub->id] = $program->program_name . ' / ' . $sub->sub_name;
                }
            }
        }
        return $out;
    }

    private function buildSessionList(?int $programId, ?int $programSubId): array
    {
        if(!$programId){
            return [];
        }

        $rubricQuery = ProgramRubric::find()->select(['rubric_id'])->where(['program_id' => $programId]);
        if($programSubId){
            $rubricQuery->andWhere(['program_sub' => $programSubId]);
        }

        $rubricIds = $rubricQuery->column();
        if(!$rubricIds){
            return [];
        }

        $sessions = RubricJudgingSession::find()
            ->where(['rubric_id' => $rubricIds])
            ->orderBy(['datetime_start' => SORT_ASC, 'session_name' => SORT_ASC])
            ->all();

        $out = [];
        foreach($sessions as $s){
            $out[$s->id] = $this->formatSessionLabel($s);
        }
        return $out;
    }

    private function formatSessionLabel(RubricJudgingSession $session): string
    {
        $start = $session->datetime_start ? new \DateTime($session->datetime_start) : null;
        $end = $session->datetime_end ? new \DateTime($session->datetime_end) : null;

        $range = '';
        if($start && $end){
            if($start->format('Y-m-d') === $end->format('Y-m-d')){
                $range = $start->format('d M Y') . ' ' . $start->format('H:i') . ' - ' . $end->format('H:i');
            }else{
                $range = $start->format('d M Y H:i') . ' - ' . $end->format('d M Y H:i');
            }
        }elseif($start){
            $range = $start->format('d M Y H:i');
        }

        if($range !== ''){
            return $session->session_name . ' (' . $range . ')';
        }

        return $session->session_name;
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->session->addFlash('info', 'Deleted');
        return $this->redirect(['index']);
    }

    protected function findModel($id): JuryRequirement
    {
        $model = JuryRequirement::findOne((int)$id);
        if($model){
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
