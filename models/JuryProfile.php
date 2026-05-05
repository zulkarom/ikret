<?php

namespace app\models;

use Yii;

class JuryProfile extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'jury_profiles';
    }

    public function rules()
    {
        return [
            [['user_id', 'fullname', 'category'], 'required'],
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['address'], 'string'],
            [['fullname', 'category', 'phone', 'institution', 'designation'], 'string', 'max' => 255],
            [['user_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'fullname' => 'Full Name',
            'category' => 'Category',
            'phone' => 'Phone',
            'institution' => 'Institution',
            'address' => 'Address',
            'designation' => 'Designation',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function flashError()
    {
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
