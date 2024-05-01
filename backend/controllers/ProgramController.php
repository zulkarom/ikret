<?php

namespace backend\controllers;

use Yii;
use backend\models\Program;
use backend\models\ProgramSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\db\Expression;
/**
 * ProgramController implements the CRUD actions for Program model.
 */
class ProgramController extends Controller
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

    /**
     * Lists all Program models.
     * @return mixed
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        if($request->get('ent_id')){
            $searchModel = new ProgramSearch(['entrepreneur_id' => $request->get('ent_id')]);
        }else{
            $searchModel = new ProgramSearch();
        }

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Program model.
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
     * Creates a new Program model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($b=false)
    {
        $model = new Program();

        if($b == true){
            $request = Yii::$app->request;
            $model->entrepreneur_id = $request->get('ent_id');
        }

        if ($model->load(Yii::$app->request->post())) {
            if($model->prog_category != 1){
                $model->prog_other = "";
            }
            if($model->prog_anjuran != 2){
                $model->anjuran_other = "";
            }
            $model->created_at = new Expression('NOW()');
            if($model->save()){
                Yii::$app->session->addFlash('success', "Data Saved");
                if($b == true){
                    return $this->redirect(['/entrepreneur/view', 'id' => $model->entrepreneur_id]);
                }else{
                    return $this->redirect(['view', 'id' => $model->id]);   
                }
                
            }
            
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Program model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if($model->prog_category != 1){
                $model->prog_other = "";
            }
            if($model->prog_anjuran != 2){
                $model->anjuran_other = "";
            }
            $model->updated_at = new Expression('NOW()');
            if($model->save()){
                Yii::$app->session->addFlash('success', "Data Updated");

                return $this->redirect(['view', 'id' => $model->id]);
            }
            else{
                $model->flashError();
            }
            
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Program model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
}
