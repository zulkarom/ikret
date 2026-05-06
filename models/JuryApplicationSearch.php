<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class JuryApplicationSearch extends JuryApplication
{
    public $fullname;
    public $email;
    public $category;
    public $program_abbr;
    public $program_sub_abbr;
    public $program_sub_filter;

    public function rules()
    {
        return [
            [['program_id', 'program_sub_id', 'judging_session_id', 'status', 'created_at'], 'integer'],
            [['fullname', 'email', 'category', 'program_abbr', 'program_sub_abbr', 'program_sub_filter'], 'safe'],
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
            ->innerJoin('user u', 'u.id = p.user_id')
            ->leftJoin(Program::tableName() . ' pr', 'pr.id = a.program_id')
            ->leftJoin(ProgramSub::tableName() . ' ps', 'ps.id = a.program_sub_id');

        $programTable = \Yii::$app->db->schema->getTableSchema(Program::tableName());
        if($programTable && $programTable->getColumn('is_active')){
            $query->andWhere(['pr.is_active' => 1]);
        }

        $programSubTable = \Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
        if($programSubTable && $programSubTable->getColumn('is_active')){
            $query->andWhere(['or', ['a.program_sub_id' => null], ['a.program_sub_id' => 0], ['ps.is_active' => 1]]);
        }

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
        $dataProvider->sort->attributes['category'] = [
            'asc' => ['p.category' => SORT_ASC],
            'desc' => ['p.category' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['program_abbr'] = [
            'asc' => ['pr.program_abbr' => SORT_ASC],
            'desc' => ['pr.program_abbr' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['program_sub_abbr'] = [
            'asc' => ['ps.sub_abbr' => SORT_ASC],
            'desc' => ['ps.sub_abbr' => SORT_DESC],
        ];

        $this->load($params);

        if(!$this->validate()){
            return $dataProvider;
        }

        $query->andFilterWhere(['a.program_id' => $this->program_id]);
        $query->andFilterWhere(['a.program_sub_id' => $this->program_sub_id]);
        $query->andFilterWhere(['a.judging_session_id' => $this->judging_session_id]);
        $query->andFilterWhere(['a.status' => $this->status]);
        $query->andFilterWhere(['a.created_at' => $this->created_at]);

        if($this->program_sub_filter){
            if(strpos($this->program_sub_filter, 's:') === 0){
                $query->andWhere(['a.program_sub_id' => (int)substr($this->program_sub_filter, 2)]);
            }elseif(strpos($this->program_sub_filter, 'p:') === 0){
                $query->andWhere(['a.program_id' => (int)substr($this->program_sub_filter, 2)])
                    ->andWhere(['or', ['a.program_sub_id' => null], ['a.program_sub_id' => 0]]);
            }
        }

        if($this->fullname){
            $query->andWhere(['like', 'p.fullname', $this->fullname]);
        }
        if($this->email){
            $query->andWhere(['like', 'u.email', $this->email]);
        }
        if($this->category){
            $query->andWhere(['like', 'p.category', $this->category]);
        }
        if($this->program_abbr){
            $query->andWhere(['like', 'pr.program_abbr', $this->program_abbr]);
        }
        if($this->program_sub_abbr){
            $query->andWhere(['like', 'ps.sub_abbr', $this->program_sub_abbr]);
        }

        return $dataProvider;
    }
}
