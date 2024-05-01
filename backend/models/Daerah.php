<?php

namespace backend\models;

use Yii;
use backend\models\Negeri;
/**
 * This is the model class for table "daerah".
 *
 * @property int $id
 * @property string $daerah_name
 * @property int $negeri
 */
class Daerah extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'daerah';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['daerah_name', 'negeri'], 'required'],
            [['negeri'], 'integer'],
            [['daerah_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'daerah_name' => 'City',
            'negeri' => 'State',
        ];
    }

    public function getState()
    {
        return $this->hasOne(Negeri::className(), ['id' => 'negeri']);
    }
}
