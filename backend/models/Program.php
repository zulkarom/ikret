<?php

namespace backend\models;

use Yii;
use common\models\Common;
/**
 * This is the model class for table "program".
 *
 * @property int $id
 * @property string $prog_name
 * @property int $prog_category
 * @property string $prog_other
 * @property string $prog_date
 * @property string $prog_description
 * @property int $prog_anjuran
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ProgramCategory $progCategory
 */
class Program extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['entrepreneur_id', 'prog_name', 'prog_category', 'prog_date', 'prog_anjuran'], 'required'],
            [['prog_category', 'prog_anjuran'], 'integer'],
            [['prog_date', 'created_at', 'updated_at'], 'safe'],
            [['prog_description'], 'string'],
            [['prog_name', 'prog_other', 'anjuran_other'], 'string', 'max' => 225],

            ['prog_other', 'required', 'when' => function($model){
                return $model->prog_category == '1';},
                'whenClient' => "function (attribute, value) {
                return $('#program-prog_category').val() == '1';
                                 }",
            ],

            [['prog_category'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramCategory::className(), 'targetAttribute' => ['prog_category' => 'id']],
            [['entrepreneur_id'], 'exist', 'skipOnError' => true, 'targetClass' => Entrepreneur::className(), 'targetAttribute' => ['entrepreneur_id' => 'id']],
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
            'prog_name' => \Yii::t('app', 'Program Name'),
            'prog_category' => \Yii::t('app', 'Category'),
            'prog_other' => \Yii::t('app', 'Other Category'),
            'prog_date' => \Yii::t('app', 'Program Date'),
            'prog_description' => \Yii::t('app', 'Description'),
            'prog_anjuran' => \Yii::t('app', 'Organize'),
            'anjuran_other' => \Yii::t('app', 'Other Organize'),
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[ProgCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgCategory()
    {
        return $this->hasOne(ProgramCategory::className(), ['id' => 'prog_category']);
    }

    /**
     * Gets query for [[Entrepreneur]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntrepreneur()
    {
        return $this->hasOne(Entrepreneur::className(), ['id' => 'entrepreneur_id']);
    }

    public function getEntrepreneurName(){
        return $this->entrepreneur->user->fullname;
    }

    public function anjuran(){
        return Common::anjuran();
    }

    public function getAnjuranText(){
        $arr = $this->anjuran();
        if(array_key_exists($this->prog_anjuran, $this->anjuran())){
             return $arr[$this->prog_anjuran];
        } 
    }

    public static function countProgram(){
        return self::find()
        ->count();
    }
    
    public static function countProgramUser(){
        return self::find()
        ->where(['entrepreneur_id' => Yii::$app->user->identity->entrepreneur->id])
        ->count();
    }

    public function flashError(){
        if($this->getErrors()){
            foreach($this->getErrors() as $error){
                if($error){
                    foreach($error as $e){
                        Yii::$app->session->addFlash('error', $e);
                    }
                }
            }
        }
        
    }

}
