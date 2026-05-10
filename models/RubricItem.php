<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rubric_item".
 *
 * @property int $id
 * @property string $item_text
 * @property int $category_id
 * @property int|null $item_type
 * @property int|null $item_order
 * @property string|null $colum_ans
 *
 * @property RubricCategory $category
 */
class RubricItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rubric_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $integerAttributes = ['category_id', 'item_type', 'item_order', 'option_number', 'is_required'];
        if(static::getTableSchema()->getColumn('is_recommend') !== null){
            $integerAttributes[] = 'is_recommend';
        }

        return [
            [['item_text', 'category_id'], 'required'],

            [['item_text', 'item_description', 'item_short'], 'string'],

            [$integerAttributes, 'integer'],
            [['colum_ans'], 'string', 'max' => 100],

            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => RubricCategory::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_text' => 'Item Text',
            'category_id' => 'Category ID',
            'item_type' => 'Item Type',
            'item_order' => 'Item Order',
            'colum_ans' => 'Colum Ans',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(RubricCategory::class, ['id' => 'category_id']);
    }

    public function hasRecommendAttribute()
    {
        return $this->hasAttribute('is_recommend');
    }

    public function getIsRecommendation()
    {
        if($this->hasRecommendAttribute()){
            return (int)$this->getAttribute('is_recommend') === 1;
        }

        return $this->category && (int)$this->category->is_recommend === 1;
    }

    public function setRecommendationFlag($value)
    {
        if($this->hasRecommendAttribute()){
            $this->setAttribute('is_recommend', ((int)$value === 1) ? 1 : 0);
        }
    }

    public function listType(){
        return [1=>'likert', 2=> 'yesno', 3=> 'shorttext', 4=> 'longtext'];
    }
}
