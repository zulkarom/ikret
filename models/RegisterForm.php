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
    public $username;
    public $password;
    public $password_repeat;
    public $email;
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
			
            [['username', 'fullname', 'institution'], 'string'],

            ['email', 'email'],

            ['password_repeat', 'compare', 'compareAttribute' => 'password'],

            [['username', 'fullname', 'password', 'password_repeat', 'email'], 'required'],

            [['password'], 'string', 'min' => 6],

            [['institution'], 'string'],

        ];
    }
	
	public function attributeLabels()
    {
        $label = parent::attributeLabels();
        $label['username'] = 'Username';
        $label['fullname'] = 'Full Name';
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

        if (!$this->validate()) {
            return null;
        }

        $existingUser = User::findByUsername(strtolower($this->username));
        if ($existingUser) {
            if (strpos($existingUser->email, '@dummy.com') !== false) {
                $user = $existingUser;
                $user->fullname = strtoupper($this->fullname);
                $user->matric = strtolower($this->username);
                $user->institution = $this->institution;
                $user->email = strtolower($this->email);
                if ($this->self_register) {
                    $user->setPassword($this->password);
                }
                $user->generateAuthKey();
                $user->status = 10;

                if (!$user->save()) {
                    $user->flashError();
                    Yii::$app->session->addFlash('error', 'Failed to update existing user.');
                    return false;
                }
            } else {
                $this->addError('username', 'Username is already taken.');
                return null;
            }
        } else {
            $user = new User();
            $user->username = strtolower($this->username);
            $user->matric = strtolower($this->username);
            $user->fullname = strtoupper($this->fullname);
            $user->email = strtolower($this->email);
            $user->institution = $this->institution;

            if ($this->self_register) {
                $user->setPassword($this->password);
            } else {
                $user->setPassword(time());
            }
            $user->generateAuthKey();
            $user->status = 10;

            if (!$user->save()) {
                $user->flashError();
                Yii::$app->session->addFlash('error', 'failed to create user');
                return false;
            }
        }

        // Assign participant role if not already present
        $existingRole = UserRole::find()->where(['user_id' => $user->id, 'role_name' => 'participant'])->one();
        if (!$existingRole) {
            $role = new UserRole();
            $role->role_name = 'participant';
            $role->status = 10;
            $role->user_id = $user->id;
            $role->request_at = new Expression('NOW()');
            $role->approve_at = new Expression('NOW()');
            if (!$role->save()) {
                $role->flashError();
                Yii::$app->session->addFlash('error', 'Failed to create user role');
            }
        }

        if ($this->self_register) {
            Yii::$app->user->login($user);
        }

        return true;
    }
}