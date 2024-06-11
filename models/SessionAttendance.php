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
        $start = strtotime($session->datetime_start);
    $end = strtotime($session->datetime_end);
    $valid = time() >= $start && time() <= $end;
    if($session){
        if($valid){
            $ada = SessionAttendance::find()->alias('a')
            ->where(['a.session_id' => $session->id, 'a.user_id' => $user_id])
            ->one();
            if($ada){
                Yii::$app->session->addFlash('error', "Already Recorded");
            }else{
                return true;
            }
        }else{
            Yii::$app->session->addFlash('error', "Invalid Session Time");
        }
    }
    return false;
    }
}
