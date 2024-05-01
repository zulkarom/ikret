<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "agency".
 *
 * @property int $id
 * @property int $entrepreneur_id
 * @property string|null $description
 */
class Agency extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['entrepreneur_id', 'nama_agensi', 'tarikh_terima'], 'required'],
            [['entrepreneur_id'], 'integer'],
            [['description'], 'string', 'max' => 255],
            [['nama_agensi'], 'string', 'max' => 225],
            [['tarikh_terima'], 'safe'],
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
            'description' => \Yii::t('app', 'Description'),
            'nama_agensi' => \Yii::t('app', 'Agency Name'),
            'tarikh_terima' => \Yii::t('app', 'Date Accept'),
        ];
    }
    
    public function getEntrepreneur(){
        return $this->hasOne(Entrepreneur::className(), ['id' => 'entrepreneur_id']);
    }
    
    public function getEntrepreneurName(){
        return $this->entrepreneur->user->fullname;
    }

    public static function countAgency(){
        return self::find()
        ->count();
    }
    
    public static function countAgencyUser(){
        return self::find()
        ->where(['entrepreneur_id' => Yii::$app->user->identity->entrepreneur->id])
        ->count();
    }
}
