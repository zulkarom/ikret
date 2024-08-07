<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "program".
 *
 * @property int $id
 * @property string $program_name
 */
class Program extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['program_name','date_start', 'date_end', 'reg_info'], 'required'],

            [['date_start', 'date_end'], 'safe'],

            [['reg_info', 'program_abbr'], 'string'],
            [['program_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'program_name' => 'Program Name',
            'reg_info' => 'Registration Information'
        ];
    }

    public static function listPrograms(){
        return ArrayHelper::map(self::find()->all(),'id', 'program_name');
    }

    public function getProgramSubs()
    {
        return $this->hasMany(ProgramSub::class, ['program_id' => 'id']);
    }

    public static function programNameLong($program_id, $program_sub = null){
        $str = '';
        $program = Program::findOne($program_id);
        if($program){
            $str .= $program->program_name;
        }

        if($program_sub){
            $sub = ProgramSub::findOne($program_sub);
            if($sub){
                $str .= '<br />(' . $sub->sub_name . ')';
            }
        }

        return $str;
    }


    public function listSubPrograms(){
        $list = $this->getProgramSubs()->all();
        return ArrayHelper::map($list, 'id', 'subProgramText');
    }

    public function getProgramRubrics()
    {
        return $this->hasMany(ProgramRubric::class, ['program_id' => 'id']);
    }

    public function getProgramRubricsSub($sub)
    {
        return $this->hasMany(ProgramRubric::class, ['program_id' => 'id'])->where(['program_sub' => $sub]);
    }

    public function getProgramStages()
    {
        return $this->hasMany(ProgramStage::class, ['program_id' => 'id']);
    }

    public function getProgramMethods()
    {
        return $this->hasMany(ProgramMethod::class, ['program_id' => 'id']);
    }

    public function getProgramAchievements()
    {
        return $this->hasMany(ProgramAchievement::class, ['program_id' => 'id']);
    }
}
