<?php

namespace backend\models;

use Yii;
use backend\models\ModuleAnjur;
/**
 * This is the model class for table "admin_anjur".
 *
 * @property int $id
 * @property string $module_siri
 * @property string $date_start
 * @property string $date_end
 * @property int $capacity
 * @property string $location
 * @property int $module_id
 */
class AdminAnjur extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'admin_anjur';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['module_siri', 'date_start', 'date_end', 'capacity', 'location', 'module_id'], 'required'],
            [['date_start', 'date_end'], 'safe'],
            [['capacity', 'module_id'], 'integer'],
            [['module_siri', 'location'], 'string', 'max' => 225],
            [['description'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'module_siri' => 'Module Siri',
            'date_start' => 'Date Start',
            'date_end' => 'Date End',
            'capacity' => 'Capacity',
            'location' => 'Location',
            'module_id' => 'Module Id',
        ];
    }

    public function getModule()
    {
        return $this->hasOne(Module::className(), ['id' => 'module_id']);
    }

    public function getCountPeserta($id){
        return ModulePeserta::find()
        ->where(['anjur_id' => $id])
        ->count();
    }
}
