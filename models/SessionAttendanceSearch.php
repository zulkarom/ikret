<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SessionAttendance;

/**
 * SessionAttendanceSearch represents the model behind the search form of `app\models\SessionAttendance`.
 */
class SessionAttendanceSearch extends SessionAttendance
{
    public $fullname;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['session_id', 'fullname'], 'string'],
            [['scanned_at'], 'safe'],
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
        $query = SessionAttendance::find()
        ->joinWith(['user u'])
        ->orderBy('id DESC');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
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
            'session_id' => $this->session_id,
        ]);

        $query->andFilterWhere(['like', 'u.fullname', $this->fullname]);

        $query->andFilterWhere(['like', 'scanned_at', $this->scanned_at]);

        return $dataProvider;
    }
}
