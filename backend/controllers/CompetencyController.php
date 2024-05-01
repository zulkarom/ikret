<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use backend\models\Competency;
use backend\models\CompetencySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CompetencyController implements the CRUD actions for Competency model.
 */
class CompetencyController extends Controller
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
     * Lists all Competency models.
     * @return mixed
     */
    public function actionIndex()
    {
        
        $request = Yii::$app->request;
        if($request->get('ent_id')){
            $searchModel = new CompetencySearch(['entrepreneur_id' => $request->get('ent_id')]);
        }else{
            $searchModel = new CompetencySearch();
        }

        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Competency model.
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
     * Creates a new Competency model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($b=false)
    {
        $model = new Competency();

        if($b == true){
            $request = Yii::$app->request;
            $model->entrepreneur_id = $request->get('ent_id');
        }

        if ($model->load(Yii::$app->request->post())) {

            if($model->save()){
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
     * Updates an existing Competency model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Competency model.
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
     * Finds the Competency model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Competency the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Competency::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
