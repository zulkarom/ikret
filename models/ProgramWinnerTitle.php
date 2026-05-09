<?php

namespace app\models;

/**
 * This is the model class for table "program_winner_title".
 *
 * @property int $id
 * @property int $achievement_id
 * @property int $winner_order
 * @property string $title_name
 *
 * @property ProgramAchievement $achievement
 */
class ProgramWinnerTitle extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'program_winner_title';
    }

    public function rules()
    {
        return [
            [['achievement_id', 'winner_order'], 'required'],
            [['achievement_id', 'winner_order'], 'integer'],
            [['title_name'], 'string', 'max' => 255],
            [['achievement_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramAchievement::class, 'targetAttribute' => ['achievement_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'achievement_id' => 'Achievement',
            'winner_order' => 'Winner No.',
            'title_name' => 'Winner Title',
        ];
    }

    public function getAchievement()
    {
        return $this->hasOne(ProgramAchievement::class, ['id' => 'achievement_id']);
    }
}
