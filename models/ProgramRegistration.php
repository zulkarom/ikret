<?php

namespace app\models;

use Yii;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "program_reg".
 *
 * @property int $id
 * @property int $user_id
 * @property int $program_id
 * @property string|null $project_name
 * @property string|null $group_name
 * @property int $participant_cat 1=local,2=int
 * @property string|null $advisor
 * @property string|null $institution
 * @property int|null $project_desc
 * @property int|null $competition_type
 * @property string|null $poster_file
 *
 * @property Program $program
 * @property ProgramRegMember[] $programRegMembers
 * @property User $user
 */
class ProgramRegistration extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 0;
    const STATUS_REGISTERED = 10;
    const STATUS_COMPLETE = 20;

    public $poster_instance;
    public $payment_instance;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program_reg';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'program_id', 'project_name', 'project_desc', 'competition_type', 'institution'], 'required'],

            [['user_id', 'program_id', 'participant_cat', 'competition_type', 'status'], 'integer'],

            [['institution', 'poster_file', 'project_desc'], 'string'],

            [['project_name', 'group_name', 'advisor'], 'string', 'max' => 255],

            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],

            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['program_id' => 'id']],

            [['payment_instance'], 'file',
            'maxSize' => 1024 * 1024 * 2, // 2MB
            'extensions' => 'pdf', 
            'mimeTypes' => 'application/pdf',
            ],

            [['poster_instance'], 'file',
            'maxSize' => 1024 * 1024 * 5, // 5MB
            'extensions' => 'pdf', 
            'mimeTypes' => 'application/pdf',
            ],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'program_id' => 'Program ID',
            'project_name' => 'Project Title',
            'group_name' => 'Group Name',
            'participant_cat' => 'Participant Cat',
            'advisor' => 'Advisor',
            'institution' => 'Institution',
            'project_desc' => 'Project Description',
            'competition_type' => 'Participation on Competition',
            'poster_file' => 'Upload Poster',
            'poster_instance' => 'Upload Poster',
            'payment_file' => 'Upload Proof of Payment',
            'payment_instance' => 'Upload Proof of Payment'
        ];
    }

    public static function getStatusArray(){
        return [
            0 => 'DRAFT', 
            10 => 'REGISTERED',
            20=> 'COMPLETE'
        ];
    }

    public static function getStatusColor(){
	    return [0 => 'danger', 10 => 'primary', 20 => 'success'];
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

    /**
     * Gets query for [[Program]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Program::class, ['id' => 'program_id']);
    }

    /**
     * Gets query for [[ProgramRegMembers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMembers()
    {
        return $this->hasMany(Member::class, ['program_reg_id' => 'id']);
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

    public function uploadFile($type){ // payment/poster
     
        $inst_property = $type . '_instance';
        $attr_db = $type . '_file';
        $name = Yii::$app->user->identity->matric . '_' . time();
        $path =  $this->program_id . '/'.$type;
        $instance = UploadedFile::getInstance($this, $inst_property);
        if($instance){
            
            $old_path = Yii::getAlias('@upload/' . $this->$attr_db);
                if (is_file($old_path)) {
                    unlink($old_path);
                }
            
            $directory = Yii::getAlias('@upload/' . $path. '/');
            if (!is_dir($directory)) {
                FileHelper::createDirectory($directory);
            }

            $ext = $instance->extension;
            $fileName = $name.'.' . $ext;
            $filePath = $directory . $fileName;
                if ($instance->saveAs($filePath)) {
                    //assigning value here
                    $this->$attr_db =  $path . '/' . $fileName;
                }
        }
    }
}
