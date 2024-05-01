<?php
use kartik\widgets\ActiveForm;

?>


<?php $form = ActiveForm::begin([
	'id' => 'sel-result-form',
	'method' => 'get',

	]); ?>
	    <?= $form->field($model, 'others', [
	    'addon' => ['prepend' => ['content'=>'<span class="fa fa-search"></span>']]])->label(false)->textInput(['placeholder' => "Search Social Impact"]) ?> 
<?php ActiveForm::end(); ?>


<?php 

$this->registerJs('

$("#socialimpactsearch-others").change(function(){
    $("#sel-result-form").submit();
});

');

?>