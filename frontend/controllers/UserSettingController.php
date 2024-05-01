<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use common\models\ChangePasswordForm;
use common\models\User;


class UserSettingController extends \yii\web\Controller
{
	
    public function actionIndex()
    {
		

    }
	
	
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
	 * Change User password.
	 *
	 * @return mixed
	 * @throws BadRequestHttpException
	 */
	public function actionChangePassword()
	{
		$id = Yii::$app->user->id;
		$good = 0;
		try {
			$model = new ChangePasswordForm($id);
		} catch (InvalidParamException $e) {
			throw new \yii\web\BadRequestHttpException($e->getMessage());
		}
		
		$user = $this->findModel($id);
		$user->scenario = 'update_fullname';
		$associate = $user->associate;
		$associate->scenario = 'update_external';
		
		if(Yii::$app->request->post()){
			$action = Yii::$app->request->post('form-action');
			if($action == 'change-password'){
				if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->changePassword()) {
					Yii::$app->session->setFlash('success', 'Password Changed!');
					//return $this->redirect(['/site/index']);
				}
			}else if($action == 'change-profile'){
				if ($user->load(Yii::$app->request->post()) && $associate->load(Yii::$app->request->post()) ) {
					if(!$user->save()){
						$user->flashError();
					}
					 if(!$associate->save()){
						 $associate->flashError();
					 }
					Yii::$app->session->setFlash('success', 'Profile Changed!');
					//return $this->redirect(['/site/index']);
				}
			}
		}
		
		
		
	 
		
	 
		return $this->render('change-password', [
			'model' => $model,
			'user' => $user,
			'associate' => $associate
		]);
	}
	
	public function actionChangeProfile(){
		
	}
	
	/**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
	
	
	

}
