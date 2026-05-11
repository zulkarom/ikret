<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * ProgramRegistrationSearch represents the model behind the search form of `app\models\ProgramRegistration`.
 */
class JuryResultSearch extends User
{
    public $id;
    public $program_id;
    public $program_sub;
    public $rubric;
    public $fullnameSearch;
    public $jurySearch;
    public $jury_status;
    public $recommendationItem;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_internal', 'rubric', 'jury_status', 'recommendationItem'], 'integer'],
            [['fullnameSearch', 'jurySearch', 'email'], 'string'],
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
        ->leftJoin('user u','u.id = r.user_id')
        ->leftJoin('user ju','ju.id = a.user_id')
        ->where(['r.program_id' => $this->program_id, 'rubric_id' => $this->rubric]);
        if($this->program_sub){
            $query = $query->andWhere(
                ['r.program_sub' => $this->program_sub]);
        }
        $query = $query->orderBy([
            'a.status' => SORT_ASC,
            'a.score' => SORT_DESC,
            'a.reg_id' => SORT_ASC,
            'a.user_id' => SORT_ASC,
        ]);

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

        $query->andFilterWhere(['like', 'u.fullname', $this->fullnameSearch]);
        $query->andFilterWhere(['like', 'ju.fullname', $this->jurySearch]);

        if($this->jury_status !== null && $this->jury_status !== ''){
            $query->andWhere(['a.status' => (int)$this->jury_status]);
        }

        if($this->recommendationItem){
            $item = RubricItem::findOne((int)$this->recommendationItem);
            if($item && $item->colum_ans){
                $column = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$item->colum_ans);
                $query->leftJoin('rubric_answer ra', 'ra.assignment_id = a.id AND ra.rubric_id = a.rubric_id');
                if((int)$item->item_type === 2){
                    $query->andWhere(['ra.' . $column => 1]);
                }else if((int)$item->item_type === 1){
                    $query->andWhere(['>', 'ra.' . $column, 0]);
                }else{
                    $query->andWhere(['and',
                        ['not', ['ra.' . $column => null]],
                        ['<>', new Expression('TRIM(ra.' . $column . ')'), ''],
                    ]);
                }
            }
        }

        return $dataProvider;
    }
}
