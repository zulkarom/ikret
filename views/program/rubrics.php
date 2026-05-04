<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */


$this->title = 'Rubric: ' . $model->program_name;

$programSub = $programSub ?? null;
$rubrics = $rubrics ?? [];

$this->params['breadcrumbs'][] = [
    'label' => $model->program_abbr . ($programSub ? ' / ' . $programSub->sub_abbr : ''),
    'url' => ['/program-registration/manager-dashboard', 'id' => $model->id, 'sub' => $programSub ? $programSub->id : null]
];
$this->params['breadcrumbs'][] = $this->title;

?>


<div class="pagetitle">
<h1><?=$this->title?></h1>
<?php 
if($programSub){
    echo $programSub->sub_name;
}

?>
</div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    <div class="card">
            <div class="card-body pt-4">

            <div class="mb-3">
                <form method="post" action="<?= Url::to(['rubric-add', 'id' => $model->id, 'sub' => $programSub ? $programSub->id : null]) ?>">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                    <div class="row g-2 align-items-center">
                        <div class="col-md-8">
                            <input type="text" name="rubric_name" class="form-control" placeholder="New rubric name" />
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success">Add Rubric</button>
                        </div>
                    </div>
                </form>
            </div>

            <table class="table">
                <tbody>
                    <tr><th>No.</th><th>Rubric Name</th><th>Actions</th></tr>
                    <?php 
                    if($rubrics){
                        $i=1;
                        foreach($rubrics as $r){

                            $editKey = 'edit_' . $r->id;
                            $isEdit = Yii::$app->request->get($editKey) == 1;

                            echo '<tr>';
                            echo '<td>'.$i.'. </td>';
                            echo '<td>';

                            if($isEdit){
                                echo '<form method="post" action="'.Url::to(['rubric-edit', 'id' => $model->id, 'sub' => $programSub ? $programSub->id : null, 'pr' => $r->id, 'rubric' => $r->rubric_id]).'">';
                                echo Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken);
                                echo '<div class="row g-2 align-items-center">';
                                echo '<div class="col-md-8">';
                                echo '<input type="text" name="rubric_name" class="form-control" value="'.Html::encode($r->rubric->rubric_name).'" />';
                                echo '<textarea name="rubric_description" class="form-control mt-2 rubric-richtext" rows="6" placeholder="Rubric description">'.Html::encode(html_entity_decode((string)$r->rubric->rubric_description, ENT_QUOTES | ENT_HTML5, 'UTF-8')).'</textarea>';

                                echo '<div class="mt-3">';
                                echo '<div class="mb-2"><b>Judging Sessions</b></div>';
                                echo '<div class="table-responsive">';
                                echo '<table class="table table-sm" data-rubric-session-table="'.(int)$r->rubric_id.'">';
                                echo '<thead><tr><th>Name</th><th>Start</th><th>End</th><th>Location</th><th>Mode</th><th>Del</th></tr></thead>';
                                echo '<tbody>';

                                $sessions = $r->rubric->judgingSessions;
                                $modeList = \app\models\RubricJudgingSession::listMode();
                                if($sessions){
                                    foreach($sessions as $s){
                                        $startVal = $s->datetime_start ? date('Y-m-d\TH:i', strtotime($s->datetime_start)) : '';
                                        $endVal = $s->datetime_end ? date('Y-m-d\TH:i', strtotime($s->datetime_end)) : '';
                                        echo '<tr>';
                                        echo '<td>';
                                        echo Html::hiddenInput('session_id[]', (int)$s->id);
                                        echo '<input type="text" name="session_name[]" class="form-control form-control-sm" value="'.Html::encode($s->session_name).'" />';
                                        echo '</td>';
                                        echo '<td><input type="datetime-local" name="datetime_start[]" class="form-control form-control-sm" value="'.Html::encode($startVal).'" /></td>';
                                        echo '<td><input type="datetime-local" name="datetime_end[]" class="form-control form-control-sm" value="'.Html::encode($endVal).'" /></td>';
                                        echo '<td><input type="text" name="location[]" class="form-control form-control-sm" value="'.Html::encode($s->location).'" /></td>';
                                        echo '<td><select name="mode[]" class="form-select form-select-sm">';
                                        foreach($modeList as $k => $v){
                                            $sel = ((int)$s->mode === (int)$k) ? 'selected' : '';
                                            echo '<option value="'.(int)$k.'" '.$sel.'>'.Html::encode($v).'</option>';
                                        }
                                        echo '</select></td>';
                                        echo '<td class="text-center"><input type="checkbox" name="delete_session[]" value="'.(int)$s->id.'" /></td>';
                                        echo '</tr>';
                                    }
                                }

                                echo '<tr data-new-session-row="1">';
                                echo '<td>';
                                echo Html::hiddenInput('session_id[]', '');
                                echo '<input type="text" name="session_name[]" class="form-control form-control-sm" placeholder="Session 1" />';
                                echo '</td>';
                                echo '<td><input type="datetime-local" name="datetime_start[]" class="form-control form-control-sm" /></td>';
                                echo '<td><input type="datetime-local" name="datetime_end[]" class="form-control form-control-sm" /></td>';
                                echo '<td><input type="text" name="location[]" class="form-control form-control-sm" /></td>';
                                echo '<td><select name="mode[]" class="form-select form-select-sm">';
                                foreach($modeList as $k => $v){
                                    echo '<option value="'.(int)$k.'">'.Html::encode($v).'</option>';
                                }
                                echo '</select></td>';
                                echo '<td class="text-center"><input type="checkbox" name="delete_session[]" value="" /></td>';
                                echo '</tr>';

                                echo '</tbody>';
                                echo '</table>';
                                echo '</div>';
                                echo '<button type="button" class="btn btn-outline-secondary btn-sm" data-add-session="'.(int)$r->rubric_id.'">Add Session</button>';
                                echo '</div>';

                                echo '</div>';
                                echo '<div class="col-md-4">';
                                echo '<button type="submit" class="btn btn-primary btn-sm">Save</button> ';
                                echo Html::a('Cancel', ['rubrics', 'id' => $model->id, 'sub' => $programSub ? $programSub->id : null], ['class' => 'btn btn-secondary btn-sm']);
                                echo '</div>';
                                echo '</div>';
                                echo '</form>';
                            }else{
                                echo Html::encode($r->rubric->rubric_name);

                                $desc = (string)$r->rubric->rubric_description;
                                if(trim($desc) !== ''){
                                    $descHtml = html_entity_decode($desc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                    echo '<div class="mt-2 small text-muted border-start ps-2">'
                                        . \yii\helpers\HtmlPurifier::process($descHtml)
                                        . '</div>';
                                }

                                $sessions = $r->rubric->judgingSessions;
                                if($sessions){
                                    $modeList = \app\models\RubricJudgingSession::listMode();
                                    echo '<div class="mt-2">';
                                    echo '<div class="text-muted" style="font-size: 0.9em;"><b>Sessions</b></div>';
                                    echo '<ul class="mb-0" style="font-size: 0.9em;">';
                                    foreach($sessions as $s){
                                        $modeText = $modeList[(int)$s->mode] ?? $s->mode;
                                        $range = '';
                                        if($s->datetime_start && $s->datetime_end){
                                            $startTs = strtotime($s->datetime_start);
                                            $endTs = strtotime($s->datetime_end);
                                            if(date('Y-m-d', $startTs) === date('Y-m-d', $endTs)){
                                                $range = date('d M Y H:i', $startTs) . ' - ' . date('H:i', $endTs);
                                            }else{
                                                $range = date('d M Y H:i', $startTs) . ' - ' . date('d M Y H:i', $endTs);
                                            }
                                        }else if($s->datetime_start){
                                            $range = date('d M Y H:i', strtotime($s->datetime_start));
                                        }else if($s->datetime_end){
                                            $range = date('d M Y H:i', strtotime($s->datetime_end));
                                        }
                                        $meta = [];
                                        if($range){ $meta[] = $range; }
                                        if($s->location){ $meta[] = $s->location; }
                                        if($modeText){ $meta[] = $modeText; }
                                        $metaText = $meta ? (' (' . Html::encode(implode(' | ', $meta)) . ')') : '';
                                        echo '<li>' . Html::encode($s->session_name) . $metaText . '</li>';
                                    }
                                    echo '</ul>';
                                    echo '</div>';
                                }
                            }

                            echo '</td>';
                            echo '<td>';

                            echo Html::a('View', ['view-rubric', 'id' => $r->rubric_id], ['class' => 'btn btn-primary btn-sm']);
                            echo ' ' . Html::a('Edit Rubric', ['view-rubric', 'id' => $r->rubric_id, 'edit' => 1], ['class' => 'btn btn-secondary btn-sm']);
                            if(!$isEdit){
                                echo ' ' . Html::a('Edit', ['rubrics', 'id' => $model->id, 'sub' => $programSub ? $programSub->id : null, $editKey => 1], ['class' => 'btn btn-warning btn-sm']);
                                echo ' <form method="post" action="'.Url::to(['rubric-delete', 'id' => $model->id, 'sub' => $programSub ? $programSub->id : null, 'pr' => $r->id]).'" style="display:inline;">';
                                echo Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken);
                                echo '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Delete this rubric from this program?\')">Delete</button>';
                                echo '</form>';
                            }

                            echo '</td>';
                            echo '</tr>';
                            $i++;
                        }
                    }
                    ?> 
                </tbody>
            </table>

    

