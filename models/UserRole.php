<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user_role".
 *
 * @property int $id
 * @property int $user_id
 * @property string $role_name
 * @property int|null $program_id
 * @property int|null $committee_id 
* @property int|null $is_leader 
* @property int $is_deleted 
* 
* @property Committee $committee 
* @property User $user 
 */
class UserRole extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_role';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'role_name'], 'required'],

            [['user_id', 'program_id', 'committee_id', 'is_leader', 'is_deleted', 'status', 'program_sub'], 'integer'],

           [['role_name'], 'string', 'max' => 100],

           [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
           [['committee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Committee::class, 'targetAttribute' => ['committee_id' => 'id']],
    
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'role_name' => 'Role Name',
            'program_id' => 'Program',
            'committee_id' => 'Committee',
           'is_leader' => 'Committee Role',
           'is_deleted' => 'Is Deleted',
           'program_sub' => 'Competition'
        ];
    }

    public static function getStatusArray(){
        return [
            0 => 'REQUEST', 
            10 => 'AKTIF',
            20=> 'REMOVED'
        ];
    }

    public static function getStatusColor(){
	    return [0 => 'warning', 10 => 'primary', 20 => 'danger'];
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

    public static function listRoles(){
        return [
            'participant' => 'Participant',
            'manager' => 'Manager', // need to have program id
            'jury' => 'Jury',
            'committee' => 'Committee',
            'mentor' => 'Mentor',
            'admin' => 'Administrator'//
        ];
    }

    public static function listRolesRequest(){
        $user = Yii::$app->user->identity;
        if($user->is_student == 1){
            return [
                'participant' => 'Participant',
                'committee' => 'Committee',
            ];
        }else if($user->is_internal == 0){
            return [
                'participant' => 'Participant',
                'jury' => 'Jury',
                'mentor' => 'Mentor',
            ];
        }else{
            return [
                'participant' => 'Participant',
                'manager' => 'Manager', 
                'jury' => 'Jury',
                'committee' => 'Committee',
                'mentor' => 'Mentor',
            ];
        }
        
    }

    public static function listCommitteeRoles(){
        return [
            1 => 'Leader',
            2 => 'Member',
        ];
    }

    public function getRoleText(){
        $text = '';
        if(array_key_exists($this->role_name, $this->listRoles())){
            $text = $this->listRoles()[$this->role_name];
        }
        return $text;
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

    /**
    * Gets query for [[Committee]].
    *
    * @return \yii\db\ActiveQuery
    */
   public function getCommittee()
   {
       return $this->hasOne(Committee::class, ['id' => 'committee_id']);
   }

   public function getProgram()
   {
       return $this->hasOne(Program::class, ['id' => 'program_id']);
   }

   public function getProgramSub()
   {
       return $this->hasOne(ProgramSub::class, ['id' => 'program_sub']);
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

   public static function listJury(){
        $user = User::find()->alias('u')
        ->select('u.id, u.fullname')
        ->innerJoin('user_role r', 'r.user_id = u.id')
        ->where(['r.role_name' => 'jury'])->all();
        return ArrayHelper::map($user, 'id', 'fullname');
   }
}
