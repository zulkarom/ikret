<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProgramRegistration;

/**
 * ProgramRegistrationSearch represents the model behind the search form of `app\models\ProgramRegistration`.
 */
class ProgramRegistrationManagerSearch extends ProgramRegistration
{
    public $fullnameSearch;
    public $dateSearch;
    public $jury_status;
    public $jurySearch;
    public $unassigned;
   // public $programx_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['program_id', 'program_sub', 'jury_status', 'unassigned'], 'integer'],
            [['fullnameSearch','dateSearch', 'group_name', 'jurySearch'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fullnameSearch' => 'Participant Name',
            'jury_status' => 'Jury Status',
            'jurySearch' => 'Jury Name',
            'unassigned' => 'Unassigned',
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
        ->joinWith(['user u'])
        ->where(['>', 'a.status', 0])
        ->andWhere(['a.program_id' => $this->program_id,]);

        if($this->program_sub){
            $query = $query->andWhere(['a.program_sub' => $this->program_sub]);
        }

        $query = $query->orderBy(['a.group_name' => SORT_ASC, 'a.id' => SORT_ASC]);

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
        ->andFilterWhere(['like', 'u.fullname', $this->fullnameSearch])
        ->andFilterWhere(['like', 'a.group_name', $this->group_name])
        ;

        if((int)$this->unassigned === 1){
            $query->joinWith(['juries j'], false, 'LEFT JOIN')
                ->andWhere(['j.id' => null])
                ->distinct();
        }else if(($this->jury_status !== null && $this->jury_status !== '') || trim((string)$this->jurySearch) !== ''){
            $query->innerJoinWith(['juries j'], false)
                ->leftJoin(['ju' => User::tableName()], 'ju.id = j.user_id');

            if($this->jury_status !== null && $this->jury_status !== ''){
                $query->andWhere(['j.status' => (int)$this->jury_status]);
            }

            $query->andFilterWhere(['like', 'ju.fullname', $this->jurySearch])
                ->distinct();
        }

        return $dataProvider;
    }
}
