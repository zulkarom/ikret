<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\UserRole $role */
/** @var app\models\Program $program */
/** @var app\models\ProgramSub|null $programSub */
/** @var array $dashboardStats */

$title = $program->program_name;
if($programSub){
    $title .= ' / ' . $programSub->sub_name;
}

$this->title = 'Manager Dashboard - ' . $title;

$id = (int)$program->id;
$sub = $programSub ? (int)$programSub->id : null;
$dashboardStats = $dashboardStats ?? [];

$formatDate = static function($value){
    if(empty($value)){
        return 'Not set';
    }
    $time = strtotime($value);
    return $time ? date('d M Y', $time) : 'Not set';
};

$programLevelCards = [
    [
        'title' => 'Program Info',
        'url' => Url::to(['program/info', 'id' => $id, 'sub' => $sub]),
        'icon' => 'bi bi-sliders2',
        'accent' => 'navy',
        'description' => 'Control schedule, description, and registration availability.',
        'stats' => [
            ['label' => 'Status', 'value' => $dashboardStats['registration_status'] ?? 'Open'],
            ['label' => 'Start', 'value' => $formatDate($dashboardStats['date_start'] ?? null)],
            ['label' => 'End', 'value' => $formatDate($dashboardStats['date_end'] ?? null)],
        ],
    ],
    [
        'title' => 'Registration Fields',
        'url' => Url::to(['program/register-fields', 'id' => $id, 'sub' => $sub]),
        'icon' => 'bi bi-ui-checks-grid',
        'accent' => 'teal',
        'description' => 'Manage which fields appear in the registration form.',
        'stats' => [
            ['label' => 'Enabled', 'value' => (string)($dashboardStats['fields_enabled_count'] ?? 0)],
            ['label' => 'Required', 'value' => (string)($dashboardStats['fields_required_count'] ?? 0)],
            ['label' => 'Entries', 'value' => (string)($dashboardStats['registrations_total'] ?? 0)],
        ],
    ],
    [
        'title' => 'Import Participant',
        'url' => Url::to(['program-registration/import-participants', 'id' => $id, 'sub' => $sub]),
        'icon' => 'bi bi-file-earmark-arrow-up',
        'accent' => 'amber',
        'description' => 'Upload participant records by CSV using the current enabled field setup.',
        'stats' => [
            ['label' => 'Enabled', 'value' => (string)($dashboardStats['fields_enabled_count'] ?? 0)],
            ['label' => 'Scope', 'value' => $programSub ? 'Sub' : 'Program'],
            ['label' => 'Entries', 'value' => (string)($dashboardStats['registrations_total'] ?? 0)],
        ],
    ],
];

$subProgramLevelCards = [];

