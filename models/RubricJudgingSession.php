<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rubric_judging_session".
 *
 * @property int $id
 * @property int $rubric_id
 * @property string $session_name
 * @property string|null $datetime_start
 * @property string|null $datetime_end
 * @property string|null $location
 * @property int $mode
 * @property int $sort_order
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Rubric $rubric
 */
class RubricJudgingSession extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'rubric_judging_session';
    }

    public function rules()
    {
        return [
            [['rubric_id', 'session_name'], 'required'],
            [['rubric_id', 'mode', 'sort_order', 'created_at', 'updated_at'], 'integer'],
            [['datetime_start', 'datetime_end'], 'safe'],
            [['session_name', 'location'], 'string', 'max' => 255],
            [['rubric_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rubric::class, 'targetAttribute' => ['rubric_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rubric_id' => 'Rubric ID',
            'session_name' => 'Session Name',
            'datetime_start' => 'Datetime Start',
            'datetime_end' => 'Datetime End',
            'location' => 'Location',
            'mode' => 'Mode',
            'sort_order' => 'Sort Order',
        ];
    }

    public function getRubric()
    {
        return $this->hasOne(Rubric::class, ['id' => 'rubric_id']);
    }

    public static function listMode()
    {
        return [
            1 => 'Physical',
            2 => 'Online',
        ];
    }
}
