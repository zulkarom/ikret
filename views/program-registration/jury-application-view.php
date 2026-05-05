<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\JuryApplication $model */

$this->title = 'Jury Application #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Jury Applications', 'url' => ['/program-registration/admin-jury-applications-all']];
$this->params['breadcrumbs'][] = $this->title;

$programText = $model->program ? $model->program->program_name : null;
if($model->programSub){
    $sub = $model->programSub->sub_name;
    $programText = $programText ? ($programText . ' / ' . $sub) : $sub;
}

$sessionText = null;
if($model->judgingSession){
    $s = $model->judgingSession;
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

    $sessionText = $s->session_name;
    if($range !== ''){
        $sessionText .= ' (' . $range . ')';
    }
}

$statusClass = 'bg-secondary';
if((int)$model->status === 10){
    $statusClass = 'bg-success';
}elseif((int)$model->status === 20){
    $statusClass = 'bg-danger';
}

$jury = $model->juryProfile;
$email = ($jury && $jury->user) ? $jury->user->email : null;

?>

<div class="pagetitle">
    <h1><?=Html::encode($this->title)?></h1>
</div>

</div><!-- End Page Title -->

<section class="section">
    <div class="card">
        <div class="card-body pt-4">

            <p>
                <?= Html::a('Back', Yii::$app->request->referrer ?: ['/program-registration/admin-jury-applications-all'], ['class' => 'btn btn-secondary']) ?>
            </p>

            <table class="table table-bordered">
                <tbody>
                <tr>
                    <th style="width: 220px;">Program / Sub</th>
                    <td><?= Html::encode($programText) ?></td>
                </tr>
                <tr>
                    <th>Session</th>
                    <td><?= Html::encode($sessionText) ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?= Html::tag('span', Html::encode($model->statusText), ['class' => 'badge ' . $statusClass]) ?></td>
                </tr>
                <tr>
                    <th>Applied At</th>
                    <td><?= $model->created_at ? Html::encode(date('Y-m-d H:i', $model->created_at)) : null ?></td>
                </tr>
                </tbody>
            </table>

            <h5>Applicant</h5>
            <table class="table table-bordered">
                <tbody>
                <tr>
                    <th style="width: 220px;">Full Name</th>
                    <td><?= Html::encode($jury ? $jury->fullname : null) ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= Html::encode($email) ?></td>
                </tr>
                <tr>
                    <th>Category</th>
                    <td><?= Html::encode($jury ? $jury->category : null) ?></td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td><?= Html::encode($jury ? $jury->phone : null) ?></td>
                </tr>
                <tr>
                    <th>Institution</th>
                    <td><?= Html::encode($jury ? $jury->institution : null) ?></td>
                </tr>
                <tr>
                    <th>Designation</th>
                    <td><?= Html::encode($jury ? $jury->designation : null) ?></td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td><?= Html::encode($jury ? $jury->address : null) ?></td>
                </tr>
                </tbody>
            </table>

        </div>
    </div>
</section>
