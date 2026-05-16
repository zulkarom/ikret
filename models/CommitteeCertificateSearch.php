<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class CommitteeCertificateSearch extends UserRole
{
    public $fullname;
    public $committee_id;
    public $committee_role;

    public function rules()
    {
        return [
            [['fullname', 'committee_role'], 'string'],
            [['committee_id'], 'integer'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = UserRole::find()->alias('a')
            ->joinWith(['user u', 'committee c'])
            ->where([
                'a.role_name' => 'committee',
                'a.status' => 10,
            ])
            ->orderBy([
                '(CASE WHEN c.committee_order IS NULL OR c.committee_order = 0 THEN 1 ELSE 0 END)' => SORT_ASC,
                'c.committee_order' => SORT_ASC,
                'c.id' => SORT_ASC,
                'u.fullname' => SORT_ASC,
                'a.id' => SORT_ASC,
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => false,
        ]);

        $this->load($params);

        if(!$this->validate()){
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'u.fullname', $this->fullname]);
        $query->andFilterWhere(['a.committee_id' => $this->committee_id]);

        if($this->committee_role !== '' && $this->committee_role !== null){
            if($this->committee_role === 'leader'){
                $query->andWhere(['c.is_jawatankuasa' => 1, 'a.is_leader' => 1]);
            }else if($this->committee_role === 'member'){
                $query->andWhere(['c.is_jawatankuasa' => 1, 'a.is_leader' => 0]);
            }else if($this->committee_role === 'none'){
                $query->andWhere(['<>', 'c.is_jawatankuasa', 1]);
            }
        }

        return $dataProvider;
    }
}
