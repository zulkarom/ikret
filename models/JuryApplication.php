<?php

namespace app\models;

use Yii;

class JuryApplication extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'jury_applications';
    }

    public function rules()
    {
        return [
            [['jury_profile_id', 'program_id', 'declaration_accepted', 'status', 'created_at'], 'required'],
            [['jury_profile_id', 'program_id', 'program_sub_id', 'judging_session_id', 'declaration_accepted', 'status', 'created_at'], 'integer'],
            [['jury_profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => JuryProfile::class, 'targetAttribute' => ['jury_profile_id' => 'id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['program_id' => 'id']],
            [['program_sub_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramSub::class, 'targetAttribute' => ['program_sub_id' => 'id']],
            [['judging_session_id'], 'exist', 'skipOnError' => true, 'targetClass' => RubricJudgingSession::class, 'targetAttribute' => ['judging_session_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'jury_profile_id' => 'Jury',
            'program_id' => 'Program',
            'program_sub_id' => 'Program Sub',
            'judging_session_id' => 'Session',
            'declaration_accepted' => 'Declaration',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    public static function listStatus()
    {
        return [
            0 => 'NEW',
            10 => 'APPROVED',
            20 => 'REJECTED',
        ];
    }

    public function getStatusText(): string
    {
        $map = $this->listStatus();
        return array_key_exists($this->status, $map) ? $map[$this->status] : '';
    }

    public function getJuryProfile()
    {
        return $this->hasOne(JuryProfile::class, ['id' => 'jury_profile_id']);
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
