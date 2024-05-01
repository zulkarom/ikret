<?php


use yii\helpers\Html;
use yii\helpers\Url;

$context = $this->context;
$attr = $context->attribute;
$db_file_arr = explode('_', $attr);
$attr_name = $db_file_arr[0];
$db_file =  $attr_name  . '_file';
$model = $context->model;


if($model->{$db_file}){
	$style_file = '';
	$style_btn = 'style="display:none"';
	
}else{
	$style_file = 'style="display:none"';
	$style_btn = '';
	
}

$unique = $attr_name . '_' . $model->id;

?>
<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
<div id="fileupload-container-<?=$unique?>">
<div class="form-group">
<div class="row">
<div class="col-md-3">
<label><?=$model->getAttributeLabel($db_file)?></label>
</div>
<div class="col-md-5">

<div class="fileupload-buttonbar" id="btn_<?=$unique ?>" <?=$style_btn?>>
    <div>
        <!-- The fileinput-button span is used to style the file input field as button -->
        <span class="btn btn-success fileinput-button">
            <i class="glyphicon glyphicon-plus"></i>
            <span><?= Yii::t('jqueryfileupload', 'Muat Naik') ?>...</span>

            <?php
			
                $name = $context->model instanceof \yii\base\Model && $attr !== null ? Html::getInputName($model, $attr) : $context->name;
				
                $value = $context->model instanceof \yii\base\Model && $attr !== null ? Html::getAttributeValue($model, $attr) : $context->value;
				
                echo Html::hiddenInput($name, $value).
				Html::fileInput($name, $value, $context->options );
				
				
            ?>

        </span>
    </div>
    <!-- The global progress state -->
    
</div>

<div class="fileupload-progress">
        <!-- The global progress bar -->
        <div id="progress_<?=$unique ?>" class="progress" style="display:none;margin-top:10px;">
            <div class="progress-bar progress-bar-success"></div>
        </div>
    </div>
	<div id="errors_<?=$unique ?>" style="color:red"></div>
	
	
<!-- The container for the uploaded files -->

<div id="file_<?=$unique ?>"  <?=$style_file?>>
<div class="file" >
<p id="img_<?=$unique ?>">
<?php 
if($model->{$db_file}){
	//pdf Url::to('@web/images/')
	$ext = pathinfo($model->{$db_file}, PATHINFO_EXTENSION);
	if($ext == 'pdf'){
		$link = Url::to('@web/images/') . 'pdf.png';
	}else{
		$link = Url::to([$model->file_controller . '/download', 'attr' => $attr_name, 'id' => $model->id]);
	}
	?>
	<a href="<?=Url::to([$model->file_controller . '/download', 'attr' => $attr_name, 'id' => $model->id])?>" target="_blank">
			<img src="<?=$link?>" width="60" /></a>
	<?php
	

}

?>

</p>

</div>




</div>


</div>
<div class="col-md-4">
<div class="form-group" id="action_<?=$unique?>" <?=$style_file?>>

<a href="<?=Url::to([$model->file_controller . '/download', 'attr' => $attr_name, 'id' => $model->id])?>" id="download_<?=$attr?>" target="_blank" class="btn btn-success"><span class="glyphicon glyphicon-download-alt"></span></a> 

<a href="#" id="remove_<?=$unique?>" class="btn btn-danger" data-type="DELETE" data-url="<?=Url::to([$model->file_controller . '/delete', 'attr' => $attr_name, 'id' => $model->id])?>" title="Delete"><span class="glyphicon glyphicon-remove"></span></a>



</div>
<div id="action_del_<?=$unique?>" <?=$style_btn?>> <a href="<?=Url::to([$model->file_controller . '/delete-row', 'id' => $model->id])?>" id="remove_db_<?=$unique?>" class="btn btn-danger" title="Delete"><span class="glyphicon glyphicon-remove"></span></a>
</div>




</div>
</div>
</div>
</div>

<?php 

$js = "

$('#remove_$unique ').click(function(e, data){
	  e.preventDefault();
	  

  var link = $(this);
  link.html('deleting...');
  link.attr('disabled', true)

  var req = $.ajax({
    dataType: 'json',
    url: link.data('url'),
    type: 'DELETE',
	success: function(result){
		if(result.good == 1){
			$('#file_$unique ').hide();
			$('#action_$unique ').hide();
			$('#btn_$unique ').show();
			$('#img_$unique ').html('');
			link.html('<span class=\"glyphicon glyphicon-remove\"></span>');
			link.attr('disabled', false);
		}else if(result.good == 2){
			$('#fileupload-container-$unique ').remove();
		}
	}
  });

});

"
;

$this->registerJs($js);

?>

