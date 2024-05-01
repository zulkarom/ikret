<?php

namespace backend\models;

use Yii;
use common\models\User;
/**
 * This is the model class for table "supplier".
 *
 * @property int $id
 * @property int $user_id
 * @property int $age
 * @property string $address
 * @property string $location
 * @property string $profile_file
 */
class Supplier extends \yii\db\ActiveRecord
{
    public $s_longitude;
    public $s_latitude;
    public $s_location;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'supplier';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            [['user_id'], 'required', 'on' => 'signup'],
            
            ['user_id', 'unique', 'message' => 'A supplier with this email has already exist'],

            // [['address', 'postcode', 'city', 'state', 'phone', 'biz_name'], 'required', 'on' => 'insert'],

            [['address', 'postcode', 'city', 'state', 'phone', 'biz_name'], 'required', 'on' => 'admin_insert'],

            //Profile image
            ['profile_file', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'on' => ['insert', 'update']],
            [['user_id', 'age'], 'integer'],
            [['address', 's_location', 's_longitude', 's_latitude', 'longitude', 'latitude', 'location', 'biz_name', 'phone'], 'string', 'max' => 225],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'age' => 'Age',
            'address' => \Yii::t('app', 'Address'),
            'location' => \Yii::t('app', 'Location'),
            'biz_name' => \Yii::t('app', 'Business Name'),
            'fullname' => \Yii::t('app', 'Full Name'),
            'profile_file' => \Yii::t('app', 'Profile File'),
        ];
    }

    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    public function getCityModel(){
        return $this->hasOne(Daerah::className(), ['id' => 'city']);
    }
    
    public function getStateModel(){
        return $this->hasOne(Negeri::className(), ['id' => 'state']);
    }
    
    public function getSectorSuppliers(){
         return $this->hasMany(SectorSupplier::className(), ['supplier_id' => 'id']);
    }

    public function getFullAddress(){
        return $this->address.'<br/>'.$this->postcode.', '.$this->cityModel->daerah_name.'<br/>'.$this->stateModel->negeri_name;
    }

    public static function countSupplier(){
        return self::find()
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
