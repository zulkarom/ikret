<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Session;

/**
 * SessionSearch represents the model behind the search form of `app\models\Session`.
 */
class SessionSearch extends Session
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'program_id', 'program_sub', 'allow_scan_outside_duration', 'allow_scan_1_hour_after_event'], 'integer'],
            [['session_name', 'datetime_start', 'datetime_end', 'token'], 'safe'],
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
        $query = Session::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => [
                'defaultOrder' => [
                    'datetime_start' => SORT_ASC,
                    'id' => SORT_ASC,
                ],
            ],
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
            'program_id' => $this->program_id,
            'program_sub' => $this->program_sub,
            'allow_scan_outside_duration' => $this->allow_scan_outside_duration,
            'allow_scan_1_hour_after_event' => $this->allow_scan_1_hour_after_event,
            'datetime_start' => $this->datetime_start,
            'datetime_end' => $this->datetime_end,
        ]);

        $query->andFilterWhere(['like', 'session_name', $this->session_name]);
        $query->andFilterWhere(['like', 'token', $this->token]);

        return $dataProvider;
    }
}
