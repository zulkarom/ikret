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
            [['id', 'program_reg_id'], 'required'],
            [['id', 'program_reg_id'], 'integer'],
            [['member_name', 'member_matric'], 'string', 'max' => 255],
            [['program_reg_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramReg::class, 'targetAttribute' => ['program_reg_id' => 'id']],
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
            'member_name' => 'Member Name',
            'member_matric' => 'Member Matric',
        ];
    }

    /**
     * Gets query for [[ProgramReg]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgramReg()
    {
        return $this->hasOne(ProgramReg::class, ['id' => 'program_reg_id']);
    }
}
