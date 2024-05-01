<?php
namespace frontend\models\user;

//use dektrium\user\models\User;
use Yii;
use dektrium\user\models\RegistrationForm as BaseRegistrationForm;
use backend\models\Entrepreneur;
use backend\models\Supplier;

/**
 * Signup form
 */
class RegistrationForm extends BaseRegistrationForm
{
	public $fullname;
	public $role;
	public $password_repeat;
	
	public function rules()
    {
        $rules = parent::rules();
		
		$rules['usernameLength']  = ['username', 'string'];
		
		$rules['emailLength']  = ['email', 'email'];
		
        $rules['fullnameRequired'] = ['fullname', 'required'];
        
        $rules['emailRequired'] = ['email', 'required'];

        $rules['roleRequired'] = ['role', 'required'];

		$rules['password_repeatRequired'] = ['password_repeat', 'required'];
        $rules['fullnameLength']   =  ['fullname', 'string', 'min' => 3, 'max' => 255];
		
		$rules['password_repeatCompare'] = ['password_repeat', 'compare', 'compareAttribute'=>'password', 'message'=>"Passwords don't match" ];
		

		//
        return $rules;
    }
	
	public function attributeLabels()
    {
		$label = parent::attributeLabels();
		$label['role'] = 'Kategori Pengguna';
		$label['username'] = 'Username';
		$label['fullname'] = 'Nama Penuh';
		$label['password'] = 'Kata Laluan';
		$label['password_repeat'] = 'Ulang Kata Laluan';
        return $label;
    }
	
	public function register()
    {
    	// echo $this->password_repeat;
    	// die();

        if (!$this->validate()) {
            return false;
        }

        /** @var User $user */
        $user = Yii::createObject(User::className());
        $user->setScenario('register');
        $this->loadAttributes($user);

        if (!$user->register()) {
            return false;
        }
	        
       	// $user->role = $this->rol;
        if($user->role == 1){
				$entrepreneur = new Entrepreneur;
				// $client->scenario = "register";
				$entrepreneur->user_id = $user->id;
				$entrepreneur->save(); 
			}else if($user->role == 2){
				$supplier = new Supplier;
				// $expert->scenario = "register";
				$supplier->user_id = $user->id;
				$supplier->save(); 
			}
			
	        Yii::$app->session->setFlash(
	            'info',
	            Yii::t(
	                'user',
	                'Your account has been created and a message with further instructions has been sent to your email'
	            )
	        );
	        return true;
    }


}
