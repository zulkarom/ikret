<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "economic_category".
 *
 * @property int $id
 * @property string $category_name
 * @property string $created_at
 * @property string $update_at
 *
 * @property Economic[] $economics
 */
class EconomicCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'economic_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_name'], 'required'],
            [['created_at', 'update_at'], 'safe'],
            [['category_name'], 'string', 'max' => 225],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_name' => 'Category Name',
            'created_at' => 'Created At',
            'update_at' => 'Update At',
        ];
    }

    /**
     * Gets query for [[Economics]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEconomics()
    {
        return $this->hasMany(Economic::className(), ['category_id' => 'id']);
    }
}
