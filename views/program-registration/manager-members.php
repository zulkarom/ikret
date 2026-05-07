<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $registration */
/** @var app\models\Member[] $members */
/** @var app\models\ProgramSub|null $programSub */
/** @var int|null $sub */

$program = $registration->program;
$subText = $programSub ? ' / ' . $programSub->sub_abbr : '';
$this->title = 'Edit Group Members (' . $program->program_abbr . $subText . ')';

$this->params['breadcrumbs'][] = [
    'label' => $program->program_abbr . $subText,
    'url' => ['/program-registration/manager', 'id' => $program->id, 'sub' => $sub],
];
$this->params['breadcrumbs'][] = [
    'label' => 'Registration Details',
    'url' => ['/program-registration/manager-view', 'id' => $registration->id, 'sub' => $sub],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= Url::to(['/']) ?>"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item"><a href="<?= Url::to(['/program-registration/manager', 'id' => $program->id, 'sub' => $sub]) ?>">Participants</a></li>
        <li class="breadcrumb-item"><a href="<?= Url::to(['/program-registration/manager-view', 'id' => $registration->id, 'sub' => $sub]) ?>">Registration Details</a></li>
        <li class="breadcrumb-item active"><?= Html::encode($this->title) ?></li>
    </ol>
</nav>

<section class="section dashboard">
    <div class="card">
        <div class="card-header">
            <?= Html::encode($registration->group_name ?: $registration->participantText) ?>
        </div>
        <div class="card-body pt-4">
            <?php $form = ActiveForm::begin(['id' => 'manager-member-form']); ?>

            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper',
                'widgetBody' => '.container-items',
                'widgetItem' => '.member-item',
                'limit' => 50,
                'min' => 1,
                'insertButton' => '.add-member',
                'deleteButton' => '.remove-member',
                'model' => $members[0],
                'formId' => 'manager-member-form',
                'formFields' => [
                    'id',
                    'member_name',
                    'member_matric',
                ],
            ]); ?>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th style="width: 25%;">Matric No.</th>
                            <th class="text-center" style="width: 90px;"></th>
                        </tr>
                    </thead>
                    <tbody class="container-items">
                    <?php foreach($members as $i => $member): ?>
                        <tr class="member-item">
                            <td>
                                <?php
                                if(!$member->isNewRecord){
                                    echo Html::activeHiddenInput($member, "[{$i}]id");
                                }
                                ?>
                                <?= $form->field($member, "[{$i}]member_name")->textInput(['style' => 'text-transform: uppercase'])->label(false) ?>
                            </td>
                            <td>
                                <?= $form->field($member, "[{$i}]member_matric")->textInput(['style' => 'text-transform: uppercase'])->label(false) ?>
                            </td>
                            <td class="text-center">
                                <button type="button" class="remove-member btn btn-outline-danger btn-sm"><span class="bi bi-trash"></span></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">
                                <button type="button" class="add-member btn btn-outline-success btn-sm"><span class="bi bi-plus"></span> Add Member</button>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <?php DynamicFormWidget::end(); ?>

            <div class="d-flex gap-2">
                <?= Html::submitButton('Save Members', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Cancel', ['manager-view', 'id' => $registration->id, 'sub' => $sub], ['class' => 'btn btn-outline-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</section>
