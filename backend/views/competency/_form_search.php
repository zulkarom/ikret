<?php
use kartik\widgets\ActiveForm;

?>


<?php $form = ActiveForm::begin([
	'id' => 'sel-result-form',
	'method' => 'get',

	]); ?>
	    <?= $form->field($model, 'others', [
	    'addon' => ['prepend' => ['content'=>'<span class="fa fa-search"></span>']]])->label(false)->textInput(['placeholder' => "Search Competency"]) ?> 
<?php ActiveForm::end(); ?>


<?php 

$this->registerJs('

$("#competencysearch-others").change(function(){
    $("#sel-result-form").submit();
});

');

?>