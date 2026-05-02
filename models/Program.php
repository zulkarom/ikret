<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "program".
 *
 * @property int $id
 * @property string $program_name
 * @property boolean $public_registration_enabled
 */
class Program extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['program_name','date_start', 'date_end', 'reg_info'], 'required'],

            [['date_start', 'date_end'], 'safe'],

            [['public_reg_enabled'], 'integer'],

            [['reg_closed'], 'integer'],

            [['is_active'], 'integer'],

            [['public_reg_enabled'], 'default', 'value' => 1],

            [['reg_closed'], 'default', 'value' => 0],

            [['is_active'], 'default', 'value' => 1],

            [['reg_info', 'program_abbr'], 'string'],
            [['program_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'program_name' => 'Program Name',
            'reg_info' => 'Registration Information',
            'public_reg_enabled' => 'Enable Public Registration',
            'reg_closed' => 'Close Registration',
            'is_active' => 'Active'
        ];
    }

    public static function listPublicPrograms()
    {
        return self::find()
        ->where(['public_reg_enabled' => 1])
        ->orderBy(['date_start' => SORT_ASC, 'id' => SORT_ASC])
        ->all();
    }

    public function getPublicRegistrationLabel()
    {
        return (int)$this->public_reg_enabled === 1 ? 'Enabled' : 'Disabled';
    }

    public static function listPrograms(){
        return ArrayHelper::map(self::find()->all(),'id', 'program_name');
    }

    public function getProgramSubs()
    {
        return $this->hasMany(ProgramSub::class, ['program_id' => 'id']);
    }

    public static function programNameLong($program_id, $program_sub = null){
        $str = '';
        $program = Program::findOne($program_id);
        if($program){
            $str .= $program->program_name;
        }

        if($program_sub){
            $sub = ProgramSub::findOne($program_sub);
            if($sub){
                $str .= '<br />(' . $sub->sub_name . ')';
            }
        }

        return $str;
    }


    public function listSubPrograms(){
        $query = $this->getProgramSubs();

        $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
        if($subTable && $subTable->getColumn('is_active')){
            $query->andWhere(['is_active' => 1]);
        }

        $list = $query->all();
        return ArrayHelper::map($list, 'id', 'subProgramText');
    }

    public function getProgramRubrics()
    {
        return $this->hasMany(ProgramRubric::class, ['program_id' => 'id']);
    }

    public function getProgramRubricsSub($sub)
    {
        return $this->hasMany(ProgramRubric::class, ['program_id' => 'id'])->where(['program_sub' => $sub]);
    }

    public function getProgramStages()
    {
        return $this->hasMany(ProgramStage::class, ['program_id' => 'id']);
    }

    public function getProgramMethods()
    {
        return $this->hasMany(ProgramMethod::class, ['program_id' => 'id']);
    }

    public function getProgramAchievements()
    {
        return $this->hasMany(ProgramAchievement::class, ['program_id' => 'id']);
    }
}
