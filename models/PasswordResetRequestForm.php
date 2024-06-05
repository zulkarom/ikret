<?php
namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\app\models\User',
                'message' => 'There is no user with this email address.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        $user = $this->findUser();

        if (!$user) {
            return false;
        }
        
        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }

        $email = urlencode($this->email);
        $code = $user->password_reset_token;
        $secret = "aEdlkVj34p89Poj";
        $key = md5($code.$secret);
        $url = "https://api-mailer.skyhint.com/icreate/recover/" . $email . "/" . $code . "/" . $key;
        try {
			if(file_get_contents($url) == 'true'){
                return true;
            }else{
                return false;
            }
		}
		catch (\Exception $e) {
			return false;
		}

        //api email 

        /* return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->params['senderName']]) 
            ->setTo($this->email)
            ->setSubject('Password reset for ' . Yii::$app->name)
            ->send(); */
    }

    /**
     *
     * @return \common\models\User|null
     * @author Zura Sekhniashvili <zurasekhniashvili@gmail.com>
     */
    protected function findUser()
    {
        return User::findOne([
            //'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);
    }
}
