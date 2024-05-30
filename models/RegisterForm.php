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
    public $is_internal;
    public $institution;
    public $role_name = 'participant';
    public $self_register = true;
    public $button_label = 'Register';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			
            [['matric', 'fullname'], 'string'],

            ['email', 'email'],
            ['phone', 'number'],

            ['is_internal', 'integer'],

            ['password_repeat', 'compare', 'compareAttribute' => 'password'],

            [['fullname', 'phone', 'password', 'password_repeat', 'email', 'is_internal'], 'required'],

            [['fullname', 'phone', 'password', 'password_repeat', 'email', 'is_internal', 'institution'], 'required', 'on' => 'external'],

            [['matric'], 'required', 'when' => function($model){
                return $model->is_internal == '1';},
                'whenClient' => "function (attribute, value) {
        return $('#registerform-is_internal').val() == '1';
                         }",
            ],

            [['institution'], 'required', 'when' => function($model){
                return $model->is_internal == '2';},
                'whenClient' => "function (attribute, value) {
        return $('#registerform-is_internal').val() == '2';
                         }",
            ],
            

            [['password'], 'string', 'min' => 6],

            [['institution'], 'string'],

        ];
    }
	
	public function attributeLabels()
    {
        $label = parent::attributeLabels();
        $label['matric'] = 'Student/Staff ID';
        $label['fullname'] = 'Full Name';
        $label['is_internal'] = 'Category';
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
        if(!$this->self_register){
            $this->password = 'X123456789';
            $this->password_repeat = 'X123456789';
        }
        //print_r($this->getErrors());
       
        if (!$this->validate()) {
            //print_r($this->getErrors());
            //die();
            return null;
        }

        
        
        $user = new User();  
        $user->username = $this->email;
        $user->matric = $this->matric;
        $user->fullname = strtoupper($this->fullname);
        $user->phone = $this->phone;
        $user->email = $this->email;
        $user->institution = $this->institution;
        if($this->self_register){
            $user->setPassword($this->password);
        }else{
            
            $user->setPassword(time());
        }
        
        $user->generateAuthKey();
        ///auto activate for now
        $user->status = 10;

        if($user->save()){
            $role = new UserRole();
            $role->role_name = $this->role_name;
            $role->status = 10;
            $role->user_id = $user->id;
            if($role->save()){
                //auto login
                if($this->self_register){
                    Yii::$app->user->login($user);
                }
                //berjaya register
                return true;
            }else{
                Yii::$app->session->addFlash('error', "failed to create user role");
            }
            
        }else{
            $user->flashError();
            Yii::$app->session->addFlash('error', "failed to create user");
        }
    
        
        return false;
}
public static function listCategory(){
    return User::listCategory();
}
}