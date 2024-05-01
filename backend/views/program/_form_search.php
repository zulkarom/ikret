<?php
use kartik\widgets\ActiveForm;

?>


<?php $form = ActiveForm::begin([
	'id' => 'sel-result-form',
	'method' => 'get',

	]); ?>
	    <?= $form->field($model, 'prog_name', [
	    'addon' => ['prepend' => ['content'=>'<span class="fa fa-search"></span>']]])->label(false)->textInput(['placeholder' => "Search Program"]) ?> 
<?php ActiveForm::end(); ?>


<?php 

$this->registerJs('

$("#programsearch-prog_name").change(function(){
    $("#sel-result-form").submit();
});

');

?>