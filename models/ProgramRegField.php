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
 * @property int $layout_width
 * @property int $show_matric
 * @property int $sort_order
 */
class ProgramRegField extends \yii\db\ActiveRecord
{
    const LAYOUT_HALF = 6;
    const LAYOUT_FULL = 12;

    public static function tableName()
    {
        return 'program_reg_field';
    }

    public function rules()
    {
        return [
            [['program_id', 'field_name'], 'required'],
            [['program_id', 'is_enabled', 'is_required', 'layout_width', 'show_matric', 'sort_order'], 'integer'],
            [['field_name'], 'string', 'max' => 64],
            [['is_enabled'], 'default', 'value' => 0],
            [['is_required'], 'default', 'value' => 0],
            [['layout_width'], 'default', 'value' => self::LAYOUT_FULL],
            [['layout_width'], 'in', 'range' => array_keys(self::layoutWidthOptions())],
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
            'layout_width' => 'Layout Width',
            'show_matric' => 'Show Matric',
            'sort_order' => 'Sort Order',
        ];
    }

    public static function layoutWidthOptions()
    {
        return [
            self::LAYOUT_HALF => 'Half Width',
            self::LAYOUT_FULL => 'Full Width',
        ];
    }
}
