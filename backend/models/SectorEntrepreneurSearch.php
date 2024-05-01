<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SectorEntrepreneurSearch represents the model behind the search form of `backend\models\SectorEntrepreneur`.
 */
class SectorEntrepreneurSearch extends SectorEntrepreneur
{
    public $limit;
    public $sector_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'entrepreneur_id', 'sector_id'], 'integer'],
            [['description'], 'safe'],
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
           $query = SectorEntrepreneur::find()
           ->alias('e')
           ->joinWith(['sector s'])
           ->limit($this->limit); 
           $pagination = false;
        }
        else{
            $query = SectorEntrepreneur::find()
            ->alias('e')
            ->joinWith(['sector s']);;
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
        

        $query->andFilterWhere(['s.id' => $this->sector_id]);

        return $dataProvider;
    }
}
