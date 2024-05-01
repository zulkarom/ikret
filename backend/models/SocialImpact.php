<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "social_impact".
 *
 * @property int $id
 * @property int $entrepreneur_id
 * @property string|null $description
 */
class SocialImpact extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'social_impact';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['entrepreneur_id', 'category_id'], 'required'],
            [['entrepreneur_id', 'category_id'], 'integer'],
            [['description'], 'string', 'max' => 255],
            [['other'], 'string', 'max' => 225],

            ['other', 'required', 'when' => function($model){
                return $model->category_id == '1';},
                'whenClient' => "function (attribute, value) {
                return $('#socialimpact-category_id').val() == '1';
                                 }",
            ],
            
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => SocialImpactCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'entrepreneur_id' => \Yii::t('app', 'Beneficiary'),
            'entrepreneurName' => \Yii::t('app', 'Beneficiary'),
            'socialImpactName' => \Yii::t('app', 'Social Impact'),
            'description' => \Yii::t('app', 'Description'),
            'category_id' => \Yii::t('app', 'Category'),
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(SocialImpactCategory::className(), ['id' => 'category_id']);
    }
    
    public function getEntrepreneur(){
        return $this->hasOne(Entrepreneur::className(), ['id' => 'entrepreneur_id']);
    }
    
    public function getEntrepreneurName(){
        return $this->entrepreneur->user->fullname;
    }

    public function getSocialImpactName(){
        return $this->category->category_name;
    }

    public static function countSocialImpact(){
        return self::find()
        ->count();
    }
    
    public static function countSocialImpactUser(){
        return self::find()
        ->where(['entrepreneur_id' => Yii::$app->user->identity->entrepreneur->id])
        ->count();
    }
}