if((int)$program->program_type === 1){
    $subProgramLevelCards[] = [
        'title' => 'Participants & Juries Assignment',
        'url' => Url::to(['program-registration/manager', 'id' => $id, 'sub' => $sub]),
        'icon' => 'bi bi-people-fill',
        'accent' => 'indigo',
        'description' => 'Track participants and manage jury assignment coverage.',
        'stats' => [
            ['label' => 'Entries', 'value' => (string)($dashboardStats['registrations_total'] ?? 0)],
            ['label' => 'Assigned', 'value' => (string)($dashboardStats['assignments_total'] ?? 0)],
            ['label' => 'Done', 'value' => (string)($dashboardStats['assignments_complete'] ?? 0)],
        ],
    ];
    $subProgramLevelCards[] = [
        'title' => 'Jury Applications',
        'url' => Url::to(['program-registration/manager-jury-applications', 'id' => $id, 'sub' => $sub]),
        'icon' => 'bi bi-person-lines-fill',
        'accent' => 'teal',
        'description' => 'Review and track incoming jury applications.',
        'stats' => [
            ['label' => 'Total', 'value' => (string)($dashboardStats['jury_applications_total'] ?? 0)],
            ['label' => 'New', 'value' => (string)($dashboardStats['jury_applications_new'] ?? 0)],
            ['label' => 'Approved', 'value' => (string)($dashboardStats['jury_applications_approved'] ?? 0)],
        ],
    ];
    $subProgramLevelCards[] = [
        'title' => 'Result By Assignments',
        'url' => Url::to(['program-registration/jury-result', 'id' => $id, 'sub' => $sub]),
        'icon' => 'bi bi-bar-chart-line-fill',
        'accent' => 'amber',
        'description' => 'Review assignment outcomes and judging progress.',
        'stats' => [
            ['label' => 'Rubrics', 'value' => (string)($dashboardStats['rubrics_count'] ?? 0)],
            ['label' => 'Completed', 'value' => (string)($dashboardStats['assignments_complete'] ?? 0)],
            ['label' => 'Pending', 'value' => (string)(($dashboardStats['assignments_total'] ?? 0) - ($dashboardStats['assignments_complete'] ?? 0))],
        ],
    ];
    $subProgramLevelCards[] = [
        'title' => 'Analysis & Achievement',
        'url' => Url::to(['program-registration/manager-analysis', 'id' => $id, 'sub' => $sub]),
        'icon' => 'bi bi-trophy-fill',
        'accent' => 'rose',
        'description' => 'Analyze scores and distribute awards or achievements.',
        'stats' => [
            ['label' => 'Registered', 'value' => (string)($dashboardStats['registrations_registered'] ?? 0)],
            ['label' => 'Achievements', 'value' => (string)($dashboardStats['achievements_count'] ?? 0)],
            ['label' => 'Awarded', 'value' => (string)($dashboardStats['awarded_count'] ?? 0)],
        ],
    ];
    $subProgramLevelCards[] = [
        'title' => 'Certificates',
        'url' => Url::to(['program-registration/manager-view-certs', 'id' => $id, 'sub' => $sub]),
        'icon' => 'bi bi-patch-check-fill',
        'accent' => 'cyan',
        'description' => 'Generate and review participant certificate output.',
        'stats' => [
            ['label' => 'Eligible', 'value' => (string)($dashboardStats['registrations_total'] ?? 0)],
            ['label' => 'Awarded', 'value' => (string)($dashboardStats['awarded_count'] ?? 0)],
            ['label' => 'Complete', 'value' => (string)($dashboardStats['registrations_complete'] ?? 0)],
        ],
    ];
    $subProgramLevelCards[] = [
        'title' => 'Rubrics',
        'url' => Url::to(['program/rubrics', 'id' => $id, 'sub' => $sub]),
        'icon' => 'bi bi-journal-check',
        'accent' => 'violet',
        'description' => 'Maintain rubric structures used for this judging flow.',
        'stats' => [
            ['label' => 'Linked', 'value' => (string)($dashboardStats['rubrics_count'] ?? 0)],
            ['label' => 'Assigned', 'value' => (string)($dashboardStats['assignments_total'] ?? 0)],
            ['label' => 'Done', 'value' => (string)($dashboardStats['assignments_complete'] ?? 0)],
        ],
    ];
    $subProgramLevelCards[] = [
        'title' => 'Achievements',
        'url' => Url::to(['program/achievement', 'id' => $id, 'sub' => $sub]),
        'icon' => 'bi bi-award-fill',
        'accent' => 'emerald',
        'description' => 'Manage achievement labels and monitor their usage.',
        'stats' => [
            ['label' => 'Defined', 'value' => (string)($dashboardStats['achievements_count'] ?? 0)],
            ['label' => 'Awarded', 'value' => (string)($dashboardStats['awarded_count'] ?? 0)],
            ['label' => 'Entries', 'value' => (string)($dashboardStats['registrations_total'] ?? 0)],
        ],
    ];
}else{
    $subProgramLevelCards[] = [
        'title' => 'Participants & Certificates',
        'url' => Url::to(['program-registration/manager-session', 'id' => $id, 'sub' => $sub]),
        'icon' => 'bi bi-people-fill',
        'accent' => 'indigo',
        'description' => 'Review participants and manage certificate issuance.',
        'stats' => [
            ['label' => 'Entries', 'value' => (string)($dashboardStats['registrations_total'] ?? 0)],
            ['label' => 'Registered', 'value' => (string)($dashboardStats['registrations_registered'] ?? 0)],
            ['label' => 'Complete', 'value' => (string)($dashboardStats['registrations_complete'] ?? 0)],
        ],
    ];
}

