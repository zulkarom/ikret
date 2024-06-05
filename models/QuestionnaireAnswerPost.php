<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "questionnaire_ans_post".
 *
 * @property int $id
 * @property int $user_id
 * @property int $pre_post
 * @property int|null $q1
 * @property int|null $q2
 * @property int|null $q3
 * @property int|null $q4
 * @property int|null $q5
 * @property int|null $q6
 * @property string|null $q7
 * @property string|null $q8
 * @property string|null $q9
 * @property string|null $submitted_at
 *
 * @property User $user
 */
class QuestionnaireAnswerPost extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'questionnaire_ans_post';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            [['user_id', 'q1', 'q2', 'q3'], 'required'],

            [['user_id', 'q1', 'q2', 'q3', 'q4', 'q5', 'q8', 'q9', 'q10','sub1', 'sub1', 'sub2', 'sub3', 'sub4', 'sub5', 'sub6', 'sub7', 'sub8', 'sub9', 'sub10'], 'integer'],

            [['q6', 'q7', 'q11'], 'string', 'min' => 20],
            [['submitted_at'], 'safe'],
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
            'q8' => 'Likert Question',
            'q9' => 'Likert Question',
            '910' => 'Likert Question',
            'q6' => 'Answer for this open-ended question',
            'q7' => 'Answer for this open-ended question',
            'q11' => 'Answer for this open-ended question',
            'submitted_at' => 'Submitted At',
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
