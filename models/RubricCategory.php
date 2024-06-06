<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rubric_category".
 *
 * @property int $id
 * @property string $category_name
 * @property int $rubric_id
 *
 * @property Rubric $rubric
 * @property RubricItem[] $rubricItems
 */
class RubricCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rubric_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_name', 'rubric_id'], 'required'],
            [['rubric_id', 'is_recommend'], 'integer'],
            [['category_name'], 'string', 'max' => 255],
            [['rubric_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rubric::class, 'targetAttribute' => ['rubric_id' => 'id']],
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
            'rubric_id' => 'Rubric ID',
        ];
    }

    /**
     * Gets query for [[Rubric]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRubric()
    {
        return $this->hasOne(Rubric::class, ['id' => 'rubric_id']);
    }

    /**
     * Gets query for [[RubricItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(RubricItem::class, ['category_id' => 'id']);
    }
}
