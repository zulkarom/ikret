<?php

use yii\helpers\Html;
use app\models\Program;
use app\models\ProgramSub;
use app\models\ProgramRubric;
use app\models\RubricJudgingSession;

/** @var yii\web\View $this */

$this->title = 'Import Jury Applications (CSV)';
$this->params['breadcrumbs'][] = ['label' => 'Jury Applications', 'url' => ['/program-registration/admin-jury-applications-all']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="pagetitle">
    <h1><?=Html::encode($this->title)?></h1>
</div>

</div><!-- End Page Title -->

<section class="section">
    <div class="card">
        <div class="card-body pt-4">

            <p class="text-muted">
                This importer creates new rows in <code>jury_applications</code> using the CSV (matched by email).
                If the user does not exist and <code>fullname</code> is provided, the user account will be created.
                If a user does not have a <code>jury_profiles</code> record yet, it will be created from the CSV (if columns provided).
            </p>

            <h5>Required columns</h5>
            <table class="table table-bordered">
                <tbody>
                <tr><th style="width:240px;">email</th><td>User email (must exist in the system)</td></tr>
                <tr><th>judging_session_id</th><td>Session ID (must exist in the system)</td></tr>
                </tbody>
            </table>

            <h5>Optional columns</h5>
            <table class="table table-bordered">
                <tbody>
                <tr><th style="width:240px;">program_id</th><td>Target program ID (only required if the selected session is used by more than one program/sub)</td></tr>
                <tr><th style="width:240px;">program_sub_id</th><td>Sub program ID (nullable)</td></tr>
                <tr><th>fullname</th><td>Used when creating jury profile (if missing)</td></tr>
                <tr><th>category</th><td>Used when creating jury profile (if missing). Accepted values: <code>Academic</code> / <code>Industry</code></td></tr>
                <tr><th>phone</th><td>Used when creating jury profile (if missing)</td></tr>
                <tr><th>institution</th><td>Used when creating jury profile (if missing)</td></tr>
                <tr><th>designation</th><td>Used when creating jury profile (if missing)</td></tr>
                <tr><th>address</th><td>Used when creating jury profile (if missing)</td></tr>
                </tbody>
            </table>

            <?php
            $formatSession = function(RubricJudgingSession $s): string {
                $start = $s->datetime_start ? new \DateTime($s->datetime_start) : null;
                $end = $s->datetime_end ? new \DateTime($s->datetime_end) : null;

                $range = '';
                if($start && $end){
                    if($start->format('Y-m-d') === $end->format('Y-m-d')){
                        $range = $start->format('d M Y') . ' ' . $start->format('H:i') . ' - ' . $end->format('H:i');
                    }else{
                        $range = $start->format('d M Y H:i') . ' - ' . $end->format('d M Y H:i');
                    }
                }elseif($start){
                    $range = $start->format('d M Y H:i');
                }

                $text = $s->id . ' - ' . $s->session_name;
                if($range !== ''){
                    $text .= ' (' . $range . ')';
                }
                return $text;
            };

            $programQuery = Program::find();
            $programTable = \Yii::$app->db->schema->getTableSchema(Program::tableName());
            if($programTable && $programTable->getColumn('is_active')){
                $programQuery->andWhere(['is_active' => 1]);
            }else if($programTable && $programTable->getColumn('status')){
                $programQuery->andWhere(['status' => 10]);
            }

            $subTable = \Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
            $programQuery->with(['programSubs' => function($q) use ($subTable){
                if($subTable && $subTable->getColumn('is_active')){
                    $q->andWhere(['is_active' => 1]);
                }
                $q->orderBy(['sub_name' => SORT_ASC]);
            }]);

            $programs = $programQuery->orderBy(['program_name' => SORT_ASC])->all();
            ?>

            <h5>Available sessions (judging_session_id)</h5>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="width: 40%;">Program / Sub</th>
                    <th>Sessions</th>
                </tr>
                </thead>
                <tbody>
                <?php if($programs): ?>
                    <?php foreach($programs as $p): ?>
                        <?php
                        $programRows = [];
                        $programRubricIds = ProgramRubric::find()->select(['rubric_id'])->where(['program_id' => $p->id])->column();
                        if($programRubricIds){
                            $sessions = RubricJudgingSession::find()->where(['rubric_id' => $programRubricIds])->orderBy(['datetime_start' => SORT_ASC, 'session_name' => SORT_ASC])->all();
                            if($sessions){
                                foreach($sessions as $s){
                                    $programRows[] = Html::encode($formatSession($s));
                                }
                            }
                        }
                        ?>
                        <tr>
                            <td><?= Html::encode($p->program_name) ?></td>
                            <td><?= $programRows ? implode('<br />', $programRows) : '<span class="text-muted">No sessions</span>' ?></td>
                        </tr>

                        <?php if($p->programSubs): ?>
                            <?php foreach($p->programSubs as $sub): ?>
                                <?php
                                $subRows = [];
                                $subRubricIds = ProgramRubric::find()->select(['rubric_id'])->where(['program_id' => $p->id, 'program_sub' => $sub->id])->column();
                                if($subRubricIds){
                                    $sessions = RubricJudgingSession::find()->where(['rubric_id' => $subRubricIds])->orderBy(['datetime_start' => SORT_ASC, 'session_name' => SORT_ASC])->all();
                                    if($sessions){
                                        foreach($sessions as $s){
                                            $subRows[] = Html::encode($formatSession($s));
                                        }
                                    }
                                }
                                ?>
                                <tr>
                                    <td><?= Html::encode($p->program_name . ' / ' . $sub->sub_name) ?></td>
                                    <td><?= $subRows ? implode('<br />', $subRows) : '<span class="text-muted">No sessions</span>' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" class="text-muted">No active programs found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>

            <h5>Example CSV</h5>
            <pre>email,judging_session_id,fullname,category
jury1@example.com,12,Dr. A,Academic
jury2@example.com,12,Mr. B,Industry</pre>

            <h5>Upload CSV</h5>
            <?= Html::beginForm(['/program-registration/jury-application-import-csv'], 'post', ['enctype' => 'multipart/form-data']) ?>
            <div class="mb-3">
                <?= Html::fileInput('csv_file', null, ['class' => 'form-control', 'accept' => '.csv']) ?>
            </div>
            <div class="mb-3">
                <?= Html::submitButton('Import', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Back', ['/program-registration/admin-jury-applications-all'], ['class' => 'btn btn-secondary']) ?>
            </div>
            <?= Html::endForm() ?>

        </div>
    </div>
</section>
