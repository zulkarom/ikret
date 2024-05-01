<?php
namespace backend\controllers;

use backend\models\AgencySearch;
use backend\models\CompetencySearch;
use backend\models\EconomicSearch;
use backend\models\Entrepreneur;
use backend\models\EntrepreneurProfile;
use backend\models\EntrepreneurSearch;
use backend\models\ProgramSearch;
use backend\models\SectorEntrepreneurSearch;
use backend\models\SocialImpactSearch;
use backend\models\UploadFile;
use common\models\User;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * EntrepreneurController implements the CRUD actions for Entrepreneur model.
 */
class EntrepreneurController extends Controller
{

    /**
     *
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
                        'roles' => [
                            '@'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function actionEntrepreneurListJson($q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $out = [
            'results' => [
                'id' => '',
                'text' => ''
            ]
        ];
        if (! is_null($q)) {
            $query = new Query();
            $query->select('entrepreneur.id, user.fullname AS text')
                ->from('entrepreneur')
                ->leftJoin('user', 'entrepreneur.user_id = user.id')
                ->where([
                'like',
                'user.fullname',
                $q
            ])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id > 0) {
            $out['results'] = [
                'id' => $id,
                'text' => Entrepreneur::findOne($id)->user->username
            ];
        }
        return $out;
    }

    /**
     * Lists all Entrepreneur models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EntrepreneurSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single Entrepreneur model.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $searchModelSector = new SectorEntrepreneurSearch([
            'entrepreneur_id' => $id
        ]);
        $searchModelSector->limit = 5;
        $dataProviderSector = $searchModelSector->search(Yii::$app->request->queryParams);

        $searchModelCompetency = new CompetencySearch([
            'entrepreneur_id' => $id
        ]);
        $searchModelCompetency->limit = 5;
        $dataProviderCompetency = $searchModelCompetency->search(Yii::$app->request->queryParams);

        $searchModelSocial = new SocialImpactSearch([
            'entrepreneur_id' => $id
        ]);
        $searchModelSocial->limit = 5;
        $dataProviderSocial = $searchModelSocial->search(Yii::$app->request->queryParams);

        $searchModelEconomic = new EconomicSearch([
            'entrepreneur_id' => $id
        ]);
        $searchModelEconomic->limit = 5;
        $dataProviderEconomic = $searchModelEconomic->search(Yii::$app->request->queryParams);

        $searchModelAgency = new AgencySearch([
            'entrepreneur_id' => $id
        ]);
        $searchModelAgency->limit = 5;
        $dataProviderAgency = $searchModelAgency->search(Yii::$app->request->queryParams);

        $searchModelProgram = new ProgramSearch([
            'entrepreneur_id' => $id
        ]);
        $searchModelProgram->limit = 5;
        $dataProviderProgram = $searchModelProgram->search(Yii::$app->request->queryParams);

        return $this->render('view', [
            'searchModelSector' => $searchModelSector,
            'dataProviderSector' => $dataProviderSector,
            'searchModelCompetency' => $searchModelCompetency,
            'dataProviderCompetency' => $dataProviderCompetency,
            'searchModelSocial' => $searchModelSocial,
            'dataProviderSocial' => $dataProviderSocial,
            'searchModelEconomic' => $searchModelEconomic,
            'dataProviderEconomic' => $dataProviderEconomic,
            'searchModelAgency' => $searchModelAgency,
            'dataProviderAgency' => $dataProviderAgency,
            'searchModelProgram' => $searchModelProgram,
            'dataProviderProgram' => $dataProviderProgram,
            'model' => $this->findModel($id)
        ]);
    }

    public function actionViewEdit($id)
    {
        $searchModelSector = new SectorEntrepreneurSearch([
            'entrepreneur_id' => $id
        ]);
        $searchModelSector->limit = 5;
        $dataProviderSector = $searchModelSector->search(Yii::$app->request->queryParams);

        $searchModelCompetency = new CompetencySearch([
            'entrepreneur_id' => $id
        ]);
        $searchModelCompetency->limit = 5;
        $dataProviderCompetency = $searchModelCompetency->search(Yii::$app->request->queryParams);

        $searchModelSocial = new SocialImpactSearch([
            'entrepreneur_id' => $id
        ]);
        $searchModelSocial->limit = 5;
        $dataProviderSocial = $searchModelSocial->search(Yii::$app->request->queryParams);

        $searchModelEconomic = new EconomicSearch([
            'entrepreneur_id' => $id
        ]);
        $searchModelEconomic->limit = 5;
        $dataProviderEconomic = $searchModelEconomic->search(Yii::$app->request->queryParams);

        $searchModelAgency = new AgencySearch([
            'entrepreneur_id' => $id
        ]);
        $searchModelAgency->limit = 5;
        $dataProviderAgency = $searchModelAgency->search(Yii::$app->request->queryParams);

        $searchModelProgram = new ProgramSearch([
            'entrepreneur_id' => $id
        ]);
        $searchModelProgram->limit = 5;
        $dataProviderProgram = $searchModelProgram->search(Yii::$app->request->queryParams);

        return $this->render('view-edit', [
            'searchModelSector' => $searchModelSector,
            'dataProviderSector' => $dataProviderSector,
            'searchModelCompetency' => $searchModelCompetency,
            'dataProviderCompetency' => $dataProviderCompetency,
            'searchModelSocial' => $searchModelSocial,
            'dataProviderSocial' => $dataProviderSocial,
            'searchModelEconomic' => $searchModelEconomic,
            'dataProviderEconomic' => $dataProviderEconomic,
            'searchModelAgency' => $searchModelAgency,
            'dataProviderAgency' => $dataProviderAgency,
            'searchModelProgram' => $searchModelProgram,
            'dataProviderProgram' => $dataProviderProgram,
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Creates a new Entrepreneur model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Entrepreneur();
        $modelUser = new User();

        $model->setScenario('admin_insert');
        $modelUser->setScenario('create');

        if (Yii::$app->request->isAjax) {
            $model->load($_POST);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        } else if ($model->load(Yii::$app->request->post())) {

            // kena find dah ada user ke belum
            $user = User::findOne([
                'username' => $model->username
            ]);
            if ($user) {
                $modelUser = $user;
                $modelUser->updated_at = time();
            } else {
                $modelUser->fullname = $model->fullname;
                $modelUser->username = $model->username;
                $modelUser->email = $model->email;
                $modelUser->nric = $model->nric;
                $modelUser->role = 1;
                $modelUser->status = 10;
                if ($model->password) {
                    $modelUser->setPassword($model->password);
                } else {
                    $modelUser->setPassword(Yii::$app->security->generateRandomString(5));
                }
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {

                if ($modelUser->save()) {

                    // $bene = Entrepreneur::findOne(['user_id' => $modelUser->id]);
                    $model->user_id = $modelUser->id;
                    if ($model->save()) {

                        $transaction->commit();
                        Yii::$app->session->addFlash('success', "A new beneficiary created");
                        return $this->redirect([
                            'view',
                            'id' => $model->id
                        ]);
                    } else {
                        $model->flashError();
                    }
                } else {

                    $modelUser->flashError();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $model,
            'modelUser' => $modelUser
        ]);
    }

    /**
     * Updates an existing Entrepreneur model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        // $model = $this->findModel($id);
        $model = $this->findModel($id);
        $modelUser = $model->user;
        $model->fullname = $modelUser->fullname;
        $model->username = $modelUser->username;
        $model->email = $modelUser->email;
        $model->nric = $modelUser->nric;

        $modelUser->scenario = 'update';
        $model->scenario = 'admin_insert';

        if (Yii::$app->request->isAjax) {
            $model->load($_POST);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        } else if ($model->load(Yii::$app->request->post())) {

            // $modelUser->username = $modelUser->email;
            if ($modelUser->rawPassword) {
                $modelUser->setPassword($modelUser->rawPassword);
            }

            $modelUser->fullname = $model->fullname;
            $modelUser->username = $model->username;
            $modelUser->email = $model->email;
            $modelUser->nric = $model->nric;

            if ($modelUser->save()) {
                if ($model->save()) {
                    Yii::$app->session->addFlash('success', "Data Updated");
                    return $this->redirect([
                        'view',
                        'id' => $id
                    ]);
                } else {
                    $model->flashError();
                }
            } else {
                $modelUser->flashError();
            }
        }

        return $this->render('update', [
            'model' => $model,
            'modelUser' => $modelUser
        ]);
    }

    /**
     * Deletes an existing Entrepreneur model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        try {
            $model->delete();
        } catch (\yii\db\IntegrityException $e) {
            throw new \yii\web\ForbiddenHttpException('Could not delete this record (Integrity constraint violation)');
        }

        return $this->redirect([
            'index'
        ]);
    }

    public function actionProfileImage($id)
    {
        $model = $this->findModel($id);

        UploadFile::profileImage(1, $model);
    }

    /**
     * Finds the Entrepreneur model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Entrepreneur the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Entrepreneur::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findEntrepreneurProfile($id)
    {
        if (($model = EntrepreneurProfile::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
