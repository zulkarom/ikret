<?php

namespace frontend\controllers\user;

use Yii;
use dektrium\user\models\RegistrationForm;
use dektrium\user\controllers\RegistrationController as BaseRegistrationController;

class RegistrationController extends BaseRegistrationController
{
    /**
     * Displays the registration page.
     * After successful registration if enableConfirmation is enabled shows info message otherwise
     * redirects to home page.
     *
     * @return string
     * @throws \yii\web\HttpException
     */

	public function actionRegister()
    {
		$this->layout = "//main-login";

		$request = Yii::$app->request;
		// echo "<pre>";
		// print_r($request) ;
		// die();

		$username = $request->get('param1');
		$role = $request->get('param2');
		

        /** @var RegistrationForm $model */
        $model = \Yii::createObject(RegistrationForm::className());
        $event = $this->getFormEvent($model);
        
		$model->role = $role; 
		$model->username = $username;

        $this->trigger(self::EVENT_BEFORE_REGISTER, $event);

        $this->performAjaxValidation($model);

        if ($model->load(\Yii::$app->request->post()) && $model->register()) {

            $this->trigger(self::EVENT_AFTER_REGISTER, $event);

            return $this->render('/message', [
                'title'  => \Yii::t('user', 'Your account has been created'),
                'module' => $this->module,
            ]);
        }else{
           // print_r($model->getErrors());die();
        }

        return $this->render('register', [
            'model'  => $model,
            'module' => $this->module,
        ]);
	}
	
	public function actionResend(){
		$this->layout = "//main-login";
		return parent::actionResend();
	}
	
	public function actionConfirm($id, $code){
		$this->layout = "//main-login";
		return parent::actionConfirm($id, $code);
	}

    
}
