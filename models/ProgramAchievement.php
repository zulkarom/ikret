<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "program_achievement".
 *
 * @property int $id
 * @property int $program_id
 * @property int|null $program_sub
 * @property string $name
 * @property int|null $winner_count
 *
 * @property Program $program
 * @property ProgramWinnerTitle[] $winnerTitles
 */
class ProgramAchievement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program_achievement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['program_id', 'name'], 'required'],
            [['program_id', 'program_sub', 'winner_count'], 'integer'],
            [['winner_count'], 'default', 'value' => null],
            [['winner_count'], 'integer', 'min' => 0],
            [['name'], 'string', 'max' => 255],
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
            'program_id' => 'Program ID',
            'program_sub' => 'Program Sub',
            'name' => 'Name',
            'winner_count' => 'Number of Winner',
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

    public function getProgramSub()
    {
        return $this->hasOne(ProgramSub::class, ['id' => 'program_sub']);
    }

    public function getWinnerTitles()
    {
        return $this->hasMany(ProgramWinnerTitle::class, ['achievement_id' => 'id'])->orderBy(['winner_order' => SORT_ASC]);
    }
}
