<?php

throw new \yii\web\NotFoundHttpException('Page not found.');

use yii\helpers\Html;

$this->title = 'Public Registration Settings';

$programTable = Yii::$app->db->schema->getTableSchema(app\models\Program::tableName());
$publicRegCol = ($programTable && $programTable->getColumn('public_reg_enabled'))
    ? 'public_reg_enabled'
    : (($programTable && $programTable->getColumn('public_registration_enabled')) ? 'public_registration_enabled' : null);
?>
<div class="pagetitle">
<h1><?=$this->title?></h1></div>

</div><!-- End Page Title -->

<section class="section dashboard">

<div class="card">
    <div class="card-body pt-4">
        <form method="post">
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                        <tr><th>No.</th><th>Program</th><th>Status</th><th>Enable Public Registration</th></tr>
                        <?php $i = 1; foreach($programs as $program){ ?>
                            <?php $enabled = $publicRegCol ? (int)$program->getAttribute($publicRegCol) : 0; ?>
                            <tr>
                                <td><?=$i?>.</td>
                                <td>
                                    <?= Html::encode($program->program_name) ?>
                                    <?php if($program->program_abbr){ ?>
                                        <br /><span class="text-muted small"><?= Html::encode($program->program_abbr) ?></span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?= ((int)$enabled === 1) ? 'Enabled' : 'Disabled' ?>
                                </td>
                                <td>
                                    <input type="hidden" name="Program[<?=$program->id?>][public_reg_enabled]" value="0">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="Program[<?=$program->id?>][public_reg_enabled]" value="1" <?=((int)$enabled === 1) ? 'checked' : ''?>>
                                    </div>
                                </td>
                            </tr>
                        <?php $i++; } ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
            </div>
        </form>
    </div>
</div>

</section>
