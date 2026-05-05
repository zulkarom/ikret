<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class JuryProfileSearch extends JuryProfile
{
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['fullname', 'category', 'phone', 'institution', 'designation', 'address'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = JuryProfile::find()->alias('jp');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        $this->load($params);

        if(!$this->validate()){
            return $dataProvider;
        }

        $query->andFilterWhere([
            'jp.user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['like', 'jp.fullname', $this->fullname]);
        $query->andFilterWhere(['like', 'jp.category', $this->category]);
        $query->andFilterWhere(['or',
            ['like', 'jp.phone', $this->phone],
            ['like', 'jp.institution', $this->institution],
            ['like', 'jp.designation', $this->designation],
        ]);

        return $dataProvider;
    }
}
