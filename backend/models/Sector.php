<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sector".
 *
 * @property int $id
 * @property string|null $sector_name
 */
class Sector extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sector';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sector_name'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sector_name' => 'Sector Name',
        ];
    }
}
