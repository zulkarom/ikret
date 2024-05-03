<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "program_reg_member".
 *
 * @property int $id
 * @property int $program_reg_id
 * @property string|null $member_name
 * @property string|null $member_matric
 *
 * @property ProgramReg $programReg
 */
class Member extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program_reg_member';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_name', 'member_matric'], 'required'],

            [['program_reg_id'], 'integer'],
            [['member_name', 'member_matric'], 'string', 'max' => 255],
            [['program_reg_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramRegistration::class, 'targetAttribute' => ['program_reg_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'program_reg_id' => 'Program Reg ID',
            'member_name' => 'Full Name',
            'member_matric' => 'Matric No.',
        ];
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
