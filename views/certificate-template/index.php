<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\CertificateTemplate[] $models */
/** @var app\models\Setting|null $setting */

$this->title = 'Certificate Config';

$certificateDescriptions = [
    1 => 'Per program registration. Uses participant/member names and the registered program.',
    2 => 'Committee member certificate.',
    3 => 'Jury certificate for assigned program judging.',
    4 => 'Achievement certificate for participants with an award.',
    5 => 'Medal certificate for participants with Gold, Silver, or Bronze awards.',
    6 => 'General attendance certificate. Uses the user name after at least one QR/session attendance scan.',
    7 => 'Per session attendance certificate. Uses attended session details.',
];
?>

<div class="certificate-template-index">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Certificate Config</h5>

                    <?= Html::beginForm(['index'], 'post') ?>
                    <div class="row g-3 align-items-end mb-3">
                        <div class="col-md-4">
                            <label class="form-label" for="allow-cert-from">General Certificate Release Date</label>
                            <?= Html::input('date', 'allow_cert_from', $setting ? $setting->allow_cert_from : null, ['class' => 'form-control', 'id' => 'allow-cert-from']) ?>
                        </div>
                        <div class="col-md-8">
                            <?= Html::submitButton('Save Release Settings', ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Certificate Type</th>
                                    <th>Description</th>
                                    <th>Publish Status</th>
                                    <th>Template</th>
                                    <th>Orientation</th>
                                    <th>Text</th>
                                    <th width="12%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($models as $model): ?>
                                    <tr>
                                        <td><?= (int)$model->id ?></td>
                                        <td><?= Html::encode(ucwords((string)$model->template_name)) ?></td>
                                        <td><?= Html::encode($certificateDescriptions[(int)$model->id] ?? '') ?></td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <?= Html::checkbox('published[' . $model->id . ']', (int)$model->published === 1, ['class' => 'form-check-input', 'id' => 'published-' . $model->id]) ?>
                                                <label class="form-check-label" for="published-<?= (int)$model->id ?>">
                                                    <?= (int)$model->published === 1 ? 'Published' : 'Unpublished' ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($model->template_file)): ?>
                                                <a href="<?= Url::to('@web/images/' . ltrim($model->template_file, '/')) ?>" target="_blank">
                                                    View
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Not uploaded</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= Html::encode($model->orientationLabel) ?></td>
                                        <td>
                                            <span class="text-muted">Name:</span>
                                            top <?= Html::encode((string)$model->name_mt) ?>,
                                            size <?= Html::encode((string)$model->name_size) ?>,
                                            <?= Html::encode($model->alignLabel) ?>
                                            <br>
                                            <span class="text-muted">Left:</span>
                                            <?= Html::encode((string)$model->margin_left) ?>
                                        </td>
                                        <td>
                                            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (!$models): ?>
                                    <tr>
                                        <td colspan="7" class="text-muted">No certificate template found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>
    </div>
</div>
