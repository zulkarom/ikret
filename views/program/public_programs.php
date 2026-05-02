<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

$storageEntry = $storageEntry ?? false;

$this->title = 'Available Programs';
?>
<div class="d-flex justify-content-center py-4">
    <a href="index.html" class="logo d-flex align-items-center w-auto">
        <span class="d-none d-lg-block">Available Programs</span>
    </a>
</div>

<div class="card mb-3">
    <div class="card-header">Programs Open for Registration</div>
    <div class="card-body">
        <?php if(empty($programs)){ ?>
            <div class="alert alert-warning mb-0">No programs are available at the moment.</div>
        <?php }else{ ?>
            <div class="row g-3">
                <?php foreach($programs as $program){ ?>
                    <?php $isClosed = (int)$program->getAttribute('reg_closed') === 1; ?>
                    <div class="col-12">
                        <div class="border rounded p-3 h-100">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
                                <div>
                                    <h5 class="mb-1"><?= Html::encode($program->program_name) ?></h5>
                                    <?php if(!empty($program->program_abbr)){ ?>
                                        <div class="text-muted small mb-2"><?= Html::encode($program->program_abbr) ?></div>
                                    <?php } ?>
                                    <?php if(!empty($program->date_start) || !empty($program->date_end)){ ?>
                                        <div class="small mb-2">
                                            <?php if(!empty($program->date_start)){ ?>
                                                <strong>Start:</strong> <?= date('d/m/Y', strtotime($program->date_start)) ?>
                                            <?php } ?>
                                            <?php if(!empty($program->date_end)){ ?>
                                                <span class="ms-3"><strong>End:</strong> <?= date('d/m/Y', strtotime($program->date_end)) ?></span>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                    <div class="small text-muted">
                                        <?= StringHelper::truncateWords(strip_tags((string)$program->reg_info), 30) ?>
                                    </div>
                                </div>
                                <div class="text-md-end">
                                    <?php if($isClosed){ ?>
                                        <button type="button" class="btn btn-secondary" disabled>Closed</button>
                                    <?php }else{ ?>
                                        <?= Html::a('Register', ['public-register-form', 'id' => $program->id], ['class' => 'btn btn-primary']) ?>
                                    <?php } ?>
                                    <div class="mt-2">
                                        <?= Html::a('Edit', ['public-edit-login', 'id' => $program->id], ['class' => '']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>
