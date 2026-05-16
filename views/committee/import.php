<?php

use yii\helpers\Html;

/** @var yii\web\View $this */

$this->title = 'Import Committees';
$this->params['breadcrumbs'][] = ['label' => 'Committees', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$samplePath = Yii::getAlias('@app') . '/db/imports/committee_data.csv';
$sampleRows = [];
if(is_file($samplePath) && ($handle = fopen($samplePath, 'r')) !== false){
    $header = fgetcsv($handle);
    if($header){
        $idx = array_flip($header);
        $sampleRows[] = $header;
        $selected = [];
        $selectedHashes = [];
        $sampleCases = [
            'non_jawatankuasa_staff' => function($row) use ($idx){
                return (int)($row[$idx['is_jawatankuasa']] ?? 0) === 0
                    && (int)($row[$idx['is_student']] ?? 0) === 0
                    && (int)($row[$idx['is_pengarah']] ?? 0) === 0;
            },
            'pengarah_staff' => function($row) use ($idx){
                return (int)($row[$idx['is_pengarah']] ?? 0) === 1
                    && (int)($row[$idx['is_student']] ?? 0) === 0;
            },
            'jawatankuasa_head' => function($row) use ($idx){
                return (int)($row[$idx['is_jawatankuasa']] ?? 0) === 1
                    && (int)($row[$idx['is_leader']] ?? 0) === 1;
            },
            'jawatankuasa_member' => function($row) use ($idx){
                return (int)($row[$idx['is_jawatankuasa']] ?? 0) === 1
                    && (int)($row[$idx['is_leader']] ?? 0) === 0
                    && (int)($row[$idx['is_student']] ?? 0) === 0;
            },
            'student_member' => function($row) use ($idx){
                return (int)($row[$idx['is_student']] ?? 0) === 1;
            },
            'blank_staff_email' => function($row) use ($idx){
                return (int)($row[$idx['is_student']] ?? 0) === 0
                    && trim((string)($row[$idx['username']] ?? '')) === '';
            },
        ];

        while(($row = fgetcsv($handle)) !== false){
            $hash = md5(json_encode($row));
            if(isset($selectedHashes[$hash])){
                continue;
            }
            foreach($sampleCases as $case => $matcher){
                if(isset($selected[$case])){
                    continue;
                }
                if($matcher($row)){
                    $selected[$case] = $row;
                    $selectedHashes[$hash] = true;
                    break;
                }
            }

            if(count($selected) === count($sampleCases)){
                break;
            }
        }

        foreach(array_keys($sampleCases) as $case){
            if(isset($selected[$case])){
                $sampleRows[] = $selected[$case];
            }
        }
    }
    fclose($handle);
}

$guidelineRows = [
    ['committee_name', 'Yes', 'committee.com_name_en', 'Registration Committee'],
    ['committee_name_bm', 'No', 'committee.com_name_bm', 'JAWATANKUASA PENDAFTARAN'],
    ['committee_order', 'No', 'committee.committee_order', '1'],
    ['is_jawatankuasa', 'Yes', 'committee.is_jawatankuasa', '1'],
    ['is_student', 'Yes', 'committee.is_student', '0'],
    ['is_pengarah', 'Yes', 'committee.is_pengarah', '0'],
    ['can_approve', 'Yes', 'committee.can_approve', '0'],
    ['cert_only', 'Yes', 'committee.cert_only', '0'],
    ['member_name', 'Yes', 'user.fullname', 'Dr. Siti Aminah binti Ali'],
    ['role', 'Yes', 'Import helper for is_leader', 'Head'],
    ['is_leader', 'Yes', 'user_role.is_leader', '1'],
    ['username', 'Yes', 'Student matric or staff email', 'staff@umk.edu.my / A24A3800'],
];
?>

<div class="committee-import">
    <div class="pagetitle">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <section class="section dashboard">
        <div class="card">
            <div class="card-body pt-4">
                <div class="row">
                    <div class="col-lg-5">
                        <?= Html::beginForm(['import'], 'post', ['enctype' => 'multipart/form-data']) ?>
                            <label for="csv_file" class="form-label fw-semibold">CSV File</label>
                            <?= Html::fileInput('csv_file', null, ['class' => 'form-control mb-3', 'accept' => '.csv,text/csv', 'required' => true]) ?>
                            <?= Html::submitButton('Import CSV', ['class' => 'btn btn-primary']) ?>
                            <?= Html::a('Back', ['index'], ['class' => 'btn btn-secondary']) ?>
                        <?= Html::endForm() ?>
                    </div>

                    <div class="col-lg-7">
                        <h5>Guideline</h5>
                        <ul>
                            <li>Required committee columns: <code>committee_name</code>, <code>is_jawatankuasa</code>, <code>is_student</code>, <code>is_pengarah</code>, <code>can_approve</code>, <code>cert_only</code>.</li>
                            <li>Optional committee columns: <code>committee_name_bm</code>, <code>committee_order</code>.</li>
                            <li>Required member columns: <code>member_name</code>, <code>role</code>, <code>is_leader</code>, <code>username</code>.</li>
                            <li>Use <code>1</code> for yes/true and <code>0</code> for no/false in committee columns.</li>
                            <li><code>is_jawatankuasa</code> means a committee with a special task, normally one head and many members.</li>
                            <li><code>is_student</code> controls how <code>username</code> is handled: <code>1</code> means student matric, <code>0</code> means staff email.</li>
                            <li><code>is_leader</code> marks the head of that committee: <code>1</code> when <code>role</code> is <code>Head</code>, otherwise <code>0</code>.</li>
                            <li>If <code>committee_name</code> and <code>committee_name_bm</code> are empty, the importer will reuse the previous row's committee data (useful to add multiple members under the same committee).</li>
                            <li>For student rows, <code>username</code> must contain matric. The importer uses the standard student account helper and creates the account if needed.</li>
                            <li>For staff rows, <code>username</code> must contain the staff email. Blank staff email rows are skipped.</li>
                            <li>Existing committee roles are reactivated and updated to approved status.</li>
                        </ul>
                    </div>
                </div>

                <h5 class="mt-4">Required Column Guide</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Column</th>
                                <th>Required</th>
                                <th>System Field</th>
                                <th>Example</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($guidelineRows as $row): ?>
                                <tr>
                                    <?php foreach($row as $cell): ?>
                                        <td><?= Html::encode($cell) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if($sampleRows): ?>
                    <h5 class="mt-4">Sample CSV Preview</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <?php foreach($sampleRows as $rowIndex => $row): ?>
                                    <tr>
                                        <?php foreach($row as $cell): ?>
                                            <?php if($rowIndex === 0): ?>
                                                <th class="text-danger"><?= Html::encode($cell) ?></th>
                                            <?php else: ?>
                                                <td><?= Html::encode($cell) ?></td>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
