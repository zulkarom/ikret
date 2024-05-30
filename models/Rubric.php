<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rubric".
 *
 * @property int $id
 * @property string $rubric_name
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Jury[] $Juries
 */
class Rubric extends \yii\db\ActiveRecord
{
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
        return $this->hasMany(RubricCategory::class, ['rubric_id' => 'id']);
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
}
