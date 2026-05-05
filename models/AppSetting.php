<?php

namespace app\models;

use Yii;

class AppSetting extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'app_setting';
    }

    public function rules()
    {
        return [
            [['key'], 'required'],
            [['key'], 'string', 'max' => 100],
            [['value'], 'string', 'max' => 255],
            [['key'], 'unique'],
        ];
    }

    public static function getValue(string $key, $default = null)
    {
        $row = self::find()->where(['key' => $key])->one();
        if($row){
            return $row->value;
        }
        return $default;
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        $value = self::getValue($key, null);
        if($value === null){
            return $default;
        }
        return (string)$value === '1' || strtolower((string)$value) === 'true' || (string)$value === 'yes';
    }

    public static function setValue(string $key, ?string $value): bool
    {
        $row = self::find()->where(['key' => $key])->one();
        if(!$row){
            $row = new self();
            $row->key = $key;
        }
        $row->value = $value;
        if(!$row->save()){
            if(method_exists($row, 'flashError')){
                $row->flashError();
            } else {
                Yii::$app->session->addFlash('error', 'Failed to save setting.');
            }
            return false;
        }
        return true;
    }
}
