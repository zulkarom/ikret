<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProgramRegistration;
use app\models\ParticipantAchieve;
use yii\db\Expression;

/**
 * ProgramRegistrationSearch represents the model behind the search form of `app\models\ProgramRegistration`.
 */
class ManagerAnalysisSearch extends ProgramRegistration
{
    public $fullnameSearch;
    public $dateSearch;
    public $stage = null;
    public $rubric;
    public $statFilter;
    public $awardFilter;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['program_id', 'program_sub', 'stage', 'rubric'], 'integer'],
            [['fullnameSearch','dateSearch', 'statFilter'], 'string'],
            [['awardFilter'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fullnameSearch' => 'Participant Name',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ProgramRegistration::find()->alias('a')
        ->select('a.*, MAX(j.rubric_id) AS rubric_id, AVG(j.score) as purata')
        ->joinWith(['user u'])
        ->leftJoin('program_reg_jury j','j.reg_id = a.id')
        ->where(['>', 'a.status', 0])
        ->andWhere(['a.program_id' => $this->program_id, 
        'j.rubric_id' => $this->rubric, 
        'j.status' => 20]); //complete jury


        if($this->stage){
            $query = $query->andWhere(['j.stage' => $this->stage]);
        }
        $query = $query->groupBy('a.id');

        if($this->program_sub){
            $query = $query->andWhere(['a.program_sub' => $this->program_sub]);
        }

        $query = $query->orderBy('a.flag DESC, a.submitted_at DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
		'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

      /*   // grid filtering conditions
        $query->andFilterWhere([
            'a.program_id' => $this->programx_id,
        ]); */

        $query->andFilterWhere(['like', 'a.submitted_at', $this->submitted_at])
        ->andFilterWhere(['like', 'u.fullname', $this->fullnameSearch]);

        if($this->statFilter === 'awarded'){
            $query->andHaving(['>=', new Expression('AVG(j.score)'), 10]);
        }else if($this->statFilter === 'achievements'){
            $query->andWhere([
                'exists',
                ParticipantAchieve::find()
                    ->where('program_reg_achieve.program_reg_id = a.id'),
            ]);
        }

        if($this->awardFilter){
            $scoreExpression = new Expression('AVG(j.score)');
            if((int)$this->awardFilter === 80){
                $query->andHaving(['>=', $scoreExpression, 80]);
            }else if((int)$this->awardFilter === 60){
                $query->andHaving(['and', ['>=', $scoreExpression, 60], ['<', $scoreExpression, 80]]);
            }else if((int)$this->awardFilter === 10){
                $query->andHaving(['and', ['>=', $scoreExpression, 10], ['<', $scoreExpression, 60]]);
            }
        }

        return $dataProvider;
    }
}
