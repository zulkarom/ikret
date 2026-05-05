<?php

namespace app\controllers;

use app\models\JuryRequirement;
use app\models\AppSetting;
use app\models\Program;
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
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin;
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

        if(Yii::$app->request->isPost && $model->load(Yii::$app->request->post())){
            $model->created_at = time();
            $model->updated_at = time();
            if($model->save()){
                Yii::$app->session->addFlash('success', 'Saved');
                return $this->redirect(['index']);
            }
            $model->flashError();
        }

        $programList = ArrayHelper::map(Program::find()->orderBy(['program_name' => SORT_ASC])->all(), 'id', 'program_name');
        $subList = ArrayHelper::map(ProgramSub::find()->orderBy(['sub_name' => SORT_ASC])->all(), 'id', 'sub_name');
        $sessionList = ArrayHelper::map(RubricJudgingSession::find()->orderBy(['session_name' => SORT_ASC])->all(), 'id', 'session_name');

        return $this->render('create', [
            'model' => $model,
            'programList' => $programList,
            'subList' => $subList,
            'sessionList' => $sessionList,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if(Yii::$app->request->isPost && $model->load(Yii::$app->request->post())){
            $model->updated_at = time();
            if($model->save()){
                Yii::$app->session->addFlash('success', 'Saved');
                return $this->redirect(['index']);
            }
            $model->flashError();
        }

        $programList = ArrayHelper::map(Program::find()->orderBy(['program_name' => SORT_ASC])->all(), 'id', 'program_name');
        $subList = ArrayHelper::map(ProgramSub::find()->orderBy(['sub_name' => SORT_ASC])->all(), 'id', 'sub_name');
        $sessionList = ArrayHelper::map(RubricJudgingSession::find()->orderBy(['session_name' => SORT_ASC])->all(), 'id', 'session_name');

        return $this->render('update', [
            'model' => $model,
            'programList' => $programList,
            'subList' => $subList,
            'sessionList' => $sessionList,
        ]);
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
