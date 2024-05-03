<?php
namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Signup form
 */
class RegisterForm extends Model
{
    public $fullname;
    public $phone;
    public $password;
    public $password_repeat;
    public $matric;
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			
            [['matric', 'fullname'], 'string'],
            ['email', 'email'],
            ['phone', 'number'],

            ['password_repeat', 'compare', 'compareAttribute' => 'password'],

            [['fullname','matric', 'phone', 'password', 'password_repeat', 'email'], 'required'],

            [['password'], 'string', 'min' => 6],

        ];
    }
	
	public function attributeLabels()
    {
        $label = parent::attributeLabels();
        $label['matric'] = 'Student/Staff ID';
        $label['fullname'] = 'Full Name';
        $label['password_repeat'] = 'Password Repeat';
        return $label;
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();  
        $user->username = $this->email;
        $user->matric = $this->matric;
        $user->fullname = strtoupper($this->fullname);
        $user->phone = $this->phone;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        ///auto activate for now
        $user->status = 10;

        if($user->save()){
            //auto login
            Yii::$app->user->login($user);
            return true;
        }
    
        
        return false;
}
}