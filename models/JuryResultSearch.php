<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProgramRegistrationSearch represents the model behind the search form of `app\models\ProgramRegistration`.
 */
class JuryResultSearch extends User
{
    public $id;
    public $program_id;
    public $program_sub;

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
        $query = JuryAssign::find()->alias('a')
        ->joinWith(['registration r'])
        ->where(['r.program_id' => $this->program_id]);
        if($this->program_sub){
            $query = $query->andWhere(['r.program_sub' => $this->program_sub]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
		'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        /* $query->andFilterWhere([
            'is_internal' => $this->is_internal,
        ]); */

        /* $query->andFilterWhere(['like', 'fullname', $this->fullname]);
        
        $query->andFilterWhere(['or', 
            ['like', 'email', $this->email],
            ['like', 'phone', $this->email]
        ]); */

        return $dataProvider;
    }
}