$this->registerCss(<<<CSS
.dashboard-hero {
    background: linear-gradient(135deg, #102542 0%, #1a4b74 52%, #57b3c2 100%);
    border-radius: 22px;
    color: #fff;
    padding: 1.5rem;
    box-shadow: 0 18px 40px rgba(16, 37, 66, 0.18);
}
.dashboard-hero__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .4rem .8rem;
    border-radius: 999px;
    background: rgba(255,255,255,.14);
    color: rgba(255,255,255,.9);
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: .08em;
}
.dashboard-hero__stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: .85rem;
    margin-top: 1.2rem;
}
.dashboard-hero__stat {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.14);
    border-radius: 16px;
    padding: .9rem 1rem;
}
.dashboard-hero__stat-value {
    display: block;
    font-size: 1.35rem;
    font-weight: 700;
    line-height: 1.1;
}
.dashboard-hero__stat-label {
    color: rgba(255,255,255,.76);
    font-size: .82rem;
}
.dashboard-section {
    margin-top: 1.6rem;
}
.dashboard-section__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
}
.dashboard-section__title {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: #17324d;
    text-transform: uppercase;
    letter-spacing: .06em;
}
.dashboard-section__meta {
    color: #6e8297;
    font-size: .9rem;
}
.dashboard-card {
    border: 0;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 14px 32px rgba(18, 40, 67, 0.08);
    background: #fff;
    height: 100%;
}
.dashboard-card__inner {
    position: relative;
    height: 100%;
    padding: 1.3rem;
    border-top: 5px solid var(--accent);
    background:
        radial-gradient(circle at top right, var(--accent-soft) 0, transparent 42%),
        linear-gradient(180deg, #fff 0%, #f7fafc 100%);
}
.dashboard-card__icon {
    width: 46px;
    height: 46px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    background: var(--accent-soft);
    color: var(--accent);
    font-size: 1.2rem;
}
.dashboard-card__title {
    margin: 1rem 0 .35rem;
    color: #142b45;
    font-size: 1.08rem;
    font-weight: 700;
}
.dashboard-card__text {
    color: #5d7389;
    min-height: 44px;
    margin-bottom: 1rem;
}
.dashboard-card__stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: .65rem;
    margin-bottom: 1rem;
}
.dashboard-card__stat {
    background: #fff;
    border: 1px solid #e3ebf3;
    border-radius: 14px;
    padding: .7rem .75rem;
}
.dashboard-card__stat-value {
    display: block;
    color: #17324d;
    font-size: 1rem;
    font-weight: 700;
    line-height: 1.15;
}
.dashboard-card__stat-label {
    display: block;
    color: #70859b;
    font-size: .77rem;
    margin-top: .2rem;
}
.dashboard-card__action {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    background: var(--accent);
    border-color: var(--accent);
}
.dashboard-card__action:hover,
.dashboard-card__action:focus {
    background: var(--accent-dark);
    border-color: var(--accent-dark);
}
.accent-navy { --accent: #184e77; --accent-dark: #103a5a; --accent-soft: rgba(24, 78, 119, .12); }
.accent-teal { --accent: #198f8a; --accent-dark: #126b67; --accent-soft: rgba(25, 143, 138, .12); }
.accent-indigo { --accent: #355cdb; --accent-dark: #2745a3; --accent-soft: rgba(53, 92, 219, .12); }
.accent-amber { --accent: #c98a10; --accent-dark: #9a6908; --accent-soft: rgba(201, 138, 16, .14); }
.accent-rose { --accent: #c2497d; --accent-dark: #95355f; --accent-soft: rgba(194, 73, 125, .12); }
.accent-cyan { --accent: #0e88a7; --accent-dark: #0a657c; --accent-soft: rgba(14, 136, 167, .12); }
.accent-violet { --accent: #6a5ad1; --accent-dark: #5043a5; --accent-soft: rgba(106, 90, 209, .12); }
.accent-emerald { --accent: #1f8a5b; --accent-dark: #176646; --accent-soft: rgba(31, 138, 91, .12); }
@media (max-width: 767.98px) {
    .dashboard-card__stats {
        grid-template-columns: 1fr;
    }
}
CSS);

$renderCards = static function($cards){
    foreach($cards as $card){
        $accentClass = 'accent-' . $card['accent'];
        echo '<div class="col-12 col-md-6 col-xl-4 mb-3">';
        echo '<div class="card dashboard-card ' . Html::encode($accentClass) . '">';
        echo '<div class="dashboard-card__inner">';
        echo '<div class="dashboard-card__icon"><i class="' . Html::encode($card['icon']) . '"></i></div>';
        echo '<h5 class="dashboard-card__title">' . Html::encode($card['title']) . '</h5>';
        echo '<div class="dashboard-card__text">' . Html::encode($card['description']) . '</div>';
        echo '<div class="dashboard-card__stats">';
        foreach($card['stats'] as $stat){
            echo '<div class="dashboard-card__stat">';
            echo '<span class="dashboard-card__stat-value">' . Html::encode($stat['value']) . '</span>';
            echo '<span class="dashboard-card__stat-label">' . Html::encode($stat['label']) . '</span>';
            echo '</div>';
        }
        echo '</div>';
        echo Html::a('View <i class="bi bi-arrow-right-short"></i>', $card['url'], ['class' => 'btn btn-primary dashboard-card__action']);
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
};

?>

<div class="pagetitle">
    <h1><?=$this->title?></h1>
</div>

</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="dashboard-hero mb-4">
        <div class="dashboard-hero__eyebrow">
            <i class="bi bi-speedometer2"></i>
            <?= $programSub ? 'Sub Program Dashboard' : 'Program Dashboard' ?>
        </div>
        <h2 class="mt-3 mb-2"><?= Html::encode($title) ?></h2>
        <div class="text-white-50">A cleaner overview for registration, judging, certificates, and setup management.</div>
        <div class="dashboard-hero__stats">
            <div class="dashboard-hero__stat">
                <span class="dashboard-hero__stat-value"><?= (int)($dashboardStats['registrations_total'] ?? 0) ?></span>
                <span class="dashboard-hero__stat-label">Total Entries</span>
            </div>
            <div class="dashboard-hero__stat">
                <span class="dashboard-hero__stat-value"><?= (int)($dashboardStats['assignments_total'] ?? 0) ?></span>
                <span class="dashboard-hero__stat-label">Jury Assignments</span>
            </div>
            <div class="dashboard-hero__stat">
                <span class="dashboard-hero__stat-value"><?= Html::encode($dashboardStats['registration_status'] ?? 'Open') ?></span>
                <span class="dashboard-hero__stat-label">Registration</span>
            </div>
        </div>
    </div>

    <?php if(!$programSub){ ?>
    <div class="dashboard-section">
        <div class="dashboard-section__head">
            <h3 class="dashboard-section__title">Program Level</h3>
            <div class="dashboard-section__meta">Shared settings and registration structure</div>
        </div>
        <div class="row">
            <?php $renderCards($programLevelCards); ?>
        </div>
    </div>
    <?php } ?>

    <div class="dashboard-section">
        <div class="dashboard-section__head">
            <h3 class="dashboard-section__title">Sub Program Level</h3>
            <div class="dashboard-section__meta"><?= $programSub ? 'Current scope: ' . Html::encode($programSub->sub_name) : 'Operational tools for the current program scope' ?></div>
        </div>
        <div class="row">
            <?php $renderCards($subProgramLevelCards); ?>
        </div>
    </div>

</section>
