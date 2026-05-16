<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "committee".
 *
 * @property int $id
 * @property string|null $com_name
 * @property int|null $is_jawatankuasa
 * @property int|null $is_student
 * @property string|null $com_name_en
 * @property int|null $committee_order
 * @property int $is_pengarah
 * @property int $can_approve
 * @property int $cert_only
 *
 * @property UserRole[] $userRoles
 */
class Committee extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'committee';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_jawatankuasa', 'is_student', 'committee_order', 'is_pengarah', 'can_approve', 'cert_only'], 'integer'],
            [['com_name', 'com_name_en'], 'string', 'max' => 200],
            [['is_student', 'is_pengarah', 'can_approve', 'cert_only'], 'default', 'value' => 0],
            [['committee_order'], 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'com_name' => 'Committee Name',
            'com_name_en' => 'Committee Name (EN)',
            'committee_order' => 'Committee Order',
            'is_jawatankuasa' => 'Jawatankuasa',
            'is_student' => 'Student Committee',
            'is_pengarah' => 'Director',
            'can_approve' => 'Can Approve',
            'cert_only' => 'Certificate Only',
        ];
    }

    public static function yesNoOptions()
    {
        return [
            0 => 'No',
            1 => 'Yes',
        ];
    }

    /**
     * Gets query for [[UserRoles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserRoles()
    {
        return $this->hasMany(UserRole::class, ['committee_id' => 'id'])
        ->orderBy('is_leader ASC');
    }

    public static function listCommittees($control = false){
        if($control){
            $user = Yii::$app->user->identity;
            if($user->is_student == 1){
                return ArrayHelper::map(self::find()
                ->where(['is_student' => 1])
                ->orderBy([
                    '(CASE WHEN committee_order IS NULL OR committee_order = 0 THEN 1 ELSE 0 END)' => SORT_ASC,
                    'committee_order' => SORT_ASC,
                    'id' => SORT_ASC,
                    'com_name_en' => SORT_ASC,
                    'com_name' => SORT_ASC,
                ])
                ->all(),'id', 'com_name_en');
            }else if($user->is_student == 0){
                return ArrayHelper::map(self::find()->orderBy([
                    '(CASE WHEN committee_order IS NULL OR committee_order = 0 THEN 1 ELSE 0 END)' => SORT_ASC,
                    'committee_order' => SORT_ASC,
                    'id' => SORT_ASC,
                    'com_name_en' => SORT_ASC,
                    'com_name' => SORT_ASC,
                ])->all(),'id', 'com_name_en');
            }else{
                return [''];
            }

            
        }else{
            return ArrayHelper::map(self::find()->orderBy([
                '(CASE WHEN committee_order IS NULL OR committee_order = 0 THEN 1 ELSE 0 END)' => SORT_ASC,
                'committee_order' => SORT_ASC,
                'id' => SORT_ASC,
                'com_name_en' => SORT_ASC,
                'com_name' => SORT_ASC,
            ])->all(),'id', 'com_name_en');
        }
        
    }
}
