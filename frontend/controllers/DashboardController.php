<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use backend\models\Semester;
use common\models\Application;
use common\models\Token;
use backend\modules\project\models\Project;
use backend\models\Kadet;
use backend\models\Fasi;
/**
 * Site controller
 */
class DashboardController extends Controller
{
    /**
     * @inheritdoc
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
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if(Yii::$app->user->identity->entrepreneur){
            return $this->redirect(['/entrepreneur/dashboard']);
        }else if(Yii::$app->user->identity->supplier){
            return $this->redirect(['/supplier/dashboard']);
        }
    }
	

	
}
