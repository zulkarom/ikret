<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "negeri".
 *
 * @property string $negeri_name
 * @property int $id
 */
class Negeri extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'negeri';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['negeri_name'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'negeri_name' => 'Negeri Name',
            'id' => 'ID',
        ];
    }
}
