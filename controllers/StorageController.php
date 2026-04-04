<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;

class StorageController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $post = Yii::$app->request->post();

        return $this->render('index', [
            'post' => $post,
        ]);
    }
}
