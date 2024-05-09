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
            [['is_jawatankuasa'], 'integer'],
            [['com_name'], 'string', 'max' => 54],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'com_name' => 'Com Name',
            'is_jawatankuasa' => 'Is Jawatankuasa',
        ];
    }

    /**
     * Gets query for [[UserRoles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserRoles()
    {
        return $this->hasMany(UserRole::class, ['committee_id' => 'id']);
    }

    public static function listCommittees(){
        return ArrayHelper::map(self::find()->all(),'id', 'com_name');
    }
}
