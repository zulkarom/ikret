<?php

namespace app\controllers;

use app\models\ChangePasswordForm;
use app\models\JurySearch;
use Yii;
use yii\web\Controller;
use app\models\User;
use app\models\UserRole;
use app\models\UserSearch;
use yii\db\Expression;
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
                    ]
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

    public function actionAll(){
        if(!Yii::$app->user->identity->isAdmin) return false;
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('all', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionJury(){
        if(!Yii::$app->user->identity->isManager) return false;
        $searchModel = new JurySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('jury', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id){
        if(!Yii::$app->user->identity->isAdmin) return false;

        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {

            if($model->passwordRaw){
                $model->setPassword($model->passwordRaw);
            }

            if($model->save()){
                Yii::$app->session->addFlash('success', "Data Updated");
                return $this->refresh();
            }
            
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
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
                if(UserRole::findOne(['user_id' => Yii::$app->user->identity->id, 'role_name' => 'participant'])){
                    Yii::$app->session->addFlash('error', "Duplicate request!");
                    return $this->refresh();
                }
            }

            if($model->role_name == 'jury'){
                $model->status = 10;
                if(UserRole::findOne(['user_id' => Yii::$app->user->identity->id, 'role_name' => 'jury'])){
                    Yii::$app->session->addFlash('error', "Duplicate request!");
                    return $this->refresh();
                }
            }

            if($model->role_name == 'mentor'){
                $model->status = 10;
                if(UserRole::findOne(['user_id' => Yii::$app->user->identity->id, 'role_name' => 'mentor'])){
                    Yii::$app->session->addFlash('error', "Duplicate request!");
                    return $this->refresh();
                }
            }

            if($model->role_name == 'manager'){
                
                if(!$model->program_id){
                    Yii::$app->session->addFlash('error', "Please select a program");
                    return $this->refresh();
                }else{
                    if(UserRole::findOne(['user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager', 'program_id' => $model->program_id])){
                        Yii::$app->session->addFlash('error', "Duplicate request!");
                        return $this->refresh();
                    }
                }
            }

            if($model->role_name == 'committee'){
                if(!$model->committee_id){
                    Yii::$app->session->addFlash('error', "Please select a committee");
                    return $this->refresh();
                }else{
                    if(UserRole::findOne(['user_id' => Yii::$app->user->identity->id, 'role_name' => 'committee', 'committee_id' => $model->committee_id])){
                        Yii::$app->session->addFlash('error', "Duplicate request!");
                        return $this->refresh();
                    }

                    if($model->committee->is_jawatankuasa == 1){
                        if(!$model->is_leader){
                            Yii::$app->session->addFlash('error', "Please choose whether you are a leader or a member");
                            return $this->refresh();
                        }
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
