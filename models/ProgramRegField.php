<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "program_reg_field".
 *
 * @property int $id
 * @property int $program_id
 * @property string $field_name
 * @property int $is_enabled
 * @property int $is_required
 * @property int $show_matric
 * @property int $sort_order
 */
class ProgramRegField extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'program_reg_field';
    }

    public function rules()
    {
        return [
            [['program_id', 'field_name'], 'required'],
            [['program_id', 'is_enabled', 'is_required', 'show_matric', 'sort_order'], 'integer'],
            [['field_name'], 'string', 'max' => 64],
            [['is_enabled'], 'default', 'value' => 0],
            [['is_required'], 'default', 'value' => 0],
            [['show_matric'], 'default', 'value' => 1],
            [['sort_order'], 'default', 'value' => 0],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'program_id' => 'Program ID',
            'field_name' => 'Field Name',
            'is_enabled' => 'Enabled',
            'is_required' => 'Required',
            'show_matric' => 'Show Matric',
            'sort_order' => 'Sort Order',
        ];
    }
}
