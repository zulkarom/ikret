<?php

namespace app\controllers;

use app\models\Program;
use app\models\ProgramRegField;
use app\models\ProgramRegistration;
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
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin;
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionIndex($program_id = null)
    {
        $programs = Program::find()->orderBy(['id' => SORT_ASC])->all();

        $table = Yii::$app->db->schema->getTableSchema(ProgramRegField::tableName());
        $hasLayoutWidth = $table && $table->getColumn('layout_width');

        if($program_id === null){
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

        $available = ProgramRegistration::availableRegistrationFields();
        $existing = ProgramRegField::find()
            ->where(['program_id' => (int)$program_id])
            ->indexBy('field_name')
            ->all();

        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post('Field', []);

            foreach($available as $fieldName => $label){
                $enabled = array_key_exists('enabled', $post) && array_key_exists($fieldName, $post['enabled']) ? 1 : 0;
                $required = array_key_exists('required', $post) && array_key_exists($fieldName, $post['required']) ? 1 : 0;
                $sort = array_key_exists('sort', $post) && array_key_exists($fieldName, $post['sort']) ? (int)$post['sort'][$fieldName] : 0;
                $layoutWidth = array_key_exists('layout_width', $post) && array_key_exists($fieldName, $post['layout_width']) ? (int)$post['layout_width'][$fieldName] : ProgramRegField::LAYOUT_FULL;
                $showMatric = array_key_exists('show_matric', $post) && array_key_exists($fieldName, $post['show_matric']) ? 1 : 0;

                if(array_key_exists($fieldName, $existing)){
                    $model = $existing[$fieldName];
                }else{
                    $model = new ProgramRegField();
                    $model->program_id = (int)$program_id;
                    $model->field_name = $fieldName;
                }

                $model->is_enabled = $enabled;
                $model->is_required = $enabled ? $required : 0;
                if($hasLayoutWidth){
                    $model->layout_width = $layoutWidth;
                }
                if($fieldName === 'group_member'){
                    $model->show_matric = $showMatric;
                }
                $model->sort_order = $sort;
                $model->save(false);
            }

            Yii::$app->session->addFlash('success', 'Registration fields updated');
            return $this->redirect(['index', 'program_id' => $program_id]);
        }

        return $this->render('index', [
            'programs' => $programs,
            'program' => $program,
            'available' => $available,
            'existing' => $existing,
            'hasLayoutWidth' => (bool)$hasLayoutWidth,
        ]);
    }
}
