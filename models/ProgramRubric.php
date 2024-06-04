<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "program_rubric".
 *
 * @property int $id
 * @property int $rubric_id
 * @property int $program_id
 *
 * @property Program $program
 * @property Rubric $rubric
 */
class ProgramRubric extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program_rubric';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rubric_id', 'program_id'], 'required'],
            [['rubric_id', 'program_id'], 'integer'],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['program_id' => 'id']],
            [['rubric_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rubric::class, 'targetAttribute' => ['rubric_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rubric_id' => 'Rubric ID',
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

    /**
     * Gets query for [[Rubric]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRubric()
    {
        return $this->hasOne(Rubric::class, ['id' => 'rubric_id']);
    }
}
