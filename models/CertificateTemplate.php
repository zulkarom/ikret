<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cert_tmpl".
 *
 * @property int $id
 * @property string|null $template_name
 * @property float|null $name_mt
 * @property float|null $name_size
 * @property float|null $field1_mt
 * @property float|null $field1_size
 * @property float|null $field2_mt
 * @property float|null $field2_size
 * @property float|null $field3_mt
 * @property float|null $field3_size
 * @property float|null $field4_mt
 * @property float|null $field4_size
 * @property float|null $field5_mt
 * @property float|null $field5_size
 * @property float|null $margin_right
 * @property float|null $margin_left
 * @property int $set_type 1=preset,2=custom_html
 * @property string|null $custom_html
 * @property string|null $template_file
 * @property string|null $updated_at
 * @property int|null $published
 * @property int|null $is_portrait
 * @property string|null $published_at
 * @property string|null $publish_date
 * @property int $align 1=left,2=right,3=center
 */
class CertificateTemplate extends \yii\db\ActiveRecord
{
    public $template_upload;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cert_tmpl';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_mt', 'name_size', 'field1_mt', 'field1_size', 'field2_mt', 'field2_size', 'field3_mt', 'field3_size', 'field4_mt', 'field4_size', 'field5_mt', 'field5_size', 'margin_right', 'margin_left'], 'number'],
            [['set_type', 'published', 'is_portrait', 'align'], 'integer'],
            [['custom_html', 'template_file'], 'string'],
            [['updated_at', 'published_at', 'publish_date'], 'safe'],
            [['template_name'], 'string', 'max' => 255],
            [['template_upload'], 'file', 'skipOnEmpty' => true, 'extensions' => ['jpg', 'jpeg', 'png'], 'maxSize' => 1024 * 1024 * 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template_name' => 'Template Name',
            'name_mt' => 'Name Mt',
            'name_size' => 'Name Size',
            'field1_mt' => 'Field1 Mt',
            'field1_size' => 'Field1 Size',
            'field2_mt' => 'Field2 Mt',
            'field2_size' => 'Field2 Size',
            'field3_mt' => 'Field3 Mt',
            'field3_size' => 'Field3 Size',
            'field4_mt' => 'Field4 Mt',
            'field4_size' => 'Field4 Size',
            'field5_mt' => 'Field5 Mt',
            'field5_size' => 'Field5 Size',
            'margin_right' => 'Margin Right',
            'margin_left' => 'Margin Left',
            'set_type' => 'Set Type',
            'custom_html' => 'Custom Html',
            'template_file' => 'Template File',
            'updated_at' => 'Updated At',
            'published' => 'Published',
            'is_portrait' => 'Is Portrait',
            'published_at' => 'Published At',
            'publish_date' => 'Publish Date',
            'align' => 'Align',
            'template_upload' => 'Upload Template',
        ];
    }

    public function getOrientationLabel()
    {
        return (int)$this->is_portrait === 1 ? 'Portrait' : 'Landscape';
    }

    public function getAlignLabel()
    {
        $list = static::alignOptions();
        return isset($list[(int)$this->align]) ? $list[(int)$this->align] : 'Center';
    }

    public static function alignOptions()
    {
        return [
            1 => 'Left',
            2 => 'Right',
            3 => 'Center',
        ];
    }

    public static function orientationOptions()
    {
        return [
            0 => 'Landscape',
            1 => 'Portrait',
        ];
    }

    public static function isPublished($id)
    {
        $model = static::findOne((int)$id);
        return $model && (int)$model->published === 1;
    }

    public static function displayName($id, $fallback = 'Certificate')
    {
        $model = static::findOne((int)$id);
        if($model && trim((string)$model->template_name) !== ''){
            return ucwords((string)$model->template_name);
        }

        return $fallback;
    }

    public function textLeft($default)
    {
        return $this->margin_left === null || $this->margin_left === '' || (float)$this->margin_left <= 0 ? $default : (float)$this->margin_left;
    }

    public function textRight($default)
    {
        return $this->margin_right === null || $this->margin_right === '' || (float)$this->margin_right <= 0 ? $default : (float)$this->margin_right;
    }

    public function textTop($attribute, $default)
    {
        if ($this->$attribute === null || $this->$attribute === '') {
            return $default;
        }

        $value = (float)$this->$attribute;
        return $value > 500 ? $default : $value;
    }

    public function textSize($attribute, $default)
    {
        return $this->$attribute === null || $this->$attribute === '' ? $default : (float)$this->$attribute;
    }
}
