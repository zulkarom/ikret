<?php
namespace app\models;
 
use Yii;
use yii\base\InvalidParamException;
use yii\base\Model;
use app\models\User;
 
/**
 * Change password form for current user only
 */
class ChangePasswordForm extends Model
{
    public $id;
	public $password_old;
    public $password;
    public $confirm_password;
 
    /**
     * @var \common\models\User
     */
    private $_user;
 
    /**
     * Creates a form model given a token.
     *
     * @param  string                          $token
     * @param  array                           $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($id, $config = [])
    {
        $this->_user = User::findIdentity($id);
        
        if (!$this->_user) {
            throw new InvalidParamException('Unable to find user!');
        }
        
        $this->id = $this->_user->id;
        parent::__construct($config);
    }
 
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password_old', 'password', 'confirm_password'], 'required'],
			
            [['password','confirm_password'], 'string', 'min' => 6],
            ['confirm_password', 'compare', 'compareAttribute' => 'password'],
        ];
    }
	
	 /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password_old' => 'Current Password',
			'password' => 'New Password',
			'confirm_password' => 'Confirm Password',
        ];
    }
 
    /**
     * Changes password.
     *
     * @return boolean if password was changed.
     */
    public function changePassword()
    {
		$user = $this->_user;
		$validateOldPass = $user->validatePassword($this->password_old);
		if(!$validateOldPass){
			$this->addError('password_old', 'Kata Laluan Semasa Tidak Tepat');
		}else{
			$user->setPassword($this->password);
			return $user->save(false);
		}
		
		return false;
        
    }

    public function getPassword()

    {

        return '';

    }
	
	
}