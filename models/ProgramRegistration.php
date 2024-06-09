<?php

namespace app\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
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
    public $mentor_main;
    public $mentor_co;
    public $purata;

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
            [self::getProgramRequiredFields(1), 'required', 'on' => 'program1'],
            [self::getProgramRequiredFields(2), 'required', 'on' => 'program2'],
            [self::getProgramRequiredFields(3), 'required', 'on' => 'program3'],
            [self::getProgramRequiredFields(4), 'required', 'on' => 'program4'],
            [self::getProgramRequiredFields(5), 'required', 'on' => 'program5'],
            [self::getProgramRequiredFields(6), 'required', 'on' => 'program6'],

            [['user_id', 'program_id'], 'required', 'on' => 'draft'],

            [['user_id', 'program_id', 'participant_cat_local', 'competition_type', 'advisor_dropdown', 'status', 'participant_cat_umk', 'mentor_main', 'mentor_co', 'participant_cat_group', 'program_sub', 'award', 'participant_mode', 'participant_program'], 'integer'],

            [['institution', 'poster_file', 'project_desc', 'booth_number', 'nric', 'other_program', 'group_code'], 'string'],

            [['project_name', 'group_name', 'advisor'], 'string', 'max' => 255],

            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],

            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['program_id' => 'id']],

            [['score'], 'number'],

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
            'program_sub' => 'Category of Competition',
            'booth_number' => 'Group ID/Booth ID', //for dropdownlist
            'advisor_dropdown' => 'Lecturer\'s Name',
            'nric' => 'Identification Card Number',
            'participant_mode' => 'Mode of Participation',
            'participant_program' => 'Participant\'s Program',
            'group_member' => 'Individual/ Group Members',
            'mentor_main' => 'Main Mentor (optional)',
            'mentor_co' => 'Co Mentor (optional)',
            'group_code' => 'Group ID' //textinput
            
        ];
    }

    public static function getStatusArray(){
        return [
            self::STATUS_DRAFT => 'DRAFT', 
            self::STATUS_REGISTERED => 'REGISTERED',
            self::STATUS_COMPLETE => 'COMPLETE'
        ];
    }

    public static function getStatusColor(){
	    return [self::STATUS_DRAFT => 'danger', self::STATUS_REGISTERED => 'primary', self::STATUS_COMPLETE => 'success'];
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

    public static function getProgramRequiredFields($program_id){
        $array = [];
        switch($program_id){
            case 1: //impact
            $array = ['project_name', 'project_desc', 'participant_cat_local', 'competition_type', 'group_member'];
            break;

            case 2: //come
            $array = ['project_name', 'participant_cat_group', 'program_sub', 'group_member', 'group_code'];
            break;

            case 3: //neweek
            $array = ['advisor_dropdown', 'booth_number', 'group_member'];
            break;

            case 4: //aifif
            $array = ['participant_mode', 'participant_cat_umk', 'participant_program', 'institution'];
            break;

            case 5: //rise
            $array = ['project_name', 'participant_cat_group', 'group_member', 'group_code'];
            break;

            case 6: //jfed
            $array = ['project_name', 'group_member', 'group_code'];
            break;
        }
        return $array;
    }

    public static function getProgramFields($program_id){
        $array = [];
        switch($program_id){
            case 1: //impact
            $array = ['project_name', 'project_desc', 'participant_cat_local', 'competition_type', 'group_member', 'mentor_main', 'mentor_co'];
            break;

            case 2: //come
            $array = ['project_name', 'participant_cat_group', 'program_sub', 'group_member', 'mentor_main', 'mentor_co', 'group_code', 'group_name'];
            break;

            case 3: //neweek
            $array = ['advisor_dropdown', 'booth_number', 'group_member', 'mentor_main', 'mentor_co'];
            break;

            case 4: //aifif
            $array = ['participant_mode', 'participant_cat_umk', 'participant_program', 'institution', 'mentor_main', 'mentor_co'];
            break;

            case 5: //rise
            $array = ['project_name', 'participant_cat_group', 'group_member', 'mentor_main', 'mentor_co', 'group_code', 'group_name'];
            break;

            case 6: //jfed
            $array = ['project_name', 'group_member', 'mentor_main', 'mentor_co', 'group_code', 'group_name'];
            break;
        }
        return $array;
    }

    public function getShortFields(){
        $program_id = $this->program_id;
        $array = [];
        switch($program_id){
            case 1: //impact
            $array = ['project_name', 'competition_type'];
            break;

            case 2: //come
            $array = ['project_name', 'program_sub',  'group_code', 'group_name'];
            break;

            case 3: //neweek
            $array = ['booth_number'];
            break;

            case 4: //aifif 
            $array = ['participant_mode', 'participant_program'];
            break;

            case 5: //rise
            $array = ['project_name', 'group_code', 'group_name'];
            break;

            case 6: //jfed
            $array = ['project_name', 'group_code', 'group_name'];
            break;
        }
        return $array;
    }

    public function getParticipantText(){
        $kira = count($this->members);
        $html = $this->user->fullname;
        if($kira > 1){
            $mem = $kira - 1;
            $html .= ' & ' . $mem . ' OTHERS';
        }
        return $html;
    }

    public function getShortFieldsHtml(){
        $array = $this->getShortFields();

        //mula2 dapatkan nama participant & count group
        $html = $this->participantText;
        //ok project name
        $html .= '<ul>';
        $sub ='';
        if(in_array('program_sub', $array)){
            if($this->programSub){
                $sub = ' / '.$this->programSub->sub_name;
            }
        }
        $html .= '<li><i>Program:</i> '.$this->program->program_abbr. $sub . '</li>';
        
        if(in_array('project_name', $array)){
            $html .= '<li><i>Project Title:</i> '.$this->project_name.'</li>';
        }
        
        if(in_array('group_code', $array)){
           $html .= '<li><i>Group ID:</i> '.$this->group_code.'</li>';
        }

        if(in_array('booth_number', $array)){
            $html .= '<li><i>Booth Number:</i> '.$this->booth_number.'</li>';
        }
        
        if(in_array('group_name', $array)){
            if($this->group_name){
                $html .= '<li><i>Group Name:</i> '.$this->group_name.'</li>';
            }
        }

        if(in_array('competition_type', $array)){
            $html .= '<li><i>Competition Type:</i> '.$this->getListLabel('listCompetitionType', $this->competition_type).'</li>';
        }
        
        $html .= '<ul>';


        return $html;
    }

    public function getListLabel($fname, $key){
        $text = '';
        $array = $this->$fname();
        if(array_key_exists($key, $array)){
            $text = $array[$key];
        }
        return $text;
    }

    public static function listCategoryComeXX(){
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
            $array['NW'.$x] = 'NW'.$x;
        }
        return $array;
    }

    /**
     * GOLD	100 - 80
     * SILVER	79 - 60
     * BRONZE	59 - 0
     */
    public static function listAward(){
        return [
            80 => 'GOLD',
            60 => 'SILVER',
            0 => 'BRONZE'
        ];
    }

    public function awardText(){
        $text = '';
        $array = $this->listAward();
        if(array_key_exists($this->award, $array)){
            $text = $array[$this->award];
        }
        return $text;
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

    public function getMentorMain()
    {
        return $this->hasOne(User::class, ['id' => 'mentor_main']);
    }

    public function getMentorCo()
    {
        return $this->hasOne(User::class, ['id' => 'mentor_co']);
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
        return $this->hasMany(JuryAssign::class, ['reg_id' => 'id']);
    }

    public function getJuriesCompleted(){
        return JuryAssign::find()
        ->where(['reg_id' => $this->id, 'status' => 20])
        ->all();
    }

    public function getAchievements()
    {
        return $this->hasMany(ParticipantAchieve::class, ['program_reg_id' => 'id']);
    }

    public function mentorList(){
        $list = User::find()
        ->from('user u')
        ->innerJoin('user_role r','r.user_id = u.id')
        ->andWhere(['r.role_name' => 'mentor'])
        ->all();
        
        return ArrayHelper::map($list,'id', 'fullname');
    }

    

    public function getAverageJuriesScore(){
        $juries = $this->juries;
        $kira_juri = 0;
        $score = 0;

        if($juries){
            foreach($juries as $j){
                if($j->status == 20){
                    $kira_juri++;
                    $score += $j->score;
                }
            }
        }

        if($kira_juri == 0){
            return 0;
        }
        $avg = $score / $kira_juri;
        $avg = round($avg,2);
        $avg = $avg + 0;
        return $avg;
    }

    public function setScoreAndAward(){
        $score = $this->averageJuriesScore;
        $this->score = $score;
        $this->award = self::calcAward($score);
    }

    public static function calcAward($per){
        $list = ProgramRegistration::listAward();
        foreach($list as $key => $val){
            if($per >= $key){
                return $key;
            }
        }
        return 0;
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
