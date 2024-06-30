<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProgramRegistration;

/**
 * ProgramRegistrationSearch represents the model behind the search form of `app\models\ProgramRegistration`.
 */
class ManagerSessionSearch extends ProgramRegistration
{
    public $fullnameSearch;
    public $dateSearch;
   // public $programx_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['program_id', 'program_sub'], 'integer'],
            [['fullnameSearch','dateSearch', 'group_name', 'group_code', 'booth_number'], 'string'],
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
        ->joinWith(['user u'])
        ->where(['>', 'a.status', 0])
        ->andWhere(['a.program_id' => $this->program_id,]);

        if($this->program_sub){
            $query = $query->andWhere(['a.program_sub' => $this->program_sub]);
        }

        $query = $query->orderBy('a.flag DESC, a.submitted_at DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
		'pagination' => [
                'pageSize' => 20,
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
        ->andFilterWhere(['like', 'a.group_code', $this->group_code])
        ->andFilterWhere(['like', 'a.group_name', $this->group_name])
        ->andFilterWhere(['like', 'a.booth_number', $this->group_name])
        ;

        return $dataProvider;
    }
}
