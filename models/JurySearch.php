<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProgramRegistrationSearch represents the model behind the search form of `app\models\ProgramRegistration`.
 */
class JurySearch extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_internal'], 'integer'],
            [['fullname','email'], 'string'],
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
        $query = User::find()->alias('a')
        ->select('a.*, COUNT(s.id) as kira')
        ->innerJoin('user_role r','r.user_id = a.id')
        ->leftJoin('program_reg_jury s', 's.user_id = a.id')
        ->where(['r.role_name' => 'jury'])
        ->orderBy('kira DESC, r.approve_at DESC')
        ->groupBy('a.id');
        

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
		'pagination' => [
                'pageSize' => 100,
            ],
            //'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        /* $query->andFilterWhere([
            'is_internal' => $this->is_internal,
        ]); */

        $query->andFilterWhere(['like', 'fullname', $this->fullname]);
        
        $query->andFilterWhere(['or', 
            ['like', 'email', $this->email],
            ['like', 'phone', $this->email]
        ]);

        return $dataProvider;
    }
}
