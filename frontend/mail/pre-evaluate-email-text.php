<?php
use yii\helpers\Html;
use backend\modules\journal\models\EmailTemplate;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$link = Yii::$app->urlManager->createAbsoluteUrl(['user/login']);

$wf = basename(__FILE__, '.php'); 

$email = EmailTemplate::findOne(['on_enter_workflow' => $wf]);

echo nl2br($email->notification);

?>