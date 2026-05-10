<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rubric".
 *
 * @property int $id
 * @property string $rubric_name
 * @property string|null $rubric_description
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Jury[] $Juries
 * @property RubricJudgingSession[] $rubricJudgingSessions
 */
class Rubric extends \yii\db\ActiveRecord
{
    public function beforeSave($insert)
    {
        $this->rubric_name = $this->encodeUnsupportedUtf8($this->rubric_name);
        $this->rubric_description = $this->encodeUnsupportedUtf8($this->rubric_description);

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rubric';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rubric_name'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['rubric_description'], 'string'],
            [['rubric_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rubric_name' => 'Rubric Name',
            'rubric_description' => 'Rubric Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[ProgramRegJuries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJuries()
    {
        return $this->hasMany(JuryAssign::class, ['rubric_id' => 'id']);
    }

    public function getCategories()
    {
        return $this->hasMany(RubricCategory::class, ['rubric_id' => 'id'])->orderBy('cat_order ASC');
    }

    public function getCategoriesScore()
    {
        $categories = [];
        foreach($this->categories as $category){
            if($category->itemsScore){
                $categories[] = $category;
            }
        }
        return $categories;
    }

    public function getCategoriesRecommend()
    {
        $categories = [];
        foreach($this->categories as $category){
            if($category->itemsRecommend){
                $categories[] = $category;
            }
        }
        return $categories;
    }

    public function getJudgingSessions()
    {
        return $this->hasMany(RubricJudgingSession::class, ['rubric_id' => 'id'])->orderBy('sort_order ASC');
    }

    /**
     * get all likert * total option
     */
    public function getTotalScore(){
        $total = 0;
        $cat = $this->categories;
        if($cat){
            foreach($cat as $c){
                $items = $c->items;
                if($items){
                    foreach($items as $item){
                        if($item->item_type == 1){
                            $option = $item->option_number;
                            $total += $option;
                        }
                    }
                }
            }
        }

        return $total;

    }

    private function encodeUnsupportedUtf8($value)
    {
        if($value === null || $value === ''){
            return $value;
        }

        return preg_replace_callback('/[\x{10000}-\x{10FFFF}]/u', function($matches){
            return '&#' . $this->utf8Codepoint($matches[0]) . ';';
        }, $value);
    }

    private function utf8Codepoint($char)
    {
        if(function_exists('mb_ord')){
            return mb_ord($char, 'UTF-8');
        }

        $bytes = unpack('C*', $char);
        $b1 = $bytes[1];
        $b2 = $bytes[2];
        $b3 = $bytes[3];
        $b4 = $bytes[4];

        return (($b1 & 0x07) << 18) | (($b2 & 0x3F) << 12) | (($b3 & 0x3F) << 6) | ($b4 & 0x3F);
    }
}
