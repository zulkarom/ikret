<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "program_sub".
 *
 * @property int $id
 * @property string $sub_name
 * @property string|null $advisor
 * @property int $program_id
 *
 * @property Program $program
 */
class ProgramSub extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program_sub';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['sub_name', 'program_id'], 'required'],
            [['program_id'], 'integer'],
            [['sub_name', 'sub_abbr', 'advisor'], 'string', 'max' => 255],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['program_id' => 'id']],
        ];

        $table = Yii::$app->db->schema->getTableSchema(self::tableName());
        if($table && $table->getColumn('is_active')){
            $rules[] = [['is_active'], 'integer'];
            $rules[] = [['is_active'], 'default', 'value' => 1];
        }

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sub_name' => 'Sub Name',
            'sub_abbr' => 'Sub Abbr',
            'advisor' => 'Advisor',
            'program_id' => 'Program ID',
            'is_active' => 'Active',
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

    public function getSubProgramText(){
        $adv = '';
        if($this->advisor){
            $adv = ' / '. $this->advisor;
        }
        return $this->sub_name . $adv;
    }

    public function getProgramRubrics()
    {
        return $this->hasMany(ProgramRubric::class, ['program_sub' => 'id']);
    }
}
