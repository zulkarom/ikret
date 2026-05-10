<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "setting".
 *
 * @property int $id
 * @property string|null $allow_cert_from
 * @property string|null $grand_name
 */
class Setting extends \yii\db\ActiveRecord
{
    public $banner_file;
    public $programme_book_qr_file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'setting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $columns = static::getTableSchema()->getColumnNames();
        return [
            [$columns, 'safe'],
            [['banner_file', 'programme_book_qr_file'], 'file', 'skipOnEmpty' => true, 'extensions' => ['png', 'jpg', 'jpeg', 'webp']],
            [['show_icreate_list_event'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'allow_cert_from' => 'Certificate Release Date',
            'allow_edit_reg_until' => 'Allow Edit Registration Until',
            'date_start' => 'Event Start Date',
            'date_end' => 'Event End Date',
            'banner_image' => 'Banner Image',
            'banner_file' => 'Banner Image',
            'show_icreate_list_event' => 'Show I-CREATE List Event',
            'grand_name' => 'Grand Name',
            'programme_book_url' => 'Programme Book URL',
            'programme_book_qr' => 'Programme Book QR',
            'programme_book_qr_file' => 'Programme Book QR',
            'program_description' => 'Program Description',
        ];
    }

    public static function certificateReleaseDate()
    {
        $setting = static::findOne(1);

        return $setting ? $setting->allow_cert_from : null;
    }

    public static function areCertificatesReleased($date = null)
    {
        if ($date === null) {
            $date = static::certificateReleaseDate();
        }

        if (empty($date)) {
            return true;
        }

        $releaseTime = strtotime($date . ' 00:00:00');

        return $releaseTime === false || time() >= $releaseTime;
    }

    public static function certificateReleaseText($date = null)
    {
        if ($date === null) {
            $date = static::certificateReleaseDate();
        }

        if (empty($date)) {
            return null;
        }

        $releaseTime = strtotime($date);

        return $releaseTime === false ? $date : date('d M Y', $releaseTime);
    }
}
