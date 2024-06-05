<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "questionnaire".
 *
 * @property int $id
 * @property int $pre_post 1=pre 2 post
 * @property string $question_text
 * @property int $question_type
 */
class Questionnaire extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'questionnaire';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pre_post', 'question_text', 'question_type'], 'required'],
            [['pre_post', 'question_type'], 'integer'],
            [['question_text'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pre_post' => 'Pre Post',
            'question_text' => 'Question Text',
            'question_type' => 'Question Type',
        ];
    }

    public function getQuestionSubs(){
        return $this->hasMany(QuestionnaireSub::class, ['question_id' => 'id']);
    }

    
}
