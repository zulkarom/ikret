<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sector_supplier".
 *
 * @property int $id
 * @property int $supplier_id
 * @property string|null $description
 */
class SectorSupplier extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sector_supplier';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['supplier_id', 'sector_id'], 'required'],
            [['supplier_id' , 'sector_id'], 'integer'],
            [['description'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'supplier_id' => 'Supplier',
            'supplierName' => \Yii::t('app', 'Supplier'),
            'sector_id' => \Yii::t('app', 'Sector'),
            'sectorName' => \Yii::t('app', 'Sector'),
            'description' => \Yii::t('app', 'Description'),
            'descriptionx' => \Yii::t('app', 'Description'),
        ];
    }
    
    public function getSector(){
        return $this->hasOne(Sector::className(), ['id' => 'sector_id']);
    }
    
    public function getSupplier(){
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }
    
    public function getSectorName(){
        return $this->sector->sector_name;
    }
    
    public function getSupplierName(){
        return $this->supplier->user->fullname;
    }
    
    public function getDescriptionx(){
        return $this->description;
    }

    public static function countSectorSupplier(){
        return self::find()
        ->where(['supplier_id' => Yii::$app->user->identity->supplier->id])
        ->count();
    }
}
