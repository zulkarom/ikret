<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'List of Mentees';
$this->params['breadcrumbs'][] = $this->title;
?>
  <div class="pagetitle">
<h1><?=$this->title?></h1>
<h4><?=$user->fullname?></h4>
</div>

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
                'label' =>'Participants',
                'format' => 'raw',
                'value' => function($model){
                    return $model->registration->shortFieldsHtml;
                }
            ],
            [
                'label' =>'Members',
                'format' => 'raw',
                'value' => function($model){
                    return $model->registration->memberStr;
                }
            ],
            [
                'label' =>'Certificates',
                'format' => 'raw',
                'value' => function($model){
                    $reg = $model->registration;
                    $str = '<ul>';
                    $str .= '<li>' . Html::a('Cert. of Participation',['/program/cert-participation', 'reg' => $reg->id],['target' => '_blank']) . '</li>';

                    if($reg->award > 0 && $reg->score > 0){
                        $str .= '<li>' . Html::a('Cert. of Achievement ('.$reg->awardTextColor().')',['/program/cert-achievement', 'reg' => $reg->id],['target' => '_blank']) . '</li>';
                    }
                    if($reg->achievements){
                        foreach($reg->achievements as $v){
                            //
                            $str .= '<li>' . Html::a('Cert. of Excellence (<span style="color:#DA9100">'.$v->achieve->name.'</span>)',['/program/cert-excellence', 'reg' => $reg->id],['target' => '_blank']) . '</li>';
                        }
                        
                    }


                    return $str;
                }
            ],
  
        ],
    ]); ?>

</div>
            </div>
        </div>



    </section>
