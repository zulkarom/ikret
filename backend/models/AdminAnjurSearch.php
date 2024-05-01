<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AdminAnjur;

/**
 * AdminAnjurSearch represents the model behind the search form of `backend\models\AdminAnjur`.
 */
class AdminAnjurSearch extends AdminAnjur
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'capacity', 'module_id'], 'integer'],
            [['module_siri', 'date_start', 'date_end', 'location'], 'safe'],
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
        $query = AdminAnjur::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'date_start' => $this->date_start,
            'date_end' => $this->date_end,
            'capacity' => $this->capacity,
            'module_id' => $this->module_id,
        ]);

        $query->andFilterWhere(['like', 'module_siri', $this->module_siri])
            ->andFilterWhere(['like', 'location', $this->location]);

        return $dataProvider;
    }
}
