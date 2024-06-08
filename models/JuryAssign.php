<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "program_reg_jury".
 *
 * @property int $id
 * @property int $reg_id
 * @property int $user_id
 * @property string|null $stage
 * @property int|null $rubric_id
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property ProgramReg $reg
 * @property User $user
 */
class JuryAssign extends \yii\db\ActiveRecord
{
    public $users;
    public $keep_data;
    public $keep_open;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program_reg_jury';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reg_id', 'user_id', 'rubric_id'], 'required'],

            [['users', 'rubric_id'], 'required', 'on' => 'assign'],

            [['reg_id', 'user_id', 'rubric_id', 'stage'], 'required', 'on' => 'stage'],

            ['users', 'each', 'rule' => ['integer']],

            [['reg_id', 'user_id', 'rubric_id', 'created_at', 'updated_at', 'method', 'stage', 'status', 'keep_data', 'keep_open'], 'integer'],

            [['location'], 'string', 'max' => 255],

            [['link', 'note'], 'string'],

            [['score'], 'number'],

            [['link'], 'url', 'message' => 'Not valid url, begin with e.g. https:// ... '],

            [['date_start', 'date_end'], 'safe'],

            [['date_start', 'date_end'], 'validateDates'],

            [['reg_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramRegistration::class, 'targetAttribute' => ['reg_id' => 'id']],

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
            'reg_id' => 'Reg ID',
            'user_id' => 'Jury',
            'users' => 'Juries',
            'stage' => 'Stage/Level',
            'location' => 'Location/Platform',
            'method' => 'Method of Evaluation',
            'date_start' => 'Start Date',
            'date_end' => 'End Date',
            'rubric_id' => 'Rubric',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'statusLabel' => 'Status'
        ];
    }

    public static function getStatusArray(){
        return [
            0 => 'ASSIGNED', 
            10 => 'JUDGING',
            20=> 'COMPLETE'
        ];
    }

    public static function getStatusColor(){
	    return [0 => 'warning', 10 => 'primary', 20 => 'success'];
	}

    public function getStatusText(){
        $text = '';
        if(array_key_exists($this->status, $this->statusArray)){
            $text = $this->statusArray[$this->status];
        }
        return $text;
    }

    public function getStatusLabel(){
        $color = "";
        if(array_key_exists($this->status, $this->statusColor)){
            $color = $this->statusColor[$this->status];
        }
        return '<span class="badge bg-'.$color.'">'. $this->statusText .'</span>';
    }

    public static function listStage(){
        return [ 0 => 'Not Applicable', 1 => 'Stage 1', 2 => 'Stage 2'];
    }

    public function validateDates(){
        if(strtotime($this->date_end) < strtotime($this->date_start)){
            $this->addError('date_start','Please give correct Start and End dates');
            $this->addError('date_end','Please give correct Start and End dates');
        }
    }

    public function getStageText(){
        $text = '';
        $key = $this->stage;
        $array = $this->listStage();
        if(array_key_exists($key, $array)){
            $text = $array[$key];
        }
        return $text;
    }

    public static function listMethod(){
        return [1=>'Pitching',
                2=>'Showcase',
                3=>'Poster Presentation',
                4=>'Poster Submission',
                5=>'Gerai Jualan'];
    }

    public function getMethodText(){
        $text = '';
        $key = $this->method;
        $array = $this->listMethod();
        if(array_key_exists($key, $array)){
            $text = $array[$key];
        }
        return $text;
    }

    public function getDateText(){
        return Common::dateStartEndFormat($this->date_start,$this->date_end);
    }

    /**
     * Gets query for [[Reg]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegistration()
    {
        return $this->hasOne(ProgramRegistration::class, ['id' => 'reg_id']);
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

    public function getRubric()
    {
        return $this->hasOne(Rubric::class, ['id' => 'rubric_id']);
    }

    public static function listRubrics(){
        $list = Rubric::find()->all();
        return ArrayHelper::map($list, 'id', 'rubric_name');
    }

    public function getRubricAnswer(){
        return $this->hasOne(RubricAnswer::class, ['assignment_id' => 'id']);
    }

    public function infoHtml($admin = false){
        $html = '<li>'. $this->user->fullname .'
            <ul>';
                if($admin){
                    $html .= '<li><i>Status:</i> '.$this->statusText.'</li>';
                }
                if($this->rubric){
                    $html .= '<li><i>Rubric:</i> '.$this->rubric->rubric_name.'</li>';
                }
                if($this->stage > 0){
                    $html .= '<li><i>Stage/Level:</i> '.$this->stageText.'</li>';
                }
                if($this->method){
                    $html .= '<li><i>Method:</i> '.$this->methodText.'</li>';
                }
                if($this->location){
                    $html .= '<li><i>Location/Platform:</i> '.$this->location.'</li>';
                }
                if($this->link){
                    $html .= '<li><i>Link:</i> ' . Html::a('Click Here', $this->link, ['target' => '_blank']) .'</li>';
                }
                if($this->date_start){
                    $html .= '<li><i>Date:</i> '.$this->dateText.'</li>';
                }
                if($this->note){
                    $html .= '<li><i>Note:</i> '.$this->note.'</li>';
                }
                if($admin && $this->status == 0){
                    //kena cari program & sub
                    $p = $this->registration->program_id;
                    $sub = $this->registration->program_sub;
                    $url = ['/program-registration/jury-delete', 'id' => $this->id, 'p' => $p];
                    if($sub){
                        $url = ['/program-registration/jury-delete', 'id' => $this->id, 'p' => $p, 's' => $sub];
                    }


                    $html .= '<li><i>Action:</i> <a href="'.Url::to($url).'" class="btn btn-outline-danger btn-sm">Delete</a></li>';
                }
                if($admin && $this->status == 20){
                    $html .= '<li><i>Score:</i> '.$this->score.'</li>';
                }
        $html .= '</ul>
        </li>';
        return $html;
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
}
