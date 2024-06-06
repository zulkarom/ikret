<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rubric_answer".
 *
 * @property int $id
 * @property int $rubric_id
 * @property int $assignment_id
 * @property int|null $item_no1
 * @property int|null $item_no2
 * @property int|null $item_no3
 * @property int|null $item_no4
 * @property int|null $item_no5
 * @property int|null $item_no6
 * @property int|null $item_no7
 * @property int|null $item_no8
 * @property int|null $item_no9
 * @property int|null $item_no10
 * @property int|null $item_no11
 * @property int|null $item_no12
 * @property int|null $item_no13
 * @property int|null $item_no14
 * @property int|null $item_no15
 * @property int|null $item_no16
 * @property int|null $item_no17
 * @property int|null $item_no18
 * @property int|null $item_no19
 * @property int|null $item_no20
 * @property int|null $item_no21
 * @property int|null $item_no22
 * @property int|null $item_no23
 * @property int|null $item_no24
 * @property int|null $item_no25
 * @property int|null $item_no26
 * @property int|null $item_no27
 * @property int|null $item_no28
 * @property int|null $item_no29
 * @property int|null $item_no30
 * @property string|null $item_text1
 * @property string|null $item_text2
 * @property string|null $item_text3
 * @property string|null $item_text4
 * @property string|null $item_text5
 * @property string|null $item_text6
 * @property string|null $item_text7
 * @property string|null $item_text8
 * @property string|null $item_text9
 * @property string|null $item_text10
 *
 * @property ProgramRegJury $assignment
 * @property Rubric $rubric
 */
class RubricAnswer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rubric_answer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rubric_id', 'assignment_id'], 'required'],
            [['rubric_id', 'assignment_id', 'item_no1', 'item_no2', 'item_no3', 'item_no4', 'item_no5', 'item_no6', 'item_no7', 'item_no8', 'item_no9', 'item_no10', 'item_no11', 'item_no12', 'item_no13', 'item_no14', 'item_no15', 'item_no16', 'item_no17', 'item_no18', 'item_no19', 'item_no20', 'item_no21', 'item_no22', 'item_no23', 'item_no24', 'item_no25', 'item_no26', 'item_no27', 'item_no28', 'item_no29', 'item_no30'], 'integer'],

            [['item_text1', 'item_text2', 'item_text3', 'item_text4', 'item_text5', 'item_text6', 'item_text7', 'item_text8', 'item_text9', 'item_text10'], 'string'],

            [['rubric_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rubric::class, 'targetAttribute' => ['rubric_id' => 'id']],
            [['assignment_id'], 'exist', 'skipOnError' => true, 'targetClass' => JuryAssign::class, 'targetAttribute' => ['assignment_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rubric_id' => 'Rubric ID',
            'assignment_id' => 'Assignment ID',
            'item_no1' => 'Item No1',
            'item_no2' => 'Item No2',
            'item_no3' => 'Item No3',
            'item_no4' => 'Item No4',
            'item_no5' => 'Item No5',
            'item_no6' => 'Item No6',
            'item_no7' => 'Item No7',
            'item_no8' => 'Item No8',
            'item_no9' => 'Item No9',
            'item_no10' => 'Item No10',
            'item_no11' => 'Item No11',
            'item_no12' => 'Item No12',
            'item_no13' => 'Item No13',
            'item_no14' => 'Item No14',
            'item_no15' => 'Item No15',
            'item_no16' => 'Item No16',
            'item_no17' => 'Item No17',
            'item_no18' => 'Item No18',
            'item_no19' => 'Item No19',
            'item_no20' => 'Item No20',
            'item_no21' => 'Item No21',
            'item_no22' => 'Item No22',
            'item_no23' => 'Item No23',
            'item_no24' => 'Item No24',
            'item_no25' => 'Item No25',
            'item_no26' => 'Item No26',
            'item_no27' => 'Item No27',
            'item_no28' => 'Item No28',
            'item_no29' => 'Item No29',
            'item_no30' => 'Item No30',
            'item_text1' => 'Item Text1',
            'item_text2' => 'Item Text2',
            'item_text3' => 'Item Text3',
            'item_text4' => 'Item Text4',
            'item_text5' => 'Item Text5',
            'item_text6' => 'Item Text6',
            'item_text7' => 'Item Text7',
            'item_text8' => 'Item Text8',
            'item_text9' => 'Item Text9',
            'item_text10' => 'Item Text10',
        ];
    }

    /**
     * Gets query for [[Assignment]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssignment()
    {
        return $this->hasOne(JuryAssign::class, ['id' => 'assignment_id']);
    }

    /**
     * Gets query for [[Rubric]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRubric()
    {
        return $this->hasOne(Rubric::class, ['id' => 'rubric_id']);
    }

    public function getTotalScorePercent(){
        $total = 0;
        $score = 0;
        $rubric = $this->rubric;
        $cat = $rubric->categoriesScore;
        if($cat){
            foreach($cat as $c){
                $items = $c->items;
                if($items){
                    foreach($items as $item){
                        $option = $item->option_number;
                        $colum = $item->colum_ans;
                        $val = $this->$colum;
                        $total += $option;

                        if($item->item_type == 1){ // kira yang likert shj
                            if($val > 0){
                                $score +=$val;
                            }
                        }
                        if($item->item_type == 2){ //yesno
                            //number option kena set full mark berapa
                            if($val == 1){
                                $score += $option;
                            }
                        }
                    }
                }
            }
        }

        if($total==0){
            return 0;
        }
        $per = $score / $total;
        $per = $per * 100;
        $per = number_format($per,2,'.','');
        list($award_val, $award_text) = self::calcAward($per);
        $per = $per + 0;

        return [$total, $score, $per, $award_text, $award_val];
        
    }

    public function getIsComplete(){
        $rubric = $this->rubric;
        $cat = $rubric->categories;
        if($cat){
            foreach($cat as $c){
                $items = $c->items;
                if($items){
                    foreach($items as $item){
                        $option = $item->option_number;
                        $colum = $item->colum_ans;
                        $val = $this->$colum;
                        if($item->item_type== 1 || $item->item_type==2){
                            if($val > 0){
                                //
                            }else{
                                return false;
                            }
                        }
                        
                    }
                }
            }
        }
        return true;
    }

    public function getIsCompleteText(){
        return $this->isComplete ? 'Yes' : 'No';
    }

    public function getAwardValue(){
        list($total, $score, $percent, $award, $val) = $this->totalScorePercent;
        return $val;
    }

    public function getScoreValue(){
        list($total, $score, $percent, $award, $val) = $this->totalScorePercent;
        return $percent;
    }


    public static function calcAward($per){
        $list = ProgramRegistration::listAward();
        foreach($list as $key => $val){
            if($per >= $key){
                return [$key, $val];
            }
        }
        return [0, '-'];
    }
    
}
