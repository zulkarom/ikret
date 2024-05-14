<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "program_reg_jury".
 *
 * @property int $id
 * @property int $reg_id
 * @property int $user_id
 * @property string|null $stage
 * @property int|null $rubric_id
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property ProgramReg $reg
 * @property User $user
 */
class Jury extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program_reg_jury';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reg_id', 'user_id'], 'required'],
            [['reg_id', 'user_id', 'rubric_id', 'created_at', 'updated_at'], 'integer'],
            [['stage'], 'string', 'max' => 255],
            [['reg_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramRegistration::class, 'targetAttribute' => ['reg_id' => 'id']],
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
            'reg_id' => 'Reg ID',
            'user_id' => 'Jury',
            'stage' => 'Stage',
            'rubric_id' => 'Rubric ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Reg]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegistration()
    {
        return $this->hasOne(ProgramRegistration::class, ['id' => 'reg_id']);
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
