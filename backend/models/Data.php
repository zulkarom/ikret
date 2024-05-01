<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "data".
 *
 * @property int $id
 * @property string|null $fullname
 * @property string|null $nric
 * @property string|null $phone
 * @property string|null $city
 * @property string|null $address
 * @property string|null $postcode
 * @property string|null $state
 * @property string|null $biz_info
 * @property string|null $note
 */
class Data extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['fullname'], 'string', 'max' => 36],
            [['nric', 'phone'], 'string', 'max' => 12],
            [['city'], 'string', 'max' => 15],
            [['address', 'biz_info', 'note'], 'string', 'max' => 47],
            [['postcode'], 'string', 'max' => 8],
            [['state'], 'string', 'max' => 5],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fullname' => 'Fullname',
            'nric' => 'Nric',
            'phone' => 'Phone',
            'city' => 'City',
            'address' => 'Address',
            'postcode' => 'Postcode',
            'state' => 'State',
            'biz_info' => 'Biz Info',
            'note' => 'Note',
        ];
    }
}
