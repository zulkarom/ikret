<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "entre_supplier".
 *
 * @property int $id
 * @property int $entrepreneur_id
 * @property int $supplier_id
 * @property int|null $created_at
 */
class EntrepreneurSupplier extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entre_supplier';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['entrepreneur_id', 'supplier_id'], 'required'],
            [['entrepreneur_id', 'supplier_id', 'created_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'entrepreneurName' => \Yii::t('app', 'Client'),
            'supplier_id' => 'Supplier ID',
            'created_at' => 'Created At',
        ];
    }
    
    public function getSupplier(){
         return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }

    public function getEntrepreneur(){
        return $this->hasOne(Entrepreneur::className(), ['id' => 'entrepreneur_id']);
    }

    public function getEntrepreneurName(){
        return $this->entrepreneur->user->fullname;
    }

    public static function countClient(){
        return self::find()
        ->where(['supplier_id' => Yii::$app->user->identity->supplier->id])
        ->count();
    }

}
