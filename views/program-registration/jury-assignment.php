<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Jury Assignments';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss(<<<CSS
.participant-cell-main {
    font-weight: 700;
}

.participant-cell-members {
    margin: 0.35rem 0 0 1.1rem;
    padding: 0;
    font-size: 0.86rem;
    line-height: 1.35;
    color: #6c757d;
}
CSS);
?>
  <div class="pagetitle">
<h1><?=$this->title?></h1></div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    <div class="card">
            <div class="card-body pt-4">
            <div class="table-responsive">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
                'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
       // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' =>'Competition',
                'value' => function($model){
                    $registration = $model->registration;
                    $programText = $registration && $registration->program ? $registration->program->program_abbr : '';
                    if($registration && $registration->programSub){
                        $programText .= ' / ' . $registration->programSub->sub_name;
                    }
                    return $programText;
                }
            ],
            [
                'label' =>'Participants',
                'format' => 'html',
                'value' => function($model){
                    $registration = $model->registration;
                    if(!$registration){
                        return '';
                    }

                    $html = '';
                    if($registration->flag == 1){
                        $html .= '<i class="bi bi-flag-fill" style="color:blue"></i> ';
                    }

                    if($registration->user){
                        $leaderName = trim((string)$registration->user->fullname);
                        $text = $leaderName;
                        $leaderMatric = trim((string)$registration->user->matric);
                    }else if(!empty($registration->contact_person)){
                        $leaderName = trim((string)$registration->contact_person);
                        $text = $leaderName;
                        $leaderMatric = '';
                    }else if(!empty($registration->contact_email)){
                        $leaderName = trim((string)$registration->contact_email);
                        $text = $leaderName;
                        $leaderMatric = '';
                    }else{
                        $leaderName = 'Participant';
                        $text = 'Participant';
                        $leaderMatric = '';
                    }

                    if(!empty($registration->group_name)){
                        $text .= ' (' . $registration->group_name . ')';
                    }
                    $html .= '<div class="participant-cell-main">' . Html::encode($text) . '</div>';

                    $memberItems = [];
                    foreach($registration->members as $member){
                        $memberName = trim((string)$member->member_name);
                        $memberMatric = trim((string)$member->member_matric);
                        if($memberName === ''){
                            continue;
                        }
                        if($leaderMatric !== '' && $memberMatric !== '' && strcasecmp($leaderMatric, $memberMatric) === 0){
                            continue;
                        }
                        if(strcasecmp($leaderName, $memberName) === 0){
                            continue;
                        }
                        $memberLabel = $memberName;
                        if($memberMatric !== ''){
                            $memberLabel .= ' (' . $memberMatric . ')';
                        }
                        $memberItems[] = '<li>' . Html::encode($memberLabel) . '</li>';
                    }

                    if($memberItems){
                        $html .= '<ul class="participant-cell-members">' . implode('', $memberItems) . '</ul>';
                    }

                    return $html;
                }
            ],
            'statusLabel:html',
            [
                'label' =>'Result',
                'value' => function($model){
                    if($model->score){
                        return $model->score;
                    }else{
                        return 0;
                    }
                }
            ],

            ['class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width: 13%'],
            'template' => '{view}',
            //'visible' => false,
            'buttons'=>[
                'view'=>function ($url, $model) {
                    return Html::a('Judge',['jury-judge', 'id' => $model->id],['class'=>'btn btn-primary btn-sm']);
                },
            ],
        
        ],
  
        ],
    ]); ?>

</div>
            </div>
        </div>



    </section>
