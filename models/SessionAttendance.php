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
}
