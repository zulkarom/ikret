<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "program_reg_mentor".
 *
 * @property int $id
 * @property int $program_reg_id
 * @property int|null $user_id
 * @property int|null $is_main
 *
 * @property User $user
 */
class Mentor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program_reg_mentor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['program_reg_id'], 'required'],
            [['program_reg_id', 'user_id', 'is_main'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'program_reg_id' => 'Program Reg ID',
            'user_id' => 'User ID',
            'is_main' => 'Is Main',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
