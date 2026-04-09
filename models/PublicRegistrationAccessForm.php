<?php

namespace app\models;

use Yii;
use yii\base\Model;

class PublicRegistrationAccessForm extends Model
{
    public $contact_email;
    public $password;
    public $registration;

    public function rules()
    {
        return [
            [['contact_email', 'password'], 'required'],
            [['contact_email'], 'email'],
            [['contact_email', 'password'], 'string', 'max' => 255],
            ['password', 'validateCredentials'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'contact_email' => 'Email',
            'password' => 'Edit Password / PIN',
        ];
    }

    public function validateCredentials($attribute, $params)
    {
        if($this->hasErrors()){
            return;
        }

        if(!$this->registration instanceof ProgramRegistration){
            $this->addError($attribute, 'Registration was not found for this email.');
            return;
        }

        if(!$this->registration->validateEditPassword($this->password)){
            $this->addError($attribute, 'Incorrect email or password / PIN.');
        }
    }

    public function loadRegistration($programId)
    {
        $email = mb_strtolower(trim((string)$this->contact_email));
        $this->registration = ProgramRegistration::findOne([
            'program_id' => (int)$programId,
            'contact_email' => $email,
        ]);

        return $this->registration;
    }
}
