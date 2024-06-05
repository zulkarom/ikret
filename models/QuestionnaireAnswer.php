<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "questionnaire_ans".
 *
 * @property int $id
 * @property int $user_id
 * @property int $pre_post
 * @property int|null $q1
 * @property int|null $q2
 * @property int|null $q3
 * @property int|null $q4
 * @property int|null $q5
 * @property string|null $q6
 * @property string|null $q7
 *
 * @property User $user
 */
class QuestionnaireAnswer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'questionnaire_ans';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'q1', 'q2', 'q3'], 'required'],

            [['user_id', 'q1', 'q2', 'q3', 'q4', 'q5', 'sub1', 'sub1', 'sub2', 'sub3', 'sub4', 'sub5', 'sub6', 'sub7', 'sub8', 'sub9', 'sub10'], 'integer'],

            [['q7', 'q8', 'q9'], 'string', 'min' => 20],

            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
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
            'pre_post' => 'Pre Post',
            'q1' => 'Likert Question',
            'q2' => 'Likert Question',
            'q3' => 'Likert Question',
            'q4' => 'Likert Question',
            'q5' => 'Likert Question',
            'q6' => 'Likert Question',
            'q7' => 'Answer for this open-ended question',
            'q8' => 'Answer for this open-ended question',
            'q9' => 'Answer for this open-ended question',
        ];
    }

    

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
