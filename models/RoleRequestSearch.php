<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SessionAttendanceSearch represents the model behind the search form of `app\models\SessionAttendance`.
 */
class RoleRequestSearch extends UserRole
{
    public $fullname;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_name', 'fullname'], 'string'],
            [['status'], 'integer'],
            [['request_at'], 'safe'],
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
        $query = UserRole::find()->alias('a')
        ->joinWith(['user u'])
       // ->where(['NOT IN', 'role_name', ['participant', 'jury', 'mentor']])
        ->orderBy('a.id DESC');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
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
            'a.role_name' => $this->role_name,
            'a.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'u.fullname', $this->fullname]);
        $query->andFilterWhere(['like', 'a.request_at', $this->request_at]);


        return $dataProvider;
    }
}
