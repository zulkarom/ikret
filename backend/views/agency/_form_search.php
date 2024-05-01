<?php
use kartik\widgets\ActiveForm;

?>


<?php $form = ActiveForm::begin([
	'id' => 'sel-result-form',
	'method' => 'get',

	]); ?>
	    <?= $form->field($model, 'nama_agensi', [
	    'addon' => ['prepend' => ['content'=>'<span class="fa fa-search"></span>']]])->label(false)->textInput(['placeholder' => "Search Agency"]) ?> 
<?php ActiveForm::end(); ?>


<?php 

$this->registerJs('

$("#agencysearch-nama_agensi").change(function(){
    $("#sel-result-form").submit();
});

');

?>