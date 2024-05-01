<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use common\models\Country;
use richardfan\widget\JSRegister;

?>

 
    <?php $form = ActiveForm::begin(); ?>
	
	<div class="row">
<div class="col-md-12">


			
<div class="row">
<div class="col-md-6"><?= $form
            ->field($associate, 'title', ['template' => '{label}<div id="con-title">{input}</div>{error}']
)
            ->label('Title')
            ->dropDownList($associate->defaultTitle()) ?></div>

</div>

<?= $form->field($user, 'fullname')->textInput()->label('Name')?>
<?= $form
				->field($associate, 'institution')
				->textarea(['rows' => 2])
                ->label('Institution')?>
<?= $form
				->field($associate, 'assoc_address')
				->textarea(['rows' => 2])
                ->label('Address')?>
				
<?php 


echo $form->field($associate, 'country_id')->widget(Select2::classname(), [
    'data' => ArrayHelper::map(Country::find()->all(),'id', 'country_name'),
    'language' => 'en',
    'options' => ['multiple' => false,'placeholder' => 'Select a country ...'],
    'pluginOptions' => [
        'allowClear' => true
    ],
])->label('Country');

?>

</div>
</div>
	
 
        <div class="form-group">
            <?= Html::submitButton('Change Profile', ['class' => 'btn btn-primary', 'name' => 'form-action', 'value' => 'change-profile']) ?>
        </div>
    <?php ActiveForm::end(); ?>



<?php JSRegister::begin(); ?>
<script>
$("#associate-title").change(function(){
	var val = $(this).val();
	if(val == 999){
		var html = '<input type="text" id="associate-title" placeholder="Please specify" class="form-control" name="associate[title]" / >';
		$("#con-title").html(html);
	}
});
</script>
<?php JSRegister::end(); ?>