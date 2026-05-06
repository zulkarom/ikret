<?php

namespace app\models;

use yii\base\Model;

class DefaultPasswordForm extends Model
{
    public $password;
    public $email;

    private $_user;

    public function __construct($id, $config = [])
    {
        $this->_user = User::findIdentity($id);

        if (!$this->_user) {
            throw new InvalidArgumentException('Unable to find user.');
        }

        $this->email = $this->requiresEmailUpdate() ? '' : $this->_user->email;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            ['password', 'validatePasswordIsNotUsername'],
            ['email', 'trim'],
            ['email', 'required', 'when' => function () {
                return $this->requiresEmailUpdate();
            }],
            ['email', 'email'],
            ['email', 'validateEmail'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'New Password',
            'email' => 'Put Your Valid Email',
        ];
    }

    public function requiresEmailUpdate()
    {
        return substr(strtolower((string)$this->_user->email), -10) === '@dummy.com';
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->_user;
        $user->setPassword($this->password);

        if ($this->requiresEmailUpdate()) {
            $user->email = $this->email;
        }

        return $user->save(false);
    }

    public function validatePasswordIsNotUsername($attribute)
    {
        if (!$this->hasErrors() && $this->password === $this->_user->username) {
            $this->addError($attribute, 'New password cannot be the same as your username.');
        }
    }

    public function validateEmail($attribute)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }

        if ($this->requiresEmailUpdate() && substr(strtolower((string)$this->email), -10) === '@dummy.com') {
            $this->addError($attribute, 'Please enter your actual email address.');
            return;
        }

        $exists = User::find()
            ->where(['email' => $this->email])
            ->andWhere(['<>', 'id', $this->_user->id])
            ->exists();

        if ($exists) {
            $this->addError($attribute, 'This email address has already been taken.');
        }
    }
}
