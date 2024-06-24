<?php

namespace app\controllers;

use app\models\CertificateCommittee;
use app\models\CertificateTemplate;
use app\models\Committee;
use app\models\ProgramRegistration;
use app\models\UserRole;
use app\models\LetterPdf;
use app\models\RoleRequestSearch;
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

    public function actionIndex()
    {
        if(!Yii::$app->user->identity->isAdmin) return false;
        $list = Committee::find()->all();
        return $this->render('list', [
            'list' => $list,
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
        if(!$this->canAccessDoc($id)) return false;

        $pdf = new CertificateCommittee;
        $role = $this->findRole($id);
        $pdf->template = CertificateTemplate::findOne(2);
        $pdf->model = $role;
        $pdf->generatePdf();
        exit;
    }

    private function canAccessDoc($id){
        if(Yii::$app->user->identity->isManager){
            return true;
        }else{
            $role = $this->findRole($id);
            if($role->user_id == Yii::$app->user->identity->id){
                return true;
            }
        }
        return false;
    }

    public function actionCertificatePage()
    {
        if(!Yii::$app->user->identity->isCommittee) return false;

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
