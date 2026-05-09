<?php

namespace app\models;

use yii\base\Model;

class JuryApplicationManualCreateForm extends Model
{
    public $email;
    public $fullname;
    public $category;
    public $phone;
    public $institution;
    public $designation;
    public $address;
    public $password;
    public $program_scope;
    public $judging_session_id;

    public function rules()
    {
        return [
            [['email', 'fullname', 'category', 'program_scope'], 'required'],
            [['email'], 'email'],
            [['judging_session_id'], 'integer'],
            [['email', 'fullname', 'category', 'phone', 'institution', 'designation', 'password', 'program_scope'], 'string', 'max' => 255],
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
            'program_scope' => 'Program / Category',
            'judging_session_id' => 'Judging Session',
        ];
    }
}
