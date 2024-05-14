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
    public $group_member;

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
            [self::getProgramFields(1), 'required', 'on' => 'program1'],
            [self::getProgramFields(2), 'required', 'on' => 'program2'],
            [self::getProgramFields(3), 'required', 'on' => 'program3'],
            [self::getProgramFields(4), 'required', 'on' => 'program4'],
            [self::getProgramFields(5), 'required', 'on' => 'program5'],
            [self::getProgramFields(6), 'required', 'on' => 'program6'],

            [['user_id', 'program_id'], 'required', 'on' => 'draft'],

            [['user_id', 'program_id', 'participant_cat_local', 'competition_type', 'advisor_dropdown', 'status', 'participant_cat_umk'], 'integer'],

            [['institution', 'poster_file', 'project_desc', 'booth_number', 'nric', 'other_program'], 'string'],

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
            'participant_cat_local' => 'Category of Participant',
            'participant_cat_group' => 'Category of Participant',
            'participant_cat_umk' => 'Category of Participant',
            'advisor' => 'Name of Project Advisor (Lecturer)',
            'institution' => 'Institution',
            'project_desc' => 'Project Description',
            'competition_type' => 'Participation on Competition',
            'poster_file' => 'Upload Poster',
            'poster_instance' => 'Submission of Poster',
            'payment_file' => 'Proof of Payment',
            'payment_instance' => 'Upload Proof of Payment',
            'competition_cat' => 'Category of Competition',
            'booth_number' => 'Booth ID',
            'advisor_dropdown' => 'Lecturer\'s Name',
            'nric' => 'Identification Card Number',
            'participant_mode' => 'Mode of Participation',
            'participant_program' => 'Participant\'s Program',
            'group_member' => 'Individual/ Group Members'
            
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

    public static function getProgramFields($program_id){
        $array = [];
        switch($program_id){
            case 1:
            $array = ['project_name', 'project_desc', 'participant_cat_local', 'competition_type', 'institution', 'advisor', 'group_member', 'poster_file', 'payment_file'];
            break;

            case 2:
            $array = ['project_name', 'participant_cat_group', 'competition_cat', 'group_member', 'payment_file'];
            break;

            case 3:
            $array = ['advisor_dropdown', 'booth_number', 'group_member', 'payment_file'];
            break;

            case 4:
            $array = ['nric', 'participant_mode', 'participant_cat_umk', 'participant_program', 'institution', 'payment_file'];
            break;

            case 5:
            $array = ['project_name', 'participant_cat_group', 'group_member', 'payment_file'];
            break;

            case 6:
            $array = ['project_name', 'group_member'];
            break;
        }
        return $array;
    }

    public function getListLabel($fname, $key){
        $text = '';
        $array = $this->$fname();
        if(array_key_exists($key, $array)){
            $text = $array[$key];
        }
        return $text;
    }

    public static function listCategoryCome(){
        return [
            1 => 'E-Preneur / Dr. Fatihah Mohd & Dr. Yusmazida',
            2 => 'Business Product Pitching / Dr. Noor Raihani Binti Zainol',
            3 => 'Product Marketing Creative Video Competition / Dr. Azira Hanani Binti Ab Rahman',
            4 => 'Most Viable Student  /  Dr. Wan Farha Binti Wan Zulkiffli',
            5 => 'Takaful Product Innovation / Mrs. Farah Hanan Binti Muhamad',
            6 => 'TaxPro Idea Competition / Dr. Amira Binti Jamil',
            7 => 'Poster Presentation / Dr. Siti Fariha Binti Muhamad'
        ];
    }

    public static function listNeweekAdvisor(){
        return [
            1 => '(L1) DR NURUL IZYAN BINTI MAT DAUD',
            2 => '(L2) PN FARAH HANAN BINTI MUHAMAD/ DR NURUL IZYAN BINTI MAT DAUD',
            3 => '(L3) DR SOLOMON GBENE ZATO',
            4 => '(L4) PN NIK MADEEHA BINTI NIK MOHD MUNIR',
            5 => '(H1) DR NOR ASMA BINTI AHMAD',
            6 => '(H2) DR MOHD NAZRI BIN MUHAYIDDIN/ PN NUR \'AMIRAH BINTI MOHD YAZIZ',
            7 => '(H3) PROF DR NARESH KUMAR A/L SAMY',
            8 => '(D1) EN. ROOSHIHAN BIN ABDUL RAHIM MERICAN/ EN. TS. MAHATHIR BIN MUHAMAD'
        ];
    }

    public static function listParticipantMode(){
        return [
            1 => 'Physical',
            2 => 'Online'
        ];
    }

    public static function listParticipantLocal(){
        return [
            1 => 'Local',
            2 => 'International'
        ];
    }

    public static function listParticipantGroup(){
        return [
            1 => 'Individual', 
            2 => 'Group'
        ];
    }

    public static function listCompetitionType(){
        return [
            1 => 'Community Project Ideation', 
            2 => 'Community Project Implementation'
        ];
    }

    public static function listParticipantUMK(){
        return [
            1 => 'UMK Student',
            2 => 'Non-UMK Student'
        ];
    }

    public static function listParticipantProgram(){
        return [
            1 => 'SAA',
            2 => 'SAB',
            3 => 'SAE',
            4 => 'SAK',
            5 => 'SAL',
            6 => 'SAR',
            99 => 'Others',
        ];
    }

    public static function listNeweekBooth(){
        $array = [];
        for($x=1;$x<=104;$x++){
            $array[] = 'NW'.$x;
        }
        return $array;
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

    public function getJuries()
    {
        return $this->hasMany(Jury::class, ['reg_id' => 'id']);
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
