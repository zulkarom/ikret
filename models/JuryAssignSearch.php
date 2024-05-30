<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProgramRegistrationSearch represents the model behind the search form of `app\models\ProgramRegistration`.
 */
class JuryAssignSearch extends User
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
        $query = JuryAssign::find()->alias('a')
        ->innerJoin('user u','u.id = a.user_id')
        ->where(['a.user_id' => Yii::$app->user->identity->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
