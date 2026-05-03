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
    const STATUS_NULLIFIED = 15;
    const STATUS_COMPLETE = 20;

    public $poster_instance;
    public $payment_instance;
    public $abstract_instance;
    public $video_instance;
    public $group_member;
    public $mentor_main;
    public $mentor_co;
    public $purata;
    public $achieve_name;
    public $edit_password;
    public $edit_password_confirm;

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
            [self::getProgramRequiredFields(7), 'required', 'on' => 'program7'],
            [self::getPublicRequiredFields(1, true), 'required', 'on' => 'public_program1_create'],
            [self::getPublicRequiredFields(2, true), 'required', 'on' => 'public_program2_create'],
            [self::getPublicRequiredFields(3, true), 'required', 'on' => 'public_program3_create'],
            [self::getPublicRequiredFields(4, true), 'required', 'on' => 'public_program4_create'],
            [self::getPublicRequiredFields(5, true), 'required', 'on' => 'public_program5_create'],
            [self::getPublicRequiredFields(6, true), 'required', 'on' => 'public_program6_create'],
            [self::getPublicRequiredFields(7, true), 'required', 'on' => 'public_program7_create'],
            [self::getPublicRequiredFields(1, false), 'required', 'on' => 'public_program1_update'],
            [self::getPublicRequiredFields(2, false), 'required', 'on' => 'public_program2_update'],
            [self::getPublicRequiredFields(3, false), 'required', 'on' => 'public_program3_update'],
            [self::getPublicRequiredFields(4, false), 'required', 'on' => 'public_program4_update'],
            [self::getPublicRequiredFields(5, false), 'required', 'on' => 'public_program5_update'],
            [self::getPublicRequiredFields(6, false), 'required', 'on' => 'public_program6_update'],
            [self::getPublicRequiredFields(7, false), 'required', 'on' => 'public_program7_update'],

            [['user_id', 'program_id'], 'required', 'on' => 'draft'],
            [['project_name'], 'required', 'on' => 'draft'],

            [['contact_email', 'edit_password', 'edit_password_confirm'], 'required', 'on' => ['public_create', 'public_program1_create', 'public_program2_create', 'public_program3_create', 'public_program4_create', 'public_program5_create', 'public_program6_create', 'public_program7_create']],
            [['contact_email'], 'required', 'on' => ['public_update', 'public_program1_update', 'public_program2_update', 'public_program3_update', 'public_program4_update', 'public_program5_update', 'public_program6_update', 'public_program7_update']],
            [['edit_password'], 'required', 'on' => ['public_create', 'public_program1_create', 'public_program2_create', 'public_program3_create', 'public_program4_create', 'public_program5_create', 'public_program6_create', 'public_program7_create']],
            [['edit_password'], 'string', 'min' => 4, 'on' => ['public_create', 'public_update', 'public_program1_create', 'public_program2_create', 'public_program3_create', 'public_program4_create', 'public_program5_create', 'public_program6_create', 'public_program7_create', 'public_program1_update', 'public_program2_update', 'public_program3_update', 'public_program4_update', 'public_program5_update', 'public_program6_update', 'public_program7_update']],
            [['edit_password_confirm'], 'compare', 'compareAttribute' => 'edit_password', 'message' => 'Password / PIN confirmation does not match.', 'on' => ['public_create', 'public_update', 'public_program1_create', 'public_program2_create', 'public_program3_create', 'public_program4_create', 'public_program5_create', 'public_program6_create', 'public_program7_create', 'public_program1_update', 'public_program2_update', 'public_program3_update', 'public_program4_update', 'public_program5_update', 'public_program6_update', 'public_program7_update']],
            [['contact_email'], 'validateUniquePublicEmail', 'on' => ['public_create', 'public_update', 'public_program1_create', 'public_program2_create', 'public_program3_create', 'public_program4_create', 'public_program5_create', 'public_program6_create', 'public_program7_create', 'public_program1_update', 'public_program2_update', 'public_program3_update', 'public_program4_update', 'public_program5_update', 'public_program6_update', 'public_program7_update']],

            [['program_sub'], 'validateProgramSubActive'],

            [['user_id', 'program_id', 'participant_cat_local', 'competition_type', 'advisor_dropdown', 'status', 'participant_cat_umk', 'mentor_main', 'mentor_co', 'participant_cat_group', 'program_sub', 'award', 'participant_mode', 'participant_program'], 'integer'],

            [['participant_cat_program'], 'integer'],

            [['competition_cat_program'], 'integer'],

            [['contact_person', 'contact_no', 'contact_email'], 'string', 'max' => 255],

            [['contact_email'], 'email'],

            [['institution', 'poster_file', 'abstract_file', 'video_link', 'payment_file', 'project_desc', 'booth_number', 'nric', 'other_program', 'group_code'], 'string'],

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
            'maxSize' => 1024 * 1024 * 15, // 15MB
            'extensions' => 'pdf, pptx, jpg, jpeg, png', 
            ],

            [['abstract_instance'], 'required',
            'on' => ['public_program1_create', 'public_program2_create', 'public_program3_create', 'public_program4_create', 'public_program5_create', 'public_program6_create', 'public_program7_create', 'public_program1_update', 'public_program2_update', 'public_program3_update', 'public_program4_update', 'public_program5_update', 'public_program6_update', 'public_program7_update'],
            'when' => function($model){
                return empty($model->abstract_file);
            },
            ],

            [['payment_instance'], 'required',
            'on' => ['public_program1_create', 'public_program2_create', 'public_program3_create', 'public_program4_create', 'public_program5_create', 'public_program6_create', 'public_program7_create', 'public_program1_update', 'public_program2_update', 'public_program3_update', 'public_program4_update', 'public_program5_update', 'public_program6_update', 'public_program7_update'],
            'when' => function($model){
                return empty($model->payment_file);
            },
            ],

            [['poster_instance'], 'required',
            'when' => function($model){
                return $model->scenario !== 'draft' && (int)$model->program_id === 7 && (int)$model->participant_mode === 2 && empty($model->poster_file);
            },
            'whenClient' => 'function (attribute, value) {
  var el = document.querySelector("input[name=\"ProgramRegistration[participant_mode]\"]:checked");
  var active = document.activeElement;
  var isDraft = active && active.name === "action" && active.value === "draft";
  return !isDraft && el && String(el.value) === "2";
}',
            ],

            [['abstract_instance'], 'file',
            'maxSize' => 1024 * 1024 * 5, // 5MB
            'extensions' => 'doc, docx',
            ],

            [['participant_cat_program'], 'validateParticipantCatProgram'],

        ];
    }

    public function beforeValidate()
    {
        if(!parent::beforeValidate()){
            return false;
        }

        if($this->contact_email){
            $this->contact_email = $this->normalizeEmail($this->contact_email);
        }

        return true;
    }

    public function validateParticipantCatProgram($attribute, $params)
    {
        if((int)$this->program_id !== 7){
            return;
        }

        if(!$this->$attribute){
            return;
        }

        if(!$this->participant_mode){
            return;
        }

        $cat = ParticipantCatProgram::findOne((int)$this->$attribute);
        if(!$cat || (int)$cat->program_id !== (int)$this->program_id){
            $this->addError($attribute, 'Invalid category.');
            return;
        }

        if((int)$cat->mode !== (int)$this->participant_mode){
            $this->addError($attribute, 'Selected category is not available for the selected participation mode.');
            return;
        }

        if((int)$cat->is_active !== 1){
            $this->addError($attribute, 'Selected category is not active.');
            return;
        }
    }

    public function validateProgramSubActive($attribute, $params)
    {
        if(!$this->$attribute){
            return;
        }

        if(!$this->program_id){
            return;
        }

        $sub = ProgramSub::findOne((int)$this->$attribute);
        if(!$sub || (int)$sub->program_id !== (int)$this->program_id){
            $this->addError($attribute, 'Invalid category.');
            return;
        }

        $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
        if($subTable && $subTable->getColumn('is_active')){
            if((int)$sub->is_active !== 1){
                $this->addError($attribute, 'Selected category is not active.');
                return;
            }
        }
    }

    public function validateUniquePublicEmail($attribute, $params)
    {
        if(!$this->$attribute || !$this->program_id){
            return;
        }

        $query = self::find()
            ->where([
                'program_id' => (int)$this->program_id,
                'contact_email' => $this->normalizeEmail($this->$attribute),
            ]);

        if(!$this->isNewRecord){
            $query->andWhere(['<>', 'id', $this->id]);
        }

        $exists = $query->exists();

        if($exists){
            $this->addError($attribute, 'This email has already registered for this program. Please use the edit registration page.');
        }
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
            'contact_person' => 'Name of contact person',
            'contact_no' => 'Contact number',
            'contact_email' => 'Email',
            'project_desc' => 'Project Description',
            'competition_type' => 'Participation on Competition',
            'poster_file' => 'Upload Poster',
            'abstract_file' => 'Upload Abstract',
            'abstract_instance' => 'Upload Abstract',
            'poster_instance' => 'Submission of Poster',
            'video_link' => 'Video Link',
            'payment_file' => 'Proof of Payment',
            'payment_instance' => 'Upload Proof of Payment',
            'program_sub' => 'Category of Competition',
            'booth_number' => 'Group ID/Booth ID', //for dropdownlist
            'advisor_dropdown' => 'Lecturer\'s Name',
            'nric' => 'Identification Card Number',
            'participant_mode' => 'Mode of Participation',
            'participant_cat_program' => 'Category of Participant',
            'competition_cat_program' => 'Competition Category',
            'participant_program' => 'Participant\'s Program',
            'group_member' => 'Individual/ Group Members',
            'mentor_main' => 'Main Mentor (optional)',
            'mentor_co' => 'Co Mentor (optional)',
            'group_code' => 'Group ID',
            'edit_password' => 'Edit Password / PIN',
            'edit_password_confirm' => 'Confirm Password / PIN'
            
        ];
    }

    public function setEditPassword($password)
    {
        $this->edit_password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function validateEditPassword($password)
    {
        if(empty($this->edit_password_hash)){
            return false;
        }

        return Yii::$app->security->validatePassword($password, $this->edit_password_hash);
    }

    public function hasEditPassword()
    {
        return !empty($this->edit_password_hash);
    }

    public function normalizeEmail($email)
    {
        return mb_strtolower(trim((string)$email));
    }

    public static function getStatusArray(){
        return [
            self::STATUS_DRAFT => 'DRAFT', 
            self::STATUS_REGISTERED => 'REGISTERED',
            self::STATUS_NULLIFIED => 'NULLIFIED',
            self::STATUS_COMPLETE => 'COMPLETE'
        ];
    }

    public static function getStatusColor(){
	    return [self::STATUS_DRAFT => 'danger', self::STATUS_REGISTERED => 'primary', self::STATUS_NULLIFIED => 'warning', self::STATUS_COMPLETE => 'success'];
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
        $fromDb = self::getProgramFieldsFromDb($program_id, true);
        if($fromDb !== null){
            return $fromDb;
        }

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
            $array = ['project_name', 'program_sub' ,'participant_cat_group', 'group_member', 'group_code'];
            break;

            case 6: //jfed
            $array = ['project_name', 'group_member', 'group_code'];
            break;

            case 7: //IISEIC
            $array =  ['participant_mode', 'participant_cat_umk', 'participant_program', 'group_member','institution', 'contact_person', 'contact_no', 'contact_email', 'project_name', 'abstract_file', 'poster_file', 'payment_file'];
            break;


        }
        return $array;
    }

    public static function getPublicRequiredFields($program_id, $includePassword = true)
    {
        $fields = self::getProgramRequiredFields($program_id);

        $fields = array_values(array_diff($fields, ['abstract_file', 'payment_file', 'poster_file']));

        if(!in_array('contact_email', $fields)){
            $fields[] = 'contact_email';
        }

        if($includePassword){
            $fields[] = 'edit_password';
            $fields[] = 'edit_password_confirm';
        }

        return array_values(array_unique($fields));
    }

    public static function getProgramFields($program_id){
        $fromDb = self::getProgramFieldsFromDb($program_id, false);
        if($fromDb !== null){
            return $fromDb;
        }

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
            $array = ['project_name', 'program_sub', 'participant_cat_group', 'group_member', 'mentor_main', 'mentor_co', 'group_code', 'group_name'];
            break;

            case 6: //jfed
            $array = ['project_name', 'group_member', 'mentor_main', 'mentor_co', 'group_code', 'group_name'];
            break;

            case 7: //IISEIC
            $array = ['participant_mode', 'participant_cat_program', 'competition_cat_program', 'group_member', 'institution', 'contact_person', 'contact_no', 'contact_email', 'project_name', 'abstract_file', 'poster_file', 'video_link', 'payment_file'];
            break;
        }
        return $array;
    }

    private static function getProgramFieldsFromDb($programId, $requiredOnly)
    {
        if(!class_exists(ProgramRegField::class)){
            return null;
        }

        $hasConfig = ProgramRegField::find()
            ->where(['program_id' => (int)$programId])
            ->exists();

        if(!$hasConfig){
            return null;
        }

        $query = ProgramRegField::find()
            ->where(['program_id' => (int)$programId, 'is_enabled' => 1]);

        if($requiredOnly){
            $query->andWhere(['is_required' => 1]);
        }

        $rows = $query
            ->orderBy(['sort_order' => SORT_ASC, 'field_name' => SORT_ASC])
            ->all();

        if(!$rows){
            return [];
        }

        return ArrayHelper::getColumn($rows, 'field_name');
    }

    public static function availableRegistrationFields()
    {
        $tmp = new self();
        $labels = $tmp->attributeLabels();

        $fields = [
            'project_name',
            'project_desc',
            'participant_cat_local',
            'participant_cat_group',
            'participant_mode',
            'participant_cat_umk',
            'participant_program',
            'other_program',
            'program_sub',
            'advisor_dropdown',
            'booth_number',
            'advisor',
            'institution',
            'contact_person',
            'contact_no',
            'contact_email',
            'group_member',
            'group_code',
            'group_name',
            'mentor_main',
            'mentor_co',
            'poster_file',
            'abstract_file',
            'video_link',
            'payment_file',
            'nric',
            'competition_type',
            'participant_cat_program',
            'competition_cat_program',
        ];

        $out = [];
        foreach($fields as $f){
            $out[$f] = array_key_exists($f, $labels) ? $labels[$f] : $f;
        }
        return $out;
    }

    public static function availableRegistrationFieldTypes()
    {
        return [
            'project_name' => 'Text',
            'project_desc' => 'Textarea',
            'participant_cat_local' => 'Radio',
            'participant_cat_group' => 'Radio',
            'participant_mode' => 'Radio',
            'participant_cat_umk' => 'Radio',
            'participant_program' => 'Radio',
            'other_program' => 'Text',
            'program_sub' => 'Select',
            'advisor_dropdown' => 'Select',
            'booth_number' => 'Select',
            'advisor' => 'Text',
            'institution' => 'Text',
            'contact_person' => 'Text',
            'contact_no' => 'Text',
            'contact_email' => 'Email',
            'group_member' => 'Member List',
            'group_code' => 'Text',
            'group_name' => 'Text',
            'mentor_main' => 'Select',
            'mentor_co' => 'Select',
            'poster_file' => 'File Upload',
            'abstract_file' => 'File Upload',
            'video_link' => 'URL',
            'payment_file' => 'File Upload',
            'nric' => 'Text',
            'competition_type' => 'Radio',
            'participant_cat_program' => 'Select',
            'competition_cat_program' => 'Select',
        ];
    }

    public static function groupMemberShowMatric($programId)
    {
        if(!class_exists(ProgramRegField::class)){
            return true;
        }

        $row = ProgramRegField::find()
            ->where(['program_id' => (int)$programId, 'field_name' => 'group_member'])
            ->one();

        if(!$row){
            return true;
        }

        return (int)$row->show_matric === 1;
    }

    public static function getProgramFieldLayouts($programId)
    {
        if(!class_exists(ProgramRegField::class)){
            return [];
        }

        $rows = ProgramRegField::find()
            ->where(['program_id' => (int)$programId, 'is_enabled' => 1])
            ->all();

        if(!$rows){
            return [];
        }

        $layouts = [];
        foreach($rows as $row){
            $width = (int)$row->layout_width;
            $layouts[$row->field_name] = $width === ProgramRegField::LAYOUT_HALF ? ProgramRegField::LAYOUT_HALF : ProgramRegField::LAYOUT_FULL;
        }

        return $layouts;
    }

    public static function getFieldColumnClass($programId, $fieldName, $default = 'col-12')
    {
        $layouts = self::getProgramFieldLayouts($programId);

        if(!array_key_exists($fieldName, $layouts)){
            return $default;
        }

        return (int)$layouts[$fieldName] === ProgramRegField::LAYOUT_HALF ? 'col-md-6' : 'col-12';
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
            $array = ['project_name', 'program_sub', 'group_code', 'group_name'];
            break;

            case 6: //jfed
            $array = ['project_name', 'group_code', 'group_name'];
            break;
        }
        return $array;
    }

    public function getParticipantText(){
        $kira = count($this->members);
        if($this->user){
            $html = $this->user->fullname;
        }else if(!empty($this->contact_person)){
            $html = $this->contact_person;
        }else if(!empty($this->contact_email)){
            $html = $this->contact_email;
        }else{
            $html = 'Participant';
        }
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

    public function getParticipantCatProgramModel()
    {
        return $this->hasOne(ParticipantCatProgram::class, ['id' => 'participant_cat_program']);
    }

    public function getCompetitionCatProgramModel()
    {
        return $this->hasOne(CompetitionCatProgram::class, ['id' => 'competition_cat_program']);
    }

    public static function listCompetitionCatProgram($programId)
    {
        $rows = CompetitionCatProgram::find()
            ->where(['program_id' => $programId, 'is_active' => 1])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
            ->all();

        return ArrayHelper::map($rows, 'id', 'cat_name');
    }

    public static function listParticipantCatProgram($programId, $mode = null)
    {
        $query = ParticipantCatProgram::find()
            ->where(['program_id' => $programId, 'is_active' => 1]);

        if($mode !== null && $mode !== ''){
            $query->andWhere(['mode' => (int)$mode]);
        }

        $rows = $query
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
            ->all();

        return ArrayHelper::map($rows, 'id', 'cat_name');
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
            10 => 'BRONZE'
        ];
    }

    public static function listAwardColor(){
        return [
            80 => '#DA9100',
            60 => '#7A7A7A',
            10 => '#A87143'
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

    public function awardTextColor(){
        $text = '';
        $array = $this->listAward();
        if(array_key_exists($this->award, $array)){
            $text = $array[$this->award];
        }
        $color = '';
        $array = $this->listAwardColor();
        if(array_key_exists($this->award, $array)){
            $color = $array[$this->award];
        }
        return '<span style="color:'.$color.'">' . $text . ' AWARD</span>';
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
        $sub = '';
        if($this->programSub){
            $sub = ' / ' . $this->programSub->sub_abbr;
        }
        return $this->program->program_abbr . $sub;
    }

    public function getProgramNameLong(){
        $sub = '';
        if($this->programSub){
            $sub = '<br />(' . $this->programSub->sub_name . ')';
        }
        return $this->program->program_name . $sub;
    }

    public function getProgramSub()
    {
        return $this->hasOne(ProgramSub::class, ['id' => 'program_sub']);
    }

    public function getMentorMain()
    {
        return Mentor::findOne(['program_reg_id' => $this->id, 'is_main' => 1]);
    }

    public function getMentorCo()
    {
        return Mentor::findOne(['program_reg_id' => $this->id, 'is_main' => 0]);
    }

    public function getMentors()
    {
        return $this->hasMany(Mentor::class, ['program_reg_id' => 'id'])->orderBy('is_main DESC');
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

    public function getMemberStr(){
        $str = '';
        $strBr = '';
        $members = $this->members;
        $mentors = $this->mentors;

        $i = 0;
        if($members){
            foreach($members as $m){
            $comma = $i == 0 ? '' : ', ';
            $br = $i == 0 ? '' : '<br />';
            $str .= $comma. trim($m->member_name);
            $strBr .= $br. trim($m->member_name);
            $i++;
            }
        }
        
        if($mentors){
            foreach($mentors as $m){
            $comma = $i == 0 ? '' : ', ';
            $br = $i == 0 ? '' : '<br />';
            if($m->user){
                $str .= $comma. $m->user->fullname;
                $strBr .= $br. $m->user->fullname;
                $i++;
            }
            
            }
        }
        if($i <= 4){
            return $strBr;
        }else{
            return $str;
        }
        
    }

    public function getMemberCountAll(){
        $members = $this->members;
        $mentors = $this->mentors;
        $i = 0;
        if($members){
            foreach($members as $m){
            $i++;
            }
        }
        
        if($mentors){
            foreach($mentors as $m){
            if($m->user){
                $i++;
            }
            }
        }
        return $i;
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
        $baseName = '';
        if(!Yii::$app->user->isGuest && Yii::$app->user->identity && Yii::$app->user->identity->matric){
            $baseName = Yii::$app->user->identity->matric;
        }else if($this->contact_email){
            $baseName = preg_replace('/[^a-zA-Z0-9]/', '_', $this->normalizeEmail($this->contact_email));
        }else{
            $baseName = 'public_reg';
        }
        $name = $baseName . '_' . time();
        $path =  $this->program_id . '/'.$type;
        $instance = UploadedFile::getInstance($this, $inst_property);
        if($instance){
            $this->$inst_property = $instance;
            
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
