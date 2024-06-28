<?php

use app\models\JuryAssign;
use app\models\User;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'List of Juries';
$this->params['breadcrumbs'][] = $this->title;
?>
  <div class="pagetitle">
<h1><?=$this->title?></h1></div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    <button type="button" class="btn btn-primary" id="btn-add-curr-user"><i class="bi bi-plus"></i> Add From Existing User</button>  <button type="button" class="btn btn-success" id="btn-add-new-user"><i class="bi bi-plus"></i> Register New User</button>
    <br />

    <div class="form-group" id="con-curr-user" style="display: none;">

<br />
<?php $form = ActiveForm::begin(); ?>

    <?php 
    
    $userDesc =  '';

$url = Url::to(['/user/user-list-json']);
echo $form->field($userRole, 'user_id')->widget(Select2::classname(), [
    'initValueText' => $userDesc, // set the initial display text
    'options' => ['placeholder' => 'Search User ...'],
'pluginOptions' => [
    'allowClear' => true,
    'minimumInputLength' => 3,
    'language' => [
        'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
    ],
    'ajax' => [
        'url' => $url,
        'dataType' => 'json',
        'data' => new JsExpression('function(params) { return {q:params.term}; }')
    ],
    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
    'templateResult' => new JsExpression('function(user) { return user.text; }'),
    'templateSelection' => new JsExpression('function (user) { return user.text; }'),
    //'dropdownParent' => new yii\web\JsExpression('$("#ajaxCrudModal")')
],
])->label('Select form existing user');

    ?>



<div class="form-group">
<?= Html::submitButton('Add Jury', ['class' => 'btn btn-primary']) ?> <a href="javascript:void(0)" id="btn-hide-curr-user">Hide Form</a>
    </div>

    <?php ActiveForm::end(); ?>





    </div>
    <div class="form-group" id="con-new-user" style="display: none;">
<br />
    <?= $this->render('../site/_register_form', [
        'model' => $newUser,
    ]) ?> <a href="javascript:void(0)" id="btn-hide-new-user">Hide Form</a>

    </div>
<br />

<?php 
$this->registerJs('
$("#btn-add-curr-user").click(function(){
    $("#con-curr-user").slideDown();
    $("#con-new-user").slideUp();
});

$("#btn-hide-curr-user").click(function(){
    $("#con-curr-user").slideUp();
});

$("#btn-add-new-user").click(function(){
    $("#con-new-user").slideDown();
    $("#con-curr-user").slideUp();
});

$("#btn-hide-new-user").click(function(){
    $("#con-new-user").slideUp();
});

');

?>







    <div class="card">
            <div class="card-body pt-4">
            <div class="table-responsive">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
                'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'fullname',
            ],
            [
                'attribute' => 'email',
                'label' => 'Email /Phone',
                'format' => 'html',
                'value' => function($model){
                    return $model->email . ' ' . $model->phone;
                }
            ],
            [
                'attribute' => 'kira',
                'label' => 'No. Assign',
                'format' => 'html',
                 'value' => function($model){
                    //cari berapa complete
                    $complete = JuryAssign::find()->where(['user_id' => $model->id, 'status' => 20])->count();
                    $all = $model->kira;
                    if($complete == $all){
                        $color = 'success';
                    }else{
                        $color = 'warning';
                    }
                    return '<span class="badge bg-'.$color.'">' . $complete . ' / ' . $all . '</span>';
                } 
            ],
            ['class' => 'yii\grid\ActionColumn',
            //'contentOptions' => ['style' => 'width: 13%'],
            'template' => '{view}',
            //'visible' => false,
            'buttons'=>[
                'view'=>function ($url, $model) {
                    return Html::a('Cert.',['/program-registration/jury-cert-page', 'u' => $model->id],['class'=>'btn btn-primary btn-sm']);
                },
            ],
        
        ],
  
        ],
    ]); ?>

</div>
            </div>
        </div>



    </section>
