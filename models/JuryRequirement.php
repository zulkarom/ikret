<?php

namespace app\models;

use Yii;

class JuryRequirement extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'jury_requirements';
    }

    public function rules()
    {
        return [
            [['program_id'], 'required'],
            [['program_id', 'program_sub_id', 'judging_session_id', 'is_required', 'is_active', 'jury_limit', 'created_at', 'updated_at'], 'integer'],
            [['program_id', 'program_sub_id', 'judging_session_id'], 'unique', 'targetAttribute' => ['program_id', 'program_sub_id', 'judging_session_id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['program_id' => 'id']],
            [['program_sub_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramSub::class, 'targetAttribute' => ['program_sub_id' => 'id']],
            [['judging_session_id'], 'exist', 'skipOnError' => true, 'targetClass' => RubricJudgingSession::class, 'targetAttribute' => ['judging_session_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'program_id' => 'Program',
            'program_sub_id' => 'Program Sub',
            'judging_session_id' => 'Session',
            'is_required' => 'Needs Juries',
            'is_active' => 'Open For Application',
            'jury_limit' => 'Jury Limit',
        ];
    }

    public function getProgram()
    {
        return $this->hasOne(Program::class, ['id' => 'program_id']);
    }

    public function getProgramSub()
    {
        return $this->hasOne(ProgramSub::class, ['id' => 'program_sub_id']);
    }

    public function getJudgingSession()
    {
        return $this->hasOne(RubricJudgingSession::class, ['id' => 'judging_session_id']);
    }

    public static function listRequiredActive(): array
    {
        $rows = self::find()->where(['is_required' => 1, 'is_active' => 1])->all();
        $out = [];
        if($rows){
            foreach($rows as $r){
                $name = $r->program ? $r->program->program_name : ('Program #' . $r->program_id);
                if($r->programSub){
                    $name .= ' / ' . $r->programSub->sub_name;
                }
                if($r->judgingSession){
                    $name .= ' / ' . $r->judgingSession->session_name;
                }
                $out[$r->id] = $name;
            }
        }
        return $out;
    }

    public function flashError()
    {
        if($this->getErrors()){
            foreach($this->getErrors() as $error){
                if($error){
                    foreach($error as $e){
                        Yii::$app->session->addFlash('error', $e);
                    }
                }
            }
        }
    }
}
