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
    public $role_name = ['participant'];
    public $self_register = true;
    public $button_label = 'Register';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			
            [['username', 'fullname'], 'string'],

            ['email', 'email'],

            ['password_repeat', 'compare', 'compareAttribute' => 'password'],

            [['username', 'fullname', 'password', 'password_repeat', 'email'], 'required'],

            [['password'], 'string', 'min' => 6],

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

        $username = trim((string)$this->username);
        $email = strtolower(trim($this->email));
        $username = User::normalizeUsernameForRegistration($username);

        $user = $this->findExistingUser($username, $email);
        if ($user) {
            if (User::isDummyEmail($user->email)) {
                $submittedUsernameIsEmail = strpos($username, '@') !== false;

                if (!$submittedUsernameIsEmail) {
                    $usernameOwner = $this->findUserByUsername($username);
                    if ($usernameOwner && (int)$usernameOwner->id !== (int)$user->id) {
                        $this->addError('username', 'Username is already taken.');
                        return null;
                    }
                    $user->username = $username;
                    $user->matric = $username;
                } elseif (!$user->matric) {
                    $user->matric = $user->username;
                }

                if (!User::isDummyEmail($email) && $email !== strtolower((string)$user->email)) {
                    $emailOwner = $this->findUserByEmail($email);
                    if ($emailOwner && (int)$emailOwner->id !== (int)$user->id) {
                        $this->addError('email', 'Email is already taken.');
                        return null;
                    }
                    $user->email = $email;
                }
            } elseif ($username !== strtolower((string)$user->username)) {
                $usernameOwner = $this->findUserByUsername($username);
                if ($usernameOwner && (int)$usernameOwner->id !== (int)$user->id) {
                    $this->addError('username', 'Username is already taken.');
                    return null;
                }
                $user->username = $username;
                $user->matric = $username;
            }

            $user->setPassword($this->password);
            $user->generateAuthKey();

            if (!$user->save()) {
                $user->flashError();
                Yii::$app->session->addFlash('error', 'Failed to update existing user.');
                return false;
            }
        } else {
            if ($this->findUserByEmail($email)) {
                $this->addError('email', 'Email is already taken.');
                return null;
            }
            if ($this->findUserByUsername($username)) {
                $this->addError('username', 'Username is already taken.');
                return null;
            }

            $user = new User();
            $user->username = $username;
            $user->matric = $username;
            $user->fullname = strtoupper($this->fullname);
            $user->email = $email;

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

    private function findExistingUser($username, $email)
    {
        return User::findAccountForRegistration($username, $email);
    }

    private function findUserByUsername($username)
    {
        return User::findOne(['username' => $username]);
    }

    private function findUserByEmail($email)
    {
        return User::findOne(['email' => $email]);
    }

}
