<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "program".
 *
 * @property int $id
 * @property string $program_name
 */
class Program extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['program_name'], 'required'],
            [['program_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'program_name' => 'Program Name',
        ];
    }
}
