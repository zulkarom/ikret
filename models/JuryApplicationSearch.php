<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class JuryApplicationSearch extends JuryApplication
{
    public $fullname;
    public $email;

    public function rules()
    {
        return [
            [['program_id', 'program_sub_id', 'judging_session_id', 'status'], 'integer'],
            [['fullname', 'email'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = JuryApplication::find()->alias('a')
            ->innerJoin('jury_profiles p', 'p.id = a.jury_profile_id')
            ->innerJoin('user u', 'u.id = p.user_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        $dataProvider->sort->attributes['fullname'] = [
            'asc' => ['p.fullname' => SORT_ASC],
            'desc' => ['p.fullname' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['email'] = [
            'asc' => ['u.email' => SORT_ASC],
            'desc' => ['u.email' => SORT_DESC],
        ];

        $this->load($params);

        if(!$this->validate()){
            return $dataProvider;
        }

        $query->andFilterWhere(['a.program_id' => $this->program_id]);
        $query->andFilterWhere(['a.program_sub_id' => $this->program_sub_id]);
        $query->andFilterWhere(['a.judging_session_id' => $this->judging_session_id]);
        $query->andFilterWhere(['a.status' => $this->status]);

        if($this->fullname){
            $query->andWhere(['like', 'p.fullname', $this->fullname]);
        }
        if($this->email){
            $query->andWhere(['like', 'u.email', $this->email]);
        }

        return $dataProvider;
    }
}
