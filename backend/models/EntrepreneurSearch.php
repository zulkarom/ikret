<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Entrepreneur;

/**
 * EntrepreneurSearch represents the model behind the search form of `backend\models\Entrepreneur`.
 */
class EntrepreneurSearch extends Entrepreneur
{
    public $fullname;
    public $nric;
    public $email;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fullname', 'email', 'biz_name', 'phone', 'nric'], 'string'],
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
        $query = Entrepreneur::find()
        ->joinWith(['user']);

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



        $query->andFilterWhere(['like', 'user.fullname', $this->fullname]);
        $query->andFilterWhere(['like', 'user.nric', $this->nric]);
        
        $dataProvider->sort->attributes['fullname'] = [
            'asc' => ['user.fullname' => SORT_ASC],
            'desc' => ['user.fullname' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['nric'] = [
            'asc' => ['user.nric' => SORT_ASC],
            'desc' => ['user.nric' => SORT_DESC],
        ];
        

        return $dataProvider;
    }
}
