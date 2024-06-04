<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "program_sub".
 *
 * @property int $id
 * @property string $sub_name
 * @property string|null $advisor
 * @property int $program_id
 *
 * @property Program $program
 */
class ProgramSub extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program_sub';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sub_name', 'program_id'], 'required'],
            [['program_id'], 'integer'],
            [['sub_name', 'advisor'], 'string', 'max' => 255],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['program_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sub_name' => 'Sub Name',
            'advisor' => 'Advisor',
            'program_id' => 'Program ID',
        ];
    }

    /**
     * Gets query for [[Program]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Program::class, ['id' => 'program_id']);
    }

    public function getProgramSub()
    {
        return $this->hasOne(ProgramSub::class, ['id' => 'program_sub']);
    }

    public function getSubProgramText(){
        return $this->sub_name . ' / ' . $this->advisor;
    }

    public function getProgramRubrics()
    {
        return $this->hasMany(ProgramRubric::class, ['program_sub' => 'id']);
    }
}
