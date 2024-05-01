<?php

namespace backend\models;

use Yii;
use backend\models\Module;
/**
 * This is the model class for table "module_kategori".
 *
 * @property int $id
 * @property string $kategori_name
 * @property string $created_at
 * @property string $updated_at
 */
class ModuleKategori extends \yii\db\ActiveRecord
{
    public $kategori;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'module_kategori';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kategori_name'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['kategori_name'], 'string', 'max' => 250],
            [['description'], 'string'],
            [['kategori'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kategori_name' => 'Category Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getModules()
    {
        return $this->hasMany(Module::className(), ['kategori_id' => 'id']);
    }
}
