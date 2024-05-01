<?php

namespace backend\models;

use Yii;
use yii\helpers\FileHelper;
use karpoff\icrop\CropImageUploadBehavior;
use yii\helpers\Url;

class EntrepreneurProfile extends Entrepreneur
{
   
    public function behaviors()
    {
       

        $id = Yii::$app->user->identity->id;
        $path = $id .'/';

        $directory = Yii::getAlias('@uploaded/entrepreneur/profile/'.$path);
        if (!is_dir($directory)) {
            FileHelper::createDirectory($directory);
        }

        return [
            [
                'class' => CropImageUploadBehavior::className(),
                'attribute' => 'profile_file',
                'scenarios' => ['insert', 'update'],
                //'placeholder' => '@app/web/images/test.png',
                'path' => $directory,
                'url' => '',
                'ratio' => 1,
            ],
        ];

    }

}
