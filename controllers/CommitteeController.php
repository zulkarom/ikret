<?php

namespace app\controllers;

use app\models\ProgramRegistration;
use app\models\UserRole;
use app\models\LetterPdf;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ProgramRegistrationController implements the CRUD actions for ProgramRegistration model.
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
        ];
    }

    /**
     * Lists all ProgramRegistration models.
     *
     * @return string
     */
    public function actionRequest()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => UserRole::find()->where(['<>', 'role_name', 'participant']),
            'pagination' => [
                'pageSize' => 100
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

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

    public function actionLetter(){
        if(!Yii::$app->user->identity->isCommittee)return;

        $dataProvider = new ActiveDataProvider([
            'query' => UserRole::find()
            ->where(['=', 'role_name', 'committee'])
            ->andWhere(['user_id' => Yii::$app->user->identity->id])
        ]);

        return $this->render('letter', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLetterPdf($id){
        $model = $this->findRole($id);
        $pdf = new LetterPdf;
        $pdf->model = $model;
        $pdf->generatePdf();
        exit;
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

    public function actionDeleteRole($id)
    {
        $this->findRole($id)->delete();
        Yii::$app->session->addFlash('success', "Role Deleted");

        return $this->redirect(['request']);
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

    protected function findRole($id)
    {
        if (($model = UserRole::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
