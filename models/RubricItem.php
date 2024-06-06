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
        return [
            [['item_text', 'category_id'], 'required'],

            [['item_text', 'item_description', 'item_short'], 'string'],

            [['category_id', 'item_type', 'item_order', 'option_number'], 'integer'],
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

    public function listType(){
        return [1=>'likert', 2=> 'yesno', 3=> 'shorttext', 4=> 'longtext'];
    }
}
