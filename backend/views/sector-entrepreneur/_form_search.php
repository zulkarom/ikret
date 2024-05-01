<?php
use kartik\widgets\ActiveForm;
use backend\models\Sector;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
?>


<?php $form = ActiveForm::begin([
	'id' => 'sel-result-form',
	'method' => 'get',

]); ?>

	<?= $form->field($model, 'sector_id')->dropDownList(ArrayHelper::map(Sector::find()->all(), 'id', 'sector_name'), ['prompt' => 'Select Sector'])->label(false) ?>

<?php ActiveForm::end(); ?>


<?php 

$this->registerJs('

$("#sectorentrepreneursearch-sector_id").change(function(){
    $("#sel-result-form").submit();
});

');

?>