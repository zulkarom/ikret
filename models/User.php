<?php
namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use backend\models\Entrepreneur;
use backend\models\Supplier;
use backend\models\Daerah;
/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    public $passwordRaw;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],

            //Admin Update
            [['username','email'], 'unique'],

            [['email'], 'email'],

            [['phone', 'matric', 'fullname', 'passwordRaw'], 'string'],
            
            [['username', 'fullname'], 'required', 'on' => 'update'],
            
            [['username', 'fullname'], 'required', 'on' => 'create'],
            
            
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'fullname' => 'Full Name',
            'nric' => 'NRIC',
            'matric' => 'Student/Staff ID',
            'passwordRaw' => 'New Password',
            'is_internal' => 'Category'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByUsernameOrEmail($username)
    {
        return static::find()
        ->where(['status' => self::STATUS_ACTIVE])
            ->andWhere(['or', 
            ['username' => $username, ],
            ['email' => $username, ]
        ])->one();
    }

    public static function listCategory(){
        return [
            1 => 'UMK Student/Staff',
            2 => 'Non-UMK Institution'
        ];
    }

    public function getCategoryText(){
        $text = '';
        if(array_key_exists($this->is_internal, $this->listCategory())){
            $text = $this->listCategory()[$this->is_internal];
        }
        return $text;
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    
    public function flashError(){
        if($this->getErrors()){
            foreach($this->getErrors() as $error){
                if($error){
                    foreach($error as $e){
                        Yii::$app->session->addFlash('error', $e);
                    }
                }
            }
        }
        
    }

    public function getIsAdmin(){
        $role = UserRole::findOne(['user_id' => $this->id, 'role_name' => 'admin', 'status' => 10]);
        if($role){
            return true;
        }else{
            return false;
        }
    }

    public function getIsCommittee(){
        $role = UserRole::findOne(['user_id' => $this->id, 'role_name' => 'committee', 'status' => 10]);
        if($role){
            return true;
        }else{
            return false;
        }
    }

    public function getIsManager(){
        $role = UserRole::findOne(['user_id' => $this->id, 'role_name' => 'manager', 'status' => 10]);
        if($role){
            return true;
        }else{
            return false;
        }
    }

    public function getIsParticipant(){
        $role = UserRole::findOne(['user_id' => $this->id, 'role_name' => 'participant', 'status' => 10]);
        if($role){
            return true;
        }else{
            return false;
        }
    }

}
