<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

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

        $storageAction = Yii::$app->request->post('storage_action');

        if($storageAction){
            $programController = new ProgramController('program', Yii::$app);

            switch($storageAction){
                case 'public-programs':
                    return $programController->actionPublicPrograms();
                case 'public-register-form':
                    return $programController->actionPublicRegisterForm(
                        Yii::$app->request->post('program_id'),
                        Yii::$app->request->post('reg_id'),
                        (bool)Yii::$app->request->post('edit', false)
                    );
                case 'public-register':
                    return $programController->actionPublicRegister();
                case 'public-edit-login':
                    return $programController->actionPublicEditLogin(Yii::$app->request->post('program_id'));
                case 'public-edit-auth':
                    return $programController->actionPublicEditAuth(Yii::$app->request->post('program_id'));
                case 'public-view-register':
                    return $programController->actionPublicViewRegister(
                        Yii::$app->request->post('program_id'),
                        Yii::$app->request->post('reg_id')
                    );
                case 'rubric-edit':
                    return $programController->actionRubricEdit(
                        Yii::$app->request->post('id'),
                        Yii::$app->request->post('sub') !== '' ? Yii::$app->request->post('sub') : null,
                        Yii::$app->request->post('pr') !== '' ? Yii::$app->request->post('pr') : null,
                        Yii::$app->request->post('rubric') !== '' ? Yii::$app->request->post('rubric') : null
                    );
                case 'setting-update':
                    if (Yii::$app->user->isGuest || !Yii::$app->user->identity->isAdmin) {
                        throw new ForbiddenHttpException('You are not allowed to update settings.');
                    }

                    $id = Yii::$app->request->post('id', 1);
                    $settingController = new SettingController('setting', Yii::$app);

                    return $settingController->actionUpdate($id, ['/setting/update', 'id' => $id]);
                case 'setting-media-update':
                    if (Yii::$app->user->isGuest || !Yii::$app->user->identity->isAdmin) {
                        throw new ForbiddenHttpException('You are not allowed to update settings media.');
                    }

                    $id = Yii::$app->request->post('id', 1);
                    $settingController = new SettingController('setting', Yii::$app);

                    return $settingController->actionMedia($id, ['/setting/media', 'id' => $id]);
                default:
                    throw new BadRequestHttpException('Invalid storage action.');
            }
        }

        return $this->render('index', [
            'post' => $post,
        ]);
    }
}
