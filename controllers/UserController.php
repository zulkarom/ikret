<?php

namespace app\controllers;

use app\models\Certificate;
use app\models\CertificateTemplate;
use app\models\ChangePasswordForm;
use app\models\Member;
use app\models\Model;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use app\models\Program;
use app\models\ProgramRegistration;
use app\models\Questionnaire;
use app\models\QuestionnaireAnswer;
use app\models\QuestionnaireAnswerPost;
use app\models\Upload;
use app\models\User;
use app\models\UserRole;
use Reflector;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class UserController extends Controller
{
    /**
     * {@inheritdoc}
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
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = User::findOne(Yii::$app->user->identity->id);
        if ($model->load(Yii::$app->request->post())) {
            $model->fullname = strtoupper($model->fullname);

            if($model->save()){
                Yii::$app->session->addFlash('success', "Profile Updated");
                return $this->refresh();
            }

        }

        return $this->render('index',[
            'model' => $model
        ]);
    }

    public function actionRemoveRole($id){
        $role = $this->findUserRole($id);
        if($role->delete()){
            Yii::$app->session->addFlash('info', "User role deleted");
            return $this->redirect(['add-role']);
        }

    }

    protected function findUserRole($id)
    {
        if (($model = UserRole::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionAddRole(){
        $model = new UserRole();
        $model->user_id = Yii::$app->user->identity->id;
        $roles = UserRole::find()
        ->where(['user_id' => Yii::$app->user->identity->id])
        ->all();

        if ($model->load(Yii::$app->request->post())) {
            $model->status = 0;
            $model->request_at = new Expression('NOW()');

            if($model->role_name == 'participant'){
                $model->status = 10;
            }

            if($model->role_name == 'manager'){
                if(!$model->program_id){
                    Yii::$app->session->addFlash('error', "Please select a program");
                    return $this->refresh();
                }
            }

            if($model->role_name == 'committee'){
                if(!$model->committee_id){
                    Yii::$app->session->addFlash('error', "Please select a committee");
                    return $this->refresh();
                }else if($model->committee->is_jawatankuasa == 1){
                    if(!$model->is_leader){
                        Yii::$app->session->addFlash('error', "Please choose whether you are a leader or a member");
                    return $this->refresh();
                    }
                }
            }



            if($model->save()){
                Yii::$app->session->addFlash('success', "Role Added");
                return $this->refresh();
            }else{
                $model->flashError();
            }

        }

        return $this->render('add-role',[
            'model' => $model,
            'roles' => $roles
        ]);
    }

    public function actionChangePassword()
	{
		$id = Yii::$app->user->id;
	 
		try {
			$model = new ChangePasswordForm($id);
		} catch (\InvalidArgumentException $e) {
			throw new \yii\web\BadRequestHttpException($e->getMessage());
		}
	 
		if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->changePassword()) {
			Yii::$app->session->setFlash('success', 'Password Changed!');
		}
	 
		return $this->render('change-password', [
			'model' => $model,
		]);
	}


}
