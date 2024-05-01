<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SocialImpact;

/**
 * SocialImpactSearch represents the model behind the search form of `backend\models\SocialImpact`.
 */
class SocialImpactSearch extends SocialImpact
{
    public $limit;
    public $others;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'entrepreneur_id'], 'integer'],
            [['description'], 'safe'],
            [['other','others'], 'string'],
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
        if($this->limit > 0){
           $query = SocialImpact::find()
           ->alias('e')
           ->joinWith(['category c'])
           ->limit($this->limit); 
           $pagination = false;
        }
        else{
            $query = SocialImpact::find()->alias('e')->joinWith(['category c']);
            $pagination = ['pageSize' => 50];
            
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,

        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions

        $query->andFilterWhere(['like', 'c.category_name', $this->others]);
        $query->orFilterWhere(['like', 'e.other', $this->others]);

        return $dataProvider;
    }
}
