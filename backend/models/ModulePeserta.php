<?php

namespace backend\models;

use Yii;
use common\models\User;
use common\models\Common;
/**
 * This is the model class for table "module_peserta".
 *
 * @property int $id
 * @property int $user_id
 * @property int $module_id
 * @property int $status
 * @property string $submitted_at
 * @property string $paid_at
 * @property int $is_paid
 * @property int $payment_method
 */
class ModulePeserta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'module_peserta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'anjur_id', 'status', 'submitted_at'], 'required', 'on' => 'daftar'],
            [['user_id', 'anjur_id', 'status', 'is_paid', 'payment_method', 'user_type'], 'integer'],
            [['submitted_at', 'paid_at'], 'safe'],
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
            'anjur_id' => 'Module ID',
            'status' => 'Status',
            'submitted_at' => 'Submitted At',
            'paid_at' => 'Paid At',
            'is_paid' => 'Is Paid',
            'payment_method' => 'Payment Method',
        ];
    }

    public function getStatusText(){
        if($this->status > 0){
            return Common::statusModule()[$this->status];
        }else{
            return '';
        }
    }

    public function getAdminAnjur()
    {
        return $this->hasOne(AdminAnjur::className(), ['id' => 'anjur_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getCountPeserta($id){
        return self::find()
        ->where(['anjur_id' => $id])
        ->count();
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
