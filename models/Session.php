<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "session".
 *
 * @property int $id
 * @property string $session_name
 * @property string|null $speaker
 * @property int|null $program_id
 * @property int|null $program_sub
 * @property string|null $datetime_start
 * @property string|null $datetime_end
 * @property int $allow_scan_outside_duration
 * @property int $allow_scan_1_hour_after_event
 * @property string|null $token
 *
 * @property Program $program
 * @property ProgramSub $programSub
 * @property SessionAttendance[] $sessionAttendances
 */
class Session extends \yii\db\ActiveRecord
{
    public $reg_id;
    public $user_id;
    public $fullname;
    public $program_name;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'session';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['session_name', 'datetime_start', 'datetime_end'], 'required'],
            [['program_id', 'program_sub', 'allow_scan_outside_duration', 'allow_scan_1_hour_after_event'], 'integer'],
            [['datetime_start', 'datetime_end'], 'safe'],
            [['token', 'session_name', 'speaker'], 'string'],
            [['allow_scan_outside_duration', 'allow_scan_1_hour_after_event'], 'default', 'value' => 0],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['program_id' => 'id']],
            [['program_sub'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramSub::class, 'targetAttribute' => ['program_sub' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'session_name' => 'Session Name',
            'speaker' => 'Speaker',
            'program_id' => 'Program ID',
            'program_sub' => 'Program Sub',
            'datetime_start' => 'Start',
            'datetime_end' => 'End',
            'allow_scan_outside_duration' => 'Allow scan outside duration',
            'allow_scan_1_hour_after_event' => 'Allow scan 1 hour after event',
            'token' => 'Token',
        ];
    }

    /**
     * Gets query for [[Program]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Program::class, ['id' => 'program_id']);
    }

    public function getProgramNameShort(){
        if($this->program){
            $sub = '';
        if($this->programSub){
            $sub = ' / ' . $this->programSub->sub_abbr;
        }
        return $this->program->program_abbr . $sub;
        }
        return;
        
    }

    /**
     * Gets query for [[ProgramSub]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgramSub()
    {
        return $this->hasOne(ProgramSub::class, ['id' => 'program_sub']);
    }

    /**
     * Gets query for [[SessionAttendances]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSessionAttendances()
    {
        return $this->hasMany(SessionAttendance::class, ['session_id' => 'id']);
    }

    public function getSessionAttendancesRecent()
    {
        return SessionAttendance::find()
        ->where(['session_id' => $this->id])
        ->limit(5)
        ->orderBy('id DESC')
        ->all();
    }
}
