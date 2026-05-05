<?php

namespace app\models;

use Yii;
use yii\base\Model;

class JuryApplyForm extends Model
{
    public $email;
    public $fullname;
    public $category;
    public $phone;
    public $institution;
    public $address;
    public $designation;

    public $requirement_ids;

    public $declaration_accepted;

    public function rules()
    {
        return [
            [['email', 'fullname', 'category', 'phone', 'institution', 'designation', 'requirement_ids'], 'required'],
            [['email'], 'email'],
            [['address'], 'string'],
            [['requirement_ids'], 'each', 'rule' => ['integer']],
            [['declaration_accepted'], 'boolean'],
            [['email', 'fullname', 'category', 'phone', 'institution', 'designation'], 'string', 'max' => 255],
            [['declaration_accepted'], 'compare', 'compareValue' => 1, 'message' => 'You must accept the declaration.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'requirement_ids' => 'Programs / Sessions',
            'declaration_accepted' => 'Declaration',
        ];
    }

    public function submit(): bool
    {
        if(!$this->validate()){
            return false;
        }

        $ids = is_array($this->requirement_ids) ? $this->requirement_ids : [];
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if(!$ids){
            $this->addError('requirement_ids', 'Please select at least one option.');
            return false;
        }

        $requirements = JuryRequirement::find()
            ->where(['id' => $ids, 'is_required' => 1, 'is_active' => 1])
            ->all();

        $reqMap = [];
        if($requirements){
            foreach($requirements as $r){
                $reqMap[(int)$r->id] = $r;
            }
        }

        foreach($ids as $rid){
            if(!isset($reqMap[$rid])){
                $this->addError('requirement_ids', 'One or more selected options are not open for application.');
                return false;
            }
        }

        $email = trim(strtolower($this->email));

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $user = User::find()->where(['username' => $email])->one();
            if(!$user){
                $user = new User();
                $user->scenario = 'create';
                $user->username = $email;
                $user->email = $email;
                $user->fullname = $this->fullname;
                $user->phone = $this->phone;
                $user->institution = $this->institution;
                $user->status = User::STATUS_ACTIVE;
                $user->is_internal = 0;
                $user->is_student = 0;
                $user->setPassword($email);
                $user->generateAuthKey();
                if(!$user->save()){
                    $user->flashError();
                    $transaction->rollBack();
                    return false;
                }
            }

            $role = UserRole::find()->where(['user_id' => $user->id, 'role_name' => 'jury', 'status' => 10])->one();
            if(!$role){
                $role = new UserRole();
                $role->user_id = $user->id;
                $role->role_name = 'jury';
                $role->status = 10;
                if(!$role->save()){
                    $role->flashError();
                    $transaction->rollBack();
                    return false;
                }
            }

            $profile = JuryProfile::find()->where(['user_id' => $user->id])->one();
            if(!$profile){
                $profile = new JuryProfile();
                $profile->user_id = $user->id;
                $profile->created_at = time();
            }

            $profile->fullname = $this->fullname;
            $profile->category = $this->category;
            $profile->phone = $this->phone;
            $profile->institution = $this->institution;
            $profile->address = $this->address;
            $profile->designation = $this->designation;
            $profile->updated_at = time();

            if(!$profile->save()){
                $profile->flashError();
                $transaction->rollBack();
                return false;
            }

            foreach($ids as $rid){
                $requirement = $reqMap[$rid];

                if($requirement->jury_limit !== null){
                    $currentCount = (int)JuryApplication::find()->where([
                        'program_id' => (int)$requirement->program_id,
                        'program_sub_id' => $requirement->program_sub_id ? (int)$requirement->program_sub_id : null,
                        'judging_session_id' => $requirement->judging_session_id ? (int)$requirement->judging_session_id : null,
                    ])->count();

                    if($currentCount >= (int)$requirement->jury_limit){
                        $this->addError('requirement_ids', 'One or more selected options has reached the jury limit.');
                        $transaction->rollBack();
                        return false;
                    }
                }

                $app = new JuryApplication();
                $app->jury_profile_id = $profile->id;
                $app->program_id = (int)$requirement->program_id;
                $app->program_sub_id = $requirement->program_sub_id ? (int)$requirement->program_sub_id : null;
                $app->judging_session_id = $requirement->judging_session_id ? (int)$requirement->judging_session_id : null;
                $app->declaration_accepted = 1;
                $app->status = 0;
                $app->created_at = time();

                if(!$app->save()){
                    $app->flashError();
                    $transaction->rollBack();
                    return false;
                }
            }

            $transaction->commit();

            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->addFlash('error', $e->getMessage());
            return false;
        }
    }
}
