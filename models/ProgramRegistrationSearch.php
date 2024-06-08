<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProgramRegistration;

/**
 * ProgramRegistrationSearch represents the model behind the search form of `app\models\ProgramRegistration`.
 */
class ProgramRegistrationSearch extends ProgramRegistration
{
    public $fullnameSearch;
    public $dateSearch;
    public $programx_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['programx_id'], 'integer'],
            [['fullnameSearch','dateSearch'], 'string'],
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
        ->where(['>', 'a.status', 0]);

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

        // grid filtering conditions
        $query->andFilterWhere([
            'a.program_id' => $this->programx_id,
        ]);

        $query->andFilterWhere(['like', 'a.submitted_at', $this->submitted_at])
        ->andFilterWhere(['like', 'u.fullname', $this->fullnameSearch]);

        return $dataProvider;
    }
}