</div>
            </div>
        </div>

<?php
$this->registerJs(<<<JS
if(window.tinymce){
  tinymce.remove('.rubric-richtext');
  tinymce.init({
    selector: '.rubric-richtext',
    menubar: false,
    plugins: [
      'advlist', 'autolink', 'lists', 'link', 'charmap',
      'searchreplace', 'visualblocks', 'code', 'fullscreen',
      'paste'
    ],
    toolbar: 'undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
    height: 200,
    branding: false
  });
}

document.addEventListener('click', function(e){
  var btn = e.target.closest('[data-add-session]');
  if(!btn){ return; }
  var rubricId = btn.getAttribute('data-add-session');
  var table = document.querySelector('table[data-rubric-session-table="' + rubricId + '"]');
  if(!table){ return; }

  var tbody = table.querySelector('tbody');
  var index = tbody.querySelectorAll('tr').length + 1;

  var row = document.createElement('tr');
  row.setAttribute('data-new-session-row', '1');
  row.innerHTML = ''
    + '<td>'
    +   '<input type="hidden" name="session_id[]" value="">'
    +   '<input type="text" name="session_name[]" class="form-control form-control-sm" placeholder="Session ' + index + '">' 
    + '</td>'
    + '<td><input type="datetime-local" name="datetime_start[]" class="form-control form-control-sm"></td>'
    + '<td><input type="datetime-local" name="datetime_end[]" class="form-control form-control-sm"></td>'
    + '<td><input type="text" name="location[]" class="form-control form-control-sm"></td>'
    + '<td>'
    +   '<select name="mode[]" class="form-select form-select-sm">'
    +     '<option value="1">Physical</option>'
    +     '<option value="2">Online</option>'
    +   '</select>'
    + '</td>'
    + '<td class="text-center"><input type="checkbox" name="delete_session[]" value=""></td>';
  tbody.appendChild(row);
});
JS);
?>



    </section>

