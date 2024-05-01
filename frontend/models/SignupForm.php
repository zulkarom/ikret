<?php
namespace frontend\models;

use yii\base\Model;
use Yii;
use common\models\User;
use backend\models\Entrepreneur;
use backend\models\Supplier;
/**
 * Signup form
 */
class SignupForm extends Model
{
    public $fullname;
    public $role;
    public $password;
    public $password_repeat;
    public $username;
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			
            ['username', 'string'],

            ['role', 'integer'],

            [['username', 'role'], 'required', 'on' => 'register'],

        ];
    }
	
	public function attributeLabels()
    {
        $label = parent::attributeLabels();
        $label['role'] = 'Pilih Kategori Pengguna';
        $label['username'] = 'Username';
        $label['fullname'] = 'Nama Penuh';
        $label['password'] = 'Kata Laluan';
        $label['password_repeat'] = 'Ulang Kata Laluan';
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
        
        $user = User::find()->where(['username' => $this->username])->one();

        if($user){
           
            // $user->username = $this->username;
            // $user->fullname = $this->fullname;
            // $user->email = $this->email;
            // $user->setPassword($this->password);
            // $user->generateAuthKey();
            
            ///auto activate for now
            $user->status = 10;
            $user->role = $this->role;

            if($user->save()){
               
                if($user->role == 1){
                    $usahawan = new Entrepreneur;
                    $usahawan->scenario = "signup";
                    $usahawan->user_id = $user->id;
                    $usahawan->profile_file = '';
                    if(!$usahawan->save()){
                        $usahawan->flashError();
                    }else{
                        return true;
                    }
                }else if($user->role == 2){
                    //die();
                    $supplier = new Supplier;
                    $supplier->scenario = "signup";
                    $supplier->user_id = $user->id;
                    if(!$supplier->save()){
                        //print_r($supplier->getErrors());die();
                        $supplier->flashError();
                    }else{
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
}
