<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "program_reg_achieve".
 *
 * @property int $id
 * @property int $program_reg_id
 * @property int|null $achieve_id
 *
 * @property ProgramAchievement $achieve
 * @property ProgramReg $programReg
 */
class ParticipantAchieve extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program_reg_achieve';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['program_reg_id', 'achieve_id'], 'required'],

            [['program_reg_id', 'achieve_id'], 'integer'],
            [['program_reg_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramRegistration::class, 'targetAttribute' => ['program_reg_id' => 'id']],
            [['achieve_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramAchievement::class, 'targetAttribute' => ['achieve_id' => 'id']],
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
            'achieve_id' => 'Achievement',
        ];
    }

    /**
     * Gets query for [[Achieve]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAchieve()
    {
        return $this->hasOne(ProgramAchievement::class, ['id' => 'achieve_id']);
    }

    /**
     * Gets query for [[ProgramReg]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegistration()
    {
        return $this->hasOne(ProgramRegistration::class, ['id' => 'program_reg_id']);
    }
}
