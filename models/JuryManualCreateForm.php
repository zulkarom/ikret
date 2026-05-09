<?php

namespace app\models;

use yii\base\Model;

class JuryManualCreateForm extends Model
{
    public $email;
    public $fullname;
    public $category;
    public $phone;
    public $institution;
    public $designation;
    public $address;
    public $password;

    public function rules()
    {
        return [
            [['email', 'fullname', 'category'], 'required'],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 255],
            [['fullname', 'category', 'phone', 'institution', 'designation', 'password'], 'string', 'max' => 255],
            [['address'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Email',
            'fullname' => 'Full Name',
            'category' => 'Category',
            'phone' => 'Phone',
            'institution' => 'Institution',
            'designation' => 'Designation',
            'address' => 'Address',
            'password' => 'Password',
        ];
    }
}
