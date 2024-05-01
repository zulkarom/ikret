<?php

namespace backend\models;

use Yii;
use common\models\User;
/**
 * This is the model class for table "entrepreneur".
 *
 * @property int $id
 * @property int $user_id
 * @property int $age
 * @property string $address
 * @property string $location
 * @property string $profile_file
 */
class Entrepreneur extends \yii\db\ActiveRecord
{
    public $u_longitude;
    public $u_latitude;
    public $u_location;
    public $username;
    public $password;
    public $nric;
    public $fullname;
    public $email;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entrepreneur';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required', 'on' => 'signup'],
            
            ['username', 'validUsername'],
            
            ['nric', 'validNric'],
            
            ['email', 'validEmail',],
            
            ['user_id', 'unique', 'message' => 'A beneficiary with this user has already exist'],
			
			['phone', 'unique', 'message' => 'A beneficiary with this phone has already exist'],

            /*[['age', 'address', 'postcode', 'city', 'state', 'phone'], 'required', 'on' => 'insert'],*/

            [['user_id', 'fullname'], 'required', 'on' => 'admin_insert'],
            //Profile image
            ['profile_file', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'on' => ['insert', 'update']],

            [['user_id', 'age', 'state', 'postcode'], 'integer'],
            
            [['biz_info', 'note'], 'string'],
            
            [['address', 'u_location', 'u_longitude', 'u_latitude', 'longitude', 'latitude', 'location', 'biz_name', 'phone', 'city'], 'string', 'max' => 225],
        ];
    }
    
    public function validEmail()
    {
        $attr = 'email';
        $label = 'Email';
        $user = User::find()->where([$attr => $this->$attr]);
        if($this->user_id){
            $user->andWhere(['<>', 'id' , $this->user_id]);
        }
        $user = $user->one();
        if($user){
            $this->addError($attr, $label . ' "'. $user->$attr .'" has already been taken.');
        }
        
    }
    
    public function validUsername()
    {
        $attr = 'username';
        $label = 'Username';
        $user = User::find()->where([$attr => $this->$attr]);
        if($this->user_id){
            $user->andWhere(['<>', 'id' , $this->user_id]);
        }
        $user = $user->one();
        if($user){
            $this->addError($attr, $label . ' "'. $user->$attr .'" has already been taken.');
        }
        
    }
    
    public function validNric()
    {
        $attr = 'nric';
        $label = 'NRIC';
        $user = User::find()->where([$attr => $this->$attr]);
        if($this->user_id){
            $user->andWhere(['<>', 'id' , $this->user_id]);
        }
        $user = $user->one();
        if($user){
            $this->addError($attr, $label . ' "'. $user->$attr .'" has already been taken.');
        }
        
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'age' => \Yii::t('app', 'Age'),
            'address' => \Yii::t('app', 'Address'),
            'location' => \Yii::t('app', 'Location'),
            'profile_file' => \Yii::t('app', 'Profile Image'),
            'biz_name' => \Yii::t('app', 'Business Name'),
            'fullname' => \Yii::t('app', 'Full Name'),
            'phone' => \Yii::t('app', 'Phone'),
            'email' => \Yii::t('app', 'Email'),
            'postcode' => \Yii::t('app', 'Postcode'),
            'city' => \Yii::t('app', 'City'),
            'state' => \Yii::t('app', 'State'),
            'nric' => 'NRIC',
            'biz_info' => \Yii::t('app', 'Business Information'),
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

    public function getSectorEntrepreneurs(){
         return $this->hasMany(SectorEntrepreneur::className(), ['entrepreneur_id' => 'id']);
    }

    public static function countEntrepreneur(){
        return self::find()
        ->count();
    }

    public function getFullAddress(){
        $state = '';
        if($this->stateModel){
            $state = ' '.$this->stateModel->negeri_name;
        }
        
        return $this->address.' '.$this->postcode.' '.$this->city . $state;
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
