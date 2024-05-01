<?php
namespace frontend\controllers\user;

use dektrium\user\controllers\RecoveryController as BaseRecoveryController;

class RecoveryController extends BaseRecoveryController
{
    
    public function actionRequest()
    {
		$this->layout = "//main-login";
        return parent::actionRequest();
    }

    public function actionReset($id, $code)
    {
		$this->layout = "//main-login";
        return parent::actionReset($id, $code);
    }
}
