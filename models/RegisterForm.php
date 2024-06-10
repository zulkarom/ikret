<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\Expression;

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
    public $user_category;
    public $institution;
    public $role_name = ['participant'];
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

            ['user_category', 'integer'],

            ['password_repeat', 'compare', 'compareAttribute' => 'password'],

            [['fullname', 'phone', 'password', 'password_repeat', 'email', 'user_category'], 'required'],

            [['fullname', 'phone', 'password', 'password_repeat', 'email', 'user_category', 'institution'], 'required', 'on' => 'external'],

            [['matric'], 'required', 'when' => function($model){
                    return $model->user_category == '1' || $model->user_category == '2' || $model->user_category == '3';
                },
                'whenClient' => "function (attribute, value) {
                    return $('#registerform-user_category').val() == '1' || $('#registerform-user_category').val() == '2' || $('#registerform-user_category').val() == '3';
                }",
            ],

            [['institution'], 'required', 'when' => function($model){
                    return $model->user_category == '4' || $model->user_category == '5';
                },
                'whenClient' => "function (attribute, value) {
                    return $('#registerform-user_category').val() == '4' || $('#registerform-user_category').val() == '5';
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
        $label['user_category'] = 'Category';
        $label['password_repeat'] = 'Password Repeat';
        $label['role_option'] = 'Register as';
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
        $user->username = strtolower($this->email);
        $user->matric = $this->matric;
        $user->fullname = strtoupper($this->fullname);
        $user->phone = $this->phone;
        $user->email = strtolower($this->email);;
        $user->institution = $this->institution;

        //pasal category
        $category = $this->user_category;
        if(in_array($category, [1,2,3])){ //internal
            $user->is_internal = 1;
            if(in_array($category, [1,2])){
                $user->is_student = 1;
            }else if($category == 3){
                $user->is_student = 0;
            }
        }else if(in_array($category, [4,5])){ //external
            $user->is_internal = 0;
            $user->is_student = 0;
        }
        
        if($this->self_register){
            $user->setPassword($this->password);
        }else{
            $user->setPassword(time());
        }
        
        $user->generateAuthKey();
        ///auto activate for now
        $user->status = 10;

        if($user->save()){
            //perihal user role
            $roles = ['participant']; // utk 1 & 5
            switch($category){
                case 2: //student / committee
                $roles = ['participant', 'committee'];
                $committee_id = 32;

                break;

                case 3: //staff
                case 4: //ext
                $roles = ['jury', 'mentor'];
                break;
            }

            foreach($roles as $r){
                $role = new UserRole();
                $role->role_name = $r;
                if($r == 'committee'){
                    $role->status = 0;
                    $role->committee_id = 32;
                    $role->request_at = new Expression('NOW()');
                }else{
                    $role->status = 10;
                    $role->request_at = new Expression('NOW()');
                    $role->approve_at = new Expression('NOW()');
                }
                
                $role->user_id = $user->id;
                if($role->save()){
                    //berjaya register
                    //return true;
                }else{
                    $role->flashError();
                    Yii::$app->session->addFlash('error', "Failed to create user role");
                }

            }

            //auto login
            if($this->self_register){
                Yii::$app->user->login($user);
            }
            return true;
            
        }else{
            $user->flashError();
            Yii::$app->session->addFlash('error', "failed to create user");
        }
        return false;
}

public static function listCategory(){
    return [
        1 => 'UMK Student Participant',
        2 => 'UMK Student Participant/ Committee',
        3 => 'UMK Staff as Jury/ Mentor',
        4 => 'Non-UMK Jury/ Mentor',
        5 => 'Non-UMK Participant'
    ];
}

}