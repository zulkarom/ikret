<?php

use yii\helpers\Html;

/** @var yii\web\View $this */

$this->title = 'Import Jury Profiles (CSV)';
$this->params['breadcrumbs'][] = ['label' => 'Jury Profiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

</div><!-- End Page Title -->

<section class="section">
    <div class="card">
        <div class="card-body pt-4">
            <p class="text-muted">
                This importer will create/update <code>jury_profiles</code> based on the CSV (matched by <code>email</code>),
                and will also ensure the user has the <code>jury</code> role.
            </p>

            <h5>Required columns</h5>
            <table class="table table-bordered">
                <tbody>
                <tr><th style="width:240px;">email</th><td>User email</td></tr>
                <tr><th>fullname</th><td>Full name</td></tr>
                <tr><th>category</th><td>Jury category (e.g. <code>Academic</code> / <code>Industry</code>)</td></tr>
                </tbody>
            </table>

            <h5>Optional columns</h5>
            <table class="table table-bordered">
                <tbody>
                <tr><th style="width:240px;">phone</th><td>Phone number</td></tr>
                <tr><th>institution</th><td>Institution</td></tr>
                <tr><th>designation</th><td>Designation</td></tr>
                <tr><th>address</th><td>Address</td></tr>
                </tbody>
            </table>

            <h5>Example CSV</h5>
            <pre>email,fullname,category,phone,institution,designation,address
jury1@example.com,Dr. A,Academic,0123456789,UMK,Senior Lecturer,Campus
jury2@example.com,Mr. B,Industry,0199999999,Company Sdn Bhd,Engineer,Address line</pre>

            <h5>Upload CSV</h5>
            <?= Html::beginForm(['import-csv'], 'post', ['enctype' => 'multipart/form-data']) ?>
            <div class="mb-3">
                <?= Html::fileInput('csv_file', null, ['class' => 'form-control', 'accept' => '.csv']) ?>
            </div>
            <div class="mb-3">
                <?= Html::submitButton('Import', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Back', ['index'], ['class' => 'btn btn-secondary']) ?>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</section>
