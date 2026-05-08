<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "session_attendance".
 *
 * @property int $id
 * @property int $session_id
 * @property int $user_id
 * @property string $scanned_at
 *
 * @property Session $session
 * @property User $user
 */
class SessionAttendance extends \yii\db\ActiveRecord
{
    public $user_matric;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'session_attendance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['session_id', 'user_id', 'scanned_at'], 'required'],
            [['session_id', 'user_id'], 'integer'],
            [['scanned_at'], 'safe'],

            [['user_matric'], 'string'],

            [['session_id'], 'validateSessionTime'],
            [['user_id'], 'validateUniqueAttendance'],
            [['session_id'], 'exist', 'skipOnError' => true, 'targetClass' => Session::class, 'targetAttribute' => ['session_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'session_id' => 'Session ID',
            'user_id' => 'User ID',
            'scanned_at' => 'Scanned At',
        ];
    }

    /**
     * Gets query for [[Session]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSession()
    {
        return $this->hasOne(Session::class, ['id' => 'session_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function listSessions(){
        $list = Session::find()->all();
        return ArrayHelper::map($list, 'id', 'session_name');
    }

    public function validateAttendance($session, $user_id){
        date_default_timezone_set("Asia/Kuala_Lumpur");
        $this->session_id = $session ? $session->id : null;
        $this->user_id = $user_id;

        if($this->validate(['session_id', 'user_id'])){
            return true;
        }

        foreach($this->getFirstErrors() as $message){
            Yii::$app->session->addFlash('error', $message);
            break;
        }

        return false;
    }

    public function validateSessionTime($attribute)
    {
        if($this->hasErrors($attribute)){
            return;
        }

        date_default_timezone_set("Asia/Kuala_Lumpur");
        $session = $this->session_id ? Session::findOne($this->session_id) : null;

        if(!$session){
            $this->addError($attribute, 'Invalid Session Code');
            return;
        }

        if((int)$session->allow_scan_outside_duration === 1){
            return;
        }

        $start = strtotime($session->datetime_start);
        $end = strtotime($session->datetime_end);
        if((int)$session->allow_scan_1_hour_after_event === 1 && $end !== false){
            $end += 3600;
        }

        if($start === false || $end === false || time() < $start || time() > $end){
            $this->addError($attribute, 'Invalid Session Time');
        }
    }

    public function validateUniqueAttendance($attribute)
    {
        if($this->hasErrors($attribute) || $this->hasErrors('session_id')){
            return;
        }

        $query = static::find()
            ->where(['session_id' => $this->session_id, 'user_id' => $this->user_id]);

        if(!$this->isNewRecord){
            $query->andWhere(['<>', 'id', $this->id]);
        }

        if($query->exists()){
            $this->addError($attribute, 'Already Recorded');
        }
    }
}
