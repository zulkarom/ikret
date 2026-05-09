<?php

namespace app\controllers;

use app\models\Setting;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class SettingController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin;
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionUpdate($id = 1, $successRoute = null)
    {
        $model = Setting::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->banner_file = UploadedFile::getInstance($model, 'banner_file');
            $model->programme_book_qr_file = UploadedFile::getInstance($model, 'programme_book_qr_file');

            if ($model->validate()) {
                $uploadBase = Yii::getAlias('@webroot/uploads/setting');
                if (!is_dir($uploadBase)) {
                    @mkdir($uploadBase, 0775, true);
                }

                if ($model->banner_file) {
                    $fileName = 'banner_' . date('YmdHis') . '_' . Yii::$app->security->generateRandomString(8) . '.' . $model->banner_file->extension;
                    $fullPath = $uploadBase . DIRECTORY_SEPARATOR . $fileName;
                    if ($model->banner_file->saveAs($fullPath)) {
                        $model->banner_image = 'uploads/setting/' . $fileName;
                    }
                }

                if ($model->programme_book_qr_file) {
                    $fileName = 'programme_book_qr_' . date('YmdHis') . '_' . Yii::$app->security->generateRandomString(8) . '.' . $model->programme_book_qr_file->extension;
                    $fullPath = $uploadBase . DIRECTORY_SEPARATOR . $fileName;
                    if ($model->programme_book_qr_file->saveAs($fullPath)) {
                        $model->programme_book_qr = 'uploads/setting/' . $fileName;
                    }
                }

                if ($model->save(false)) {
                    Yii::$app->session->addFlash('success', 'Settings Updated');
                    return $successRoute === null ? $this->refresh() : $this->redirect($successRoute);
                }
            }
        }

        if (Yii::$app->request->isPost) {
            Yii::$app->session->addFlash('error', 'Failed to update settings. Please check the form for errors.');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
}
