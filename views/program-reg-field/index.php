<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Registration Fields';

?>

<div class="program-reg-field-index">

    <div class="card">
        <div class="card-header">
            <?=$this->title?>
        </div>
        <div class="card-body pt-4">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Program</label>
                    <select class="form-select" onchange="window.location=this.value;">
                        <?php foreach($programs as $p){
                            $selected = (int)$p->id === (int)$program->id ? 'selected' : '';
                            $url = Url::to(['index', 'program_id' => $p->id]);
                            echo '<option value="'.$url.'" '.$selected.'>'.$p->program_name.'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <?php $form = ActiveForm::begin(); ?>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="45%">Field</th>
                            <th width="15%">Enabled</th>
                            <th width="15%">Required</th>
                            <th width="15%">Show Matric</th>
                            <th width="15%">Sort</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($available as $fieldName => $label){
                            $row = array_key_exists($fieldName, $existing) ? $existing[$fieldName] : null;
                            $enabled = $row ? (int)$row->is_enabled === 1 : 0;
                            $required = $row ? (int)$row->is_required === 1 : 0;
                            $showMatric = $row ? (int)$row->show_matric === 1 : 1;
                            $sort = $row ? (int)$row->sort_order : 0;
                        ?>
                        <tr>
                            <td>
                                <b><?=Html::encode($label)?></b><br />
                                <small><?=Html::encode($fieldName)?></small>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="Field[enabled][<?=$fieldName?>]" value="1" <?=$enabled ? 'checked' : ''?> />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="Field[required][<?=$fieldName?>]" value="1" <?=$required ? 'checked' : ''?> />
                            </td>
                            <td class="text-center">
                                <?php if($fieldName === 'group_member'){ ?>
                                <input type="checkbox" name="Field[show_matric][<?=$fieldName?>]" value="1" <?=$showMatric ? 'checked' : ''?> />
                                <?php } ?>
                            </td>
                            <td>
                                <input type="number" class="form-control" name="Field[sort][<?=$fieldName?>]" value="<?=$sort?>" />
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

</div>
