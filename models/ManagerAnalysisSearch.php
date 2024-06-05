<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProgramRegistration;
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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['program_id', 'program_sub', 'stage', 'rubric'], 'integer'],
            [['fullnameSearch','dateSearch'], 'string'],
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
        ->select('a.user_id, a.program_id , AVG(j.score) as  purata')
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

        return $dataProvider;
    }
}
