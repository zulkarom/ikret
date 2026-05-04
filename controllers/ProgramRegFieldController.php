<?php

namespace app\controllers;

use app\models\Program;
use app\models\ProgramRegField;
use app\models\ProgramRegistration;
use app\models\UserRole;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ProgramRegFieldController extends Controller
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
                            return !Yii::$app->user->isGuest
                                && (Yii::$app->user->identity->isAdmin || Yii::$app->user->identity->isManager);
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionIndex($program_id = null, $id = null, $sub = null)
    {
        if($program_id === null && $id !== null){
            $program_id = $id;
        }

        if($program_id === null){
            $programs = Program::find()->orderBy(['id' => SORT_ASC])->all();
            $first = $programs ? $programs[0] : null;
            if(!$first){
                throw new NotFoundHttpException('No program found.');
            }
            $program_id = $first->id;
        }

        $program = Program::findOne((int)$program_id);
        if(!$program){
            throw new NotFoundHttpException('Program not found.');
        }

        $programSub = null;
        $programs = [];

        if(!Yii::$app->user->isGuest && Yii::$app->user->identity->isManager && !Yii::$app->user->identity->isAdmin){
            $role = UserRole::findOne([
                'program_id' => (int)$program_id,
                'user_id' => Yii::$app->user->identity->id,
                'role_name' => 'manager',
                'program_sub' => $sub,
                'status' => 10,
            ]);

            if(!$role){
                throw new NotFoundHttpException('The requested page does not exist.');
            }

            if((int)$program->has_sub === 1 && $sub){
                $programSub = $role->programSub;
            }
        }else{
            $programs = Program::find()->orderBy(['id' => SORT_ASC])->all();
        }

        $available = ProgramRegistration::availableRegistrationFields();
        $existing = ProgramRegField::find()
            ->where(['program_id' => (int)$program_id])
            ->indexBy('field_name')
            ->all();

        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post('Field', []);

            foreach($available as $fieldName => $label){
                $enabled = array_key_exists('enabled', $post) && array_key_exists($fieldName, $post['enabled']) ? 1 : 0;

                if(array_key_exists($fieldName, $existing)){
                    $model = $existing[$fieldName];
                }else{
                    $model = new ProgramRegField();
                    $model->program_id = (int)$program_id;
                    $model->field_name = $fieldName;
                }

                $model->is_enabled = $enabled;
                $model->is_required = 0;
                $model->save(false);
            }

            Yii::$app->session->addFlash('success', 'Registration fields updated');
            return $this->redirect(['index', 'program_id' => $program_id, 'sub' => $sub]);
        }

        return $this->render('index', [
            'programs' => $programs,
            'program' => $program,
            'programSub' => $programSub,
            'sub' => $sub,
            'available' => $available,
            'existing' => $existing,
        ]);
    }
}
