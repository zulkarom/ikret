<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "participant_cat_program".
 *
 * @property int $id
 * @property int $program_id
 * @property string $cat_name
 * @property int $mode 1=physical 2=online
 * @property string|null $fee
 * @property int $sort_order
 * @property int $is_active
 *
 * @property Program $program
 */
class ParticipantCatProgram extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'participant_cat_program';
    }

    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['program_id', 'cat_name', 'mode'], 'required'],
            [['program_id', 'mode', 'sort_order', 'is_active'], 'integer'],
            [['cat_name'], 'string', 'max' => 255],
            [['fee'], 'string', 'max' => 255],
            [['is_active'], 'default', 'value' => 1],
            [['sort_order'], 'default', 'value' => 0],
            [['mode'], 'in', 'range' => [1, 2]],
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
            'program_id' => 'Program ID',
            'cat_name' => 'Category Name',
            'mode' => 'Mode',
            'fee' => 'Fee',
            'sort_order' => 'Sort Order',
            'is_active' => 'Active',
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
