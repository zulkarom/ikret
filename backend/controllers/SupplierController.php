<?php

namespace backend\controllers;

use Yii;
use backend\models\Supplier;
use backend\models\SupplierProfile;
use backend\models\SupplierSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use backend\models\UploadFile;
use common\models\User;
use yii\db\Query;
/**
 * SupplierController implements the CRUD actions for Supplier model.
 */
class SupplierController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionSupplierListJson($q = null, $id = null) {
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('supplier.id, user.fullname AS text')
            ->from('supplier')
            ->leftJoin('user', 'supplier.user_id = user.id')
            ->where(['like', 'user.fullname', $q])
            ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Supplier::findOne($id)->user->username];
        }
        return $out;
    }

    /**
     * Lists all Supplier models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SupplierSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Supplier model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Supplier model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Supplier();
        $modelUser = new User();

        $model->setScenario('admin_insert');
        $modelUser->setScenario('create');
        if ($model->load(Yii::$app->request->post()) && $modelUser->load(Yii::$app->request->post())) {

            //kena find dah ada user ke belum
            $user = User::findOne(['email' => $modelUser->email]);
            if($user){
                $modelUser = $user;
                $modelUser->updated_at = time();
            }else{
                $modelUser->role = 2;
                $modelUser->status = 10;
                $modelUser->username = $modelUser->email;
                if($modelUser->rawPassword){
                    $modelUser->setPassword($modelUser->rawPassword);
                }else{
                    $modelUser->setPassword(Yii::$app->security->generateRandomString(5));
                }
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                
                if($modelUser->save()){
                    $model->user_id = $modelUser->id;
                    if($model->save()){
                        $transaction->commit();
                        Yii::$app->session->addFlash('success', "A new supplier added");
                        return $this->redirect(['view', 'id' => $model->id]);
                    }else{
                        $model->flashError();
                    }
                }else{
                    $modelUser->flashError();
                }
            
            }
            catch (\Exception $e)
            {
                $transaction->rollBack();
                Yii::$app->session->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $model,
            'modelUser' => $modelUser,
        ]);
    }

    /**
     * Updates an existing Supplier model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        // $model = $this->findModel($id);
        $model = $this->findModel($id);
        $modelUser = User::findOne($model->user_id);

        $modelUser->scenario = 'update';
        $model->scenario = 'admin_insert';

        if ($modelUser->load(Yii::$app->request->post()) 
            && $model->load(Yii::$app->request->post())) {

            $modelUser->username = $modelUser->email;
            if($modelUser->rawPassword){
                $modelUser->setPassword($modelUser->rawPassword);
            }            
            
            if($modelUser->save()){
                if($model->save()){
                    Yii::$app->session->addFlash('success', "Data Updated");
                    return $this->redirect(['view', 'id' => $id]);
                }else{
                    $model->flashError();
                }
            }else{
                $modelUser->flashError();
            }
        }

        return $this->render('update', [
            'model' => $model,
            'modelUser' => $modelUser,
        ]);
    }

    /**
     * Deletes an existing Supplier model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        try {
            $model->delete();
        } catch(\yii\db\IntegrityException $e) {
            throw new \yii\web\ForbiddenHttpException('Could not delete this record (Integrity constraint violation)');
        }
        
        return $this->redirect(['index']);

    }

    public function actionProfileImage($id){
        $model = $this->findModel($id);
        
        UploadFile::profileImage(2,$model);
    }

    /**
     * Finds the Supplier model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Supplier the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Supplier::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findSupplierProfile($id)
    {
        if (($model = SupplierProfile::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
