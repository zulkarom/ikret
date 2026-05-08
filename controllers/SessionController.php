<?php

namespace app\controllers;

use app\models\CertificateQr;
use app\models\CertificateTemplate;
use app\models\Session;
use app\models\SessionAttendance;
use app\models\SessionAttendanceSearch;
use app\models\SessionQr;
use app\models\SessionSearch;
use app\models\Setting;
use app\models\User;
use Yii;
use yii\db\Expression;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * SessionController implements the CRUD actions for Session model.
 */
class SessionController extends Controller
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'attendance-delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Session models.
     *
     * @return string
     */
    public function actionIndex()
    {
        if(!Yii::$app->user->identity->isManager && !Yii::$app->user->identity->isAdminRegistration) return false;
        $searchModel = new SessionSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionParticipant()
    {
        date_default_timezone_set("Asia/Kuala_Lumpur");

        $list = SessionAttendance::find()
        ->where(['user_id' => Yii::$app->user->identity->id])
        ->orderBy('id DESC')
        ->all();

        return $this->render('participant', [
            'list' => $list,
        ]);
    }

    public function actionAttendance()
    {
        if(!Yii::$app->user->identity->isManager && !Yii::$app->user->identity->isAdminRegistration) return false;
        $searchModel = new SessionAttendanceSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('attendance', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAttendanceView($id)
    {
        if(!Yii::$app->user->identity->isManager && !Yii::$app->user->identity->isAdminRegistration) return false;

        return $this->render('attendance-view', [
            'model' => $this->findAttendanceModel($id),
        ]);
    }

    public function actionAttendanceDelete($id)
    {
        if(!Yii::$app->user->identity->isManager && !Yii::$app->user->identity->isAdminRegistration) return false;

        $model = $this->findAttendanceModel($id);

        if($model->delete()){
            Yii::$app->session->addFlash('success', 'Attendance record deleted.');
        }else{
            Yii::$app->session->addFlash('error', 'Failed to delete attendance record.');
        }

        return $this->redirect(['attendance']);
    }

    public function actionQrscanner()
    {
        $this->layout = '//qrscanner';
        return $this->render('qrscanner');
    }

    public function actionQrscannerResult($t = 0)
    {
        date_default_timezone_set("Asia/Kuala_Lumpur");
        $this->layout = '//plain';
        $t = $this->extractSessionQrToken($t);

        return $this->render('qrscanner-result',[
            't' => $t,
        ]); 
    }

    /**
     * Displays a single Session model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if(!Yii::$app->user->identity->isManager && !Yii::$app->user->identity->isAdminRegistration) return false;
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Session model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        if(!Yii::$app->user->identity->isManager && !Yii::$app->user->identity->isAdminRegistration) return false;
        $model = new Session();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if($model->program_sub < 1){
                    $model->program_sub = null;
                }
                $model->created_at = time();
                $model->token = Yii::$app->security->generateRandomString(10);
                if($model->save()){
                    Yii::$app->session->addFlash('success', "Session Created");
                    return $this->redirect(['index']);
                }
                
            }
        } 

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionQrpdf($id){
        date_default_timezone_set("Asia/Kuala_Lumpur");
        $member = $this->findModel($id);
        $pdf = new SessionQr;
        $pdf->model = $member;
        $pdf->generatePdf();
        exit;
    }

    public function actionCertQr($u = null){
        if(Yii::$app->user->identity->isManager && $u){
            $user = User::findOne($u);
        }else{
            if(!Setting::areCertificatesReleased()){
                $releaseDate = Setting::certificateReleaseText();
                $message = $releaseDate
                    ? 'Certificates and awards will be released from ' . $releaseDate . '.'
                    : 'The certificates are expected to be released soon.';
                Yii::$app->session->addFlash('info', $message);
                return $this->render('empty');
            }
            $user = Yii::$app->user->identity;
        }
        $att = SessionAttendance::findOne(['user_id' => $user->id]);
        if($att){
            $pdf = new CertificateQr;
            $pdf->template = CertificateTemplate::findOne(6);
            $pdf->model = $user;
            $pdf->generatePdf();
            exit;
        }else{
            Yii::$app->session->addFlash('error', "You have not attended any event so far.");
            return $this->render('empty');
        }
        
    }

    /**
     * Updates an existing Session model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if(!Yii::$app->user->identity->isManager) return false;

        $model = $this->findModel($id);

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->updated_at = time();
                if($model->program_sub < 1){
                    $model->program_sub = null;
                }
                if($model->save()){
                    Yii::$app->session->addFlash('success', "Data Updated");
                    return $this->redirect(['index']);
                }
                
            }
        } 

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionKeyin($id)
    {
        if(!Yii::$app->user->identity->isManager) return false;
        date_default_timezone_set("Asia/Kuala_Lumpur");

        $this->layout = 'plainx';
        $model = $this->findModel($id);

        $att = new SessionAttendance();
        $att->session_id = $id;

        if ($this->request->isPost) {
            if ($att->load($this->request->post())) {
                $matric = $att->user_matric;
                $user = User::findByMatricOrEmail($matric);
                if($user){
                    if($att->validateAttendance($model, $user->id)){
                        $att->user_id = $user->id;
                        $att->scanned_at = new Expression('NOW()');
                        if($att->save()){
                            Yii::$app->session->addFlash('success', "Your attendance has been successfully recorded.");
                            return $this->refresh();
                        }
                    }

                    
                }else{
                    Yii::$app->session->addFlash('error', "User not found");
                }
                
            }
            
        } 

        return $this->render('keyin', [
            'model' => $model,
            'att' => $att
        ]);
    }

    /**
     * Deletes an existing Session model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if(!Yii::$app->user->identity->isManager && !Yii::$app->user->identity->isAdminRegistration) return false;

        $model = $this->findModel($id);
        if($model->getSessionAttendances()->exists()){
            Yii::$app->session->addFlash('error', 'This session cannot be deleted because attendance has already been recorded.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if($model->delete()){
            Yii::$app->session->addFlash('success', 'Session deleted.');
        }else{
            Yii::$app->session->addFlash('error', 'Failed to delete session.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Session model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Session the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Session::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findAttendanceModel($id)
    {
        if (($model = SessionAttendance::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested attendance record does not exist.');
    }

    protected function extractSessionQrToken($value)
    {
        $value = trim((string)$value);

        if($value === ''){
            return '';
        }

        $decoded = urldecode($value);
        $query = parse_url($decoded, PHP_URL_QUERY);

        if($query){
            parse_str($query, $params);
            if(isset($params['t'])){
                $token = trim((string)$params['t']);
                return preg_match('/^[A-Za-z0-9_-]+$/', $token) ? $token : '';
            }
        }

        $token = str_replace('https://fkp-portal.umk.edu.my/icreate/site/qr?t=', '', $decoded);
        return preg_match('/^[A-Za-z0-9_-]+$/', $token) ? $token : '';
    }
}
