<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "program_method".
 *
 * @property int $id
 * @property string $method_name
 * @property int $program_id
 *
 * @property Program $program
 */
class ProgramMethod extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program_method';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['method_name', 'program_id'], 'required'],
            [['program_id'], 'integer'],
            [['method_name'], 'string', 'max' => 255],
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
            'method_name' => 'Method Name',
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
}
