<?php

namespace app\controllers;

use app\models\CertificateTemplate;
use app\models\Setting;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class CertificateTemplateController extends Controller
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

    public function actionIndex()
    {
        $setting = Setting::findOne(1);
        if ($setting && Yii::$app->request->isPost) {
            $setting->allow_cert_from = Yii::$app->request->post('allow_cert_from');
            $published = (array)Yii::$app->request->post('published', []);

            foreach (CertificateTemplate::find()->all() as $template) {
                $template->published = array_key_exists($template->id, $published) ? 1 : 0;
                $template->updated_at = date('Y-m-d H:i:s');
                $template->save(false);
            }

            if ($setting->save(false)) {
                Yii::$app->session->addFlash('success', 'Certificate release settings updated.');
            } else {
                Yii::$app->session->addFlash('error', 'Unable to update certificate release settings.');
            }

            return $this->redirect(['index']);
        }

        $models = CertificateTemplate::find()->orderBy(['id' => SORT_ASC])->all();

        return $this->render('index', [
            'models' => $models,
            'setting' => $setting,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $submitAction = Yii::$app->request->post('submit_action', 'save');
            $model->template_upload = UploadedFile::getInstance($model, 'template_upload');

            if ($model->validate()) {
                if ($model->template_upload) {
                    $uploadBase = Yii::getAlias('@webroot/images');
                    if (!is_dir($uploadBase)) {
                        @mkdir($uploadBase, 0775, true);
                    }

                    $fileName = 'cert_template_' . $model->id . '_' . date('YmdHis') . '_' . Yii::$app->security->generateRandomString(8) . '.' . $model->template_upload->extension;
                    $fullPath = $uploadBase . DIRECTORY_SEPARATOR . $fileName;
                    if ($model->template_upload->saveAs($fullPath)) {
                        $model->template_file = $fileName;
                    } else {
                        $model->addError('template_upload', 'Unable to save uploaded template file.');
                    }
                }

                if (!$model->hasErrors()) {
                    $model->updated_at = date('Y-m-d H:i:s');
                    if ($model->save(false)) {
                        Yii::$app->session->addFlash('success', 'Certificate template updated.');

                        if ($submitAction === 'save-back') {
                            return $this->redirect(['index']);
                        }

                        return $this->redirect(['update', 'id' => $model->id]);
                    }

                    $model->addError('id', 'Unable to save certificate template to the database.');
                }
            }

            if ($model->hasErrors()) {
                Yii::$app->session->addFlash('error', implode('<br>', $model->getFirstErrors()));
            } else {
                Yii::$app->session->addFlash('error', 'Failed to update certificate template.');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    protected function findModel($id)
    {
        $model = CertificateTemplate::findOne((int)$id);
        if ($model === null) {
            throw new NotFoundHttpException('The requested certificate template does not exist.');
        }

        return $model;
    }
}
