<?php

/** @var \app\models\Program $model */
/** @var \app\models\ProgramRegistration $register */
/** @var bool $edit */

 $publicMode = $publicMode ?? false;
 $storageEntry = $storageEntry ?? false;

$this->title = 'Registration - ' . $model->program_name;

$arr_fields = $register->getProgramFields($register->program_id);
?>
<div class="d-flex justify-content-center py-4">
    <a href="index.html" class="logo d-flex align-items-center w-auto">
        <span class="d-none d-lg-block"><?=$model->program_name?></span>
    </a>
</div>

<?=$this->render('_view_register', [
    'register' => $register,
    'arr_fields' => $arr_fields,
    'edit' => $edit ?? false,
    'publicMode' => $publicMode,
    'storageEntry' => $storageEntry,
]);
?>
