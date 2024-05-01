<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "competency".
 *
 * @property int $id
 * @property int $entrepreneur_id
 * @property string|null $description
 */
class Competency extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'competency';
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
                return $('#competency-category_id').val() == '1';
                                 }",
            ],

            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => CompetencyCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
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
            'category_id' => \Yii::t('app', 'Category'),
            'entrepreneurName' => \Yii::t('app', 'Beneficiary'),
            'description' => \Yii::t('app', 'Description'),
        ];
    }
    
    public function getEntrepreneur(){
        return $this->hasOne(Entrepreneur::className(), ['id' => 'entrepreneur_id']);
    }
    
    public function getEntrepreneurName(){
        return $this->entrepreneur->user->fullname;
    }

    public function getCategory()
    {
        return $this->hasOne(CompetencyCategory::className(), ['id' => 'category_id']);
    }

    public static function countCompetency(){
        return self::find()
        ->count();
    }
    public static function countCompetencyUser(){
        return self::find()
        ->where(['entrepreneur_id' => Yii::$app->user->identity->entrepreneur->id])
        ->count();
    }
}
