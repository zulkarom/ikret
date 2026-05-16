<?php
namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
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
    public $kira;


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

            [['is_student', 'is_internal'], 'integer'],

            [['phone', 'matric', 'fullname', 'passwordRaw', 'institution'], 'string'],
            
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

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
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
    public static function findByMatricOrEmail($matric)
    {
        return static::find()
        ->where(['status' => self::STATUS_ACTIVE])
            ->andWhere(['or', 
            ['matric' => $matric, ],
            ['email' => $matric, ]
        ])->one();
    }

    public static function isDummyEmail($email)
    {
        return substr(strtolower((string)$email), -10) === '@dummy.com';
    }

    public static function isSiswaEmail($email)
    {
        $email = strtolower(trim((string)$email));
        return substr($email, -17) === '@siswa.umk.edu.my'
            || substr($email, -17) === '@siswa.edu.umk.my';
    }

    public static function normalizeUsernameForRegistration($username)
    {
        $username = trim((string)$username);
        $matric = static::matricFromSiswaEmail($username);

        return $matric ?: strtolower($username);
    }

    public static function dummyEmailForMatric($matric)
    {
        return strtolower(trim((string)$matric)) . '@dummy.com';
    }

    public static function findAccountForRegistration($username, $email = '')
    {
        $username = static::normalizeUsernameForRegistration($username);
        $email = strtolower(trim((string)$email));

        if ($email !== '' && !static::isDummyEmail($email)) {
            $user = static::findOne(['email' => $email]);
            if ($user) {
                return $user;
            }

            $user = static::findImportedStudentBySiswaEmail($email);
            if ($user) {
                return $user;
            }
        }

        if ($username !== '') {
            $user = static::findOne(['username' => $username]);
            if ($user) {
                return $user;
            }

            $matric = static::matricFromText($username);
            if ($matric) {
                $user = static::findStudentByMatric($matric);
                if ($user) {
                    return $user;
                }
            }

            if (!static::isDummyEmail($username)) {
                $user = static::findImportedStudentBySiswaEmail($username);
                if ($user) {
                    return $user;
                }
            }
        }

        return null;
    }

    public static function findStudentByMatric($matric)
    {
        $matric = static::matricFromText($matric);
        if (!$matric) {
            return null;
        }

        $dummyEmail = static::dummyEmailForMatric($matric);
        $siswaEmail = strtolower($matric) . '@siswa.umk.edu.my';
        $legacySiswaEmail = strtolower($matric) . '@siswa.edu.umk.my';

        return static::find()
            ->where(['or',
                ['username' => $matric],
                ['matric' => $matric],
                ['email' => $dummyEmail],
                ['email' => $siswaEmail],
                ['email' => $legacySiswaEmail],
            ])
            ->orWhere('(LOWER(TRIM([[email]])) LIKE :siswaDomain OR LOWER(TRIM([[email]])) LIKE :legacySiswaDomain) AND LOCATE(:matricLower, LOWER(TRIM(SUBSTRING_INDEX([[email]], \'@\', 1)))) > 0', [
                ':siswaDomain' => '%@siswa.umk.edu.my',
                ':legacySiswaDomain' => '%@siswa.edu.umk.my',
                ':matricLower' => strtolower($matric),
            ])
            ->orderBy(new Expression("
                CASE
                    WHEN LOWER(TRIM([[username]])) = :matricLower THEN 0
                    WHEN LOWER(TRIM([[email]])) = :dummyEmail THEN 1
                    WHEN LOWER(TRIM([[email]])) = :siswaEmail THEN 2
                    WHEN LOWER(TRIM([[email]])) = :legacySiswaEmail THEN 3
                    ELSE 3
                END,
                [[id]] ASC
            "))
            ->addParams([
                ':matricLower' => strtolower($matric),
                ':dummyEmail' => $dummyEmail,
                ':siswaEmail' => $siswaEmail,
                ':legacySiswaEmail' => $legacySiswaEmail,
            ])
            ->one();
    }

    public static function findOrCreateImportedStudentAccount($matric, $fullname)
    {
        $matric = strtoupper(trim((string)$matric));
        if ($matric === '') {
            return null;
        }

        $user = static::findAccountForRegistration($matric, static::dummyEmailForMatric($matric));
        if (!$user) {
            $user = new static();
            $user->username = $matric;
            $user->fullname = $fullname;
            $user->matric = $matric;
            $user->email = static::dummyEmailForMatric($matric);
            $user->is_student = 1;
            $user->is_internal = 1;
            $user->status = self::STATUS_ACTIVE;
            $user->setPassword($matric);
            $user->generateAuthKey();

            if (!$user->save(false)) {
                return false;
            }

            return $user;
        }

        $dirty = [];
        if (strtolower((string)$user->username) !== strtolower($matric)) {
            $usernameOwner = static::findOne(['username' => $matric]);
            if (!$usernameOwner || (int)$usernameOwner->id === (int)$user->id) {
                $user->username = $matric;
                $dirty[] = 'username';
            }
        }
        if (!$user->matric || strtolower((string)$user->matric) !== strtolower($matric)) {
            $user->matric = $matric;
            $dirty[] = 'matric';
        }
        if ($user->is_student === null) {
            $user->is_student = 1;
            $dirty[] = 'is_student';
        }
        if ($user->is_internal === null) {
            $user->is_internal = 1;
            $dirty[] = 'is_internal';
        }

        if ($dirty) {
            $dirty[] = 'updated_at';
            $user->save(false, array_unique($dirty));
        }

        return $user;
    }

    public static function findImportedStudentBySiswaEmail($email)
    {
        $email = strtolower(trim((string)$email));
        if (!static::isSiswaEmail($email)) {
            return null;
        }

        $localPart = strtolower(trim(strtok($email, '@')));
        if ($localPart === '') {
            return null;
        }

        return static::find()
            ->where('LOWER(TRIM([[email]])) = CONCAT(LOWER(TRIM([[username]])), :dummyDomain)', [
                ':dummyDomain' => '@dummy.com',
            ])
            ->andWhere('LOCATE(LOWER(TRIM([[username]])), :localPart) > 0', [
                ':localPart' => $localPart,
            ])
            ->orderBy(new Expression("
                CASE
                    WHEN LOWER(TRIM([[username]])) = :localPartExact THEN 0
                    ELSE 1
                END,
                [[id]] ASC
            "))
            ->addParams([':localPartExact' => $localPart])
            ->one();
    }

    public static function matricFromSiswaEmail($email)
    {
        $email = strtolower(trim((string)$email));
        if (!static::isSiswaEmail($email)) {
            return null;
        }

        $localPart = strtolower(trim(strtok($email, '@')));
        if ($localPart === '') {
            return null;
        }

        return static::matricFromText($localPart) ?: strtoupper($localPart);
    }

    public static function matricFromText($value)
    {
        $value = trim((string)$value);
        if (preg_match('/[a-z][0-9]{2}[a-z][0-9]{4}/i', $value, $matches)) {
            return strtoupper($matches[0]);
        }

        return null;
    }

    public static function listIsInternal(){
        return [
            1 => 'UMK Student/Staff',
            0 => 'Non-UMK Institution',
        ];
    }

    public static function listIsStudent(){
        return [
            1 => 'Student',
            0 => 'Not Student', //staff / external
        ];
    }

    public function getIsInternalText(){
        $text = '';
        if(array_key_exists($this->is_internal, $this->listisInternal())){
            $text = $this->listisInternal()[$this->is_internal];
        }
        return $text;
    }

    public function getIsStudentText(){
        $text = '';
        if(array_key_exists($this->is_student, $this->listIsStudent())){
            $text = $this->listIsStudent()[$this->is_student];
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

    public function hasActiveRole($roleName){
        return UserRole::find()
            ->where([
                'user_id' => $this->id,
                'role_name' => (array)$roleName,
                'status' => 10,
            ])
            ->exists();
    }

    public function getIsSuperadmin(){
        return $this->hasActiveRole('superadmin');
    }

    public function getIsAdmin(){
        return $this->isSuperadmin;
    }

    public function getIsAdminJury(){
        return $this->hasActiveRole(['admin-jury', 'superadmin']);
    }

    public function getIsAdminRegistration(){
        return $this->hasActiveRole(['admin-registration', 'superadmin']);
    }

    public function getIsAdminCertificate(){
        return $this->hasActiveRole(['admin-certificate', 'superadmin']);
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
        return $this->hasActiveRole(['manager', 'superadmin']);
    }

    public function getIsMentor(){
        $role = UserRole::findOne(['user_id' => $this->id, 'role_name' => 'mentor', 'status' => 10]);
        if($role){
            return true;
        }else{
            return false;
        }
    }

    public function getIsJury(){
        $role = UserRole::findOne(['user_id' => $this->id, 'role_name' => 'jury', 'status' => 10]);
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
