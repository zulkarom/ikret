<?php

namespace app\controllers;

use app\models\CertificateJury;
use app\models\CertificateTemplate;
use app\models\JuryAssign;
use app\models\JuryAssignSearch;
use app\models\JuryApplicationManualCreateForm;
use app\models\JuryApplicationSearch;
use app\models\JuryResultSearch;
use app\models\ManagerAnalysisSearch;
use app\models\ManagerSessionSearch;
use app\models\Member;
use app\models\Model;
use app\models\Mentor;
use app\models\MentorMenteesSearch;
use app\models\ParticipantAchieve;
use app\models\Program;
use app\models\ProgramAchievement;
use app\models\ProgramMethod;
use app\models\ProgramRegField;
use app\models\ProgramRegistration;
use app\models\ProgramRegistrationManagerSearch;
use app\models\ProgramRegistrationSearch;
use app\models\ProgramRubric;
use app\models\ProgramStage;
use app\models\ProgramSub;
use app\models\ProgramWinnerTitle;
use app\models\JuryApplication;
use app\models\JuryRequirement;
use app\models\QuestionnaireAnswer;
use app\models\Rubric;
use app\models\RubricAnswer;
use app\models\Setting;
use app\models\User;
use app\models\UserRole;
use app\models\JuryProfile;
use app\models\RubricJudgingSession;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\models\RubricItem;

/**
 * ProgramRegistrationController implements the CRUD actions for ProgramRegistration model.
 */
class ProgramRegistrationController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all ProgramRegistration models.
     *
     * @return string
     */
    public function actionIndex()
    {
        if(!Yii::$app->user->identity->isAdminRegistration) return false;
        $searchModel = new ProgramRegistrationSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
                'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
        ]);
    }

    public function actionJuryCertPdf($p, $s = null, $u = null){
        if(!$this->ensureCertificatesReleased(true, 3)){
            return $this->render('empty');
        }

        //$u = Yii::$app->user->identity->id;
        if($u){
            $u = $this->findUser($u);
        }else{
            $u = Yii::$app->user->identity;
        }
        if(!$this->canAccessDoc($u, $p,$s)) return false;

        $pdf = new CertificateJury;
        
        $assign = $this->findAssignmentByProgram($u, $p, $s);
        $pdf->template = CertificateTemplate::findOne(3);
        $pdf->model = $assign;
        $pdf->generatePdf();
        exit;
    }

    private function canAccessDoc($u, $p, $s){
        if(Yii::$app->user->identity->isManager || Yii::$app->user->identity->isAdmin){
            return true;
        }else{
            $role = $this->findAssignmentByProgram($u, $p, $s);
            if($role->user_id == Yii::$app->user->identity->id){
                return true;
            }
        }
        return false;
    }

    private function ensureCertificatesReleased($allowPrivileged = true, $templateId = null)
    {
        $certificateName = $templateId === null ? 'Certificates and awards' : CertificateTemplate::displayName($templateId, 'Certificate');

        if($allowPrivileged && !Yii::$app->user->isGuest && (Yii::$app->user->identity->isManager || Yii::$app->user->identity->isAdmin)){
            return true;
        }

        if(!Setting::areCertificatesReleased()){
            $releaseDate = Setting::certificateReleaseText();
            $message = $releaseDate
                ? $certificateName . ' will be released from ' . $releaseDate . '.'
                : $certificateName . ' has not been published.';
            Yii::$app->session->addFlash('info', $message);

            return false;
        }

        if($templateId !== null && !CertificateTemplate::isPublished($templateId)){
            Yii::$app->session->addFlash('info', $certificateName . ' has not been published.');
            return false;
        }

        return true;
    }

    public function actionJuryCertPage($u=null)
    {
        $admin = $u && Yii::$app->user->identity->isManager;
        if(!$this->ensureCertificatesReleased($admin, 3)){
            return $this->render("empty");
        }
        if($u){
            $user = $this->findUser($u);
        }else{
            $user = Yii::$app->user->identity;
        }

        if(!$user->isJury) return false;
        //cari unique program/sub
        //pastikan semua assignemnt siap
        $list = JuryAssign::find()
        ->where(['user_id' => $user->id])
        ->all();
        if($list){
            foreach($list as $a){
                if($a->status != 20){
                    Yii::$app->session->addFlash('error', "Sorry, you need to finish all the assignments.");
                    return $this->render('empty');
                }
            }
            //kena dapatkan unique program
            $programs = JuryAssign::find()->alias('a')
            ->select('r.program_id, r.program_sub')
            ->joinWith(['registration r'])
            ->where(['a.user_id' => $user->id])
            ->groupBy('r.program_id, r.program_sub')
            ->all();

        }else{
            //cari & test ni dulu
            Yii::$app->session->addFlash('error', "Sorry, you dont't have any assignment.");
            return $this->render('empty');
        }


        return $this->render('jury-cert-page', [
            'list' => $list,
            'programs' => $programs,
            'user' => $user
        ]);
    }

    public function actionMentorMentees($u = null)
    {
        $admin = $u && Yii::$app->user->identity->isManager;
        if(!$this->ensureCertificatesReleased($admin)){
            return $this->render("empty");
        }
        if($u){
            $user = $this->findUser($u);
        }else{
            $user = Yii::$app->user->identity;
        }


        if(!$user->isMentor) return false;
        

        $searchModel = new MentorMenteesSearch(['user' => $user]);
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('mentor-mentees', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'user' => $user
        ]);
    }

    public function actionManagerViewCerts($id, $sub = null)
    {
        if(!Yii::$app->user->identity->isManager) return false;
        $role = $this->findManagerRole($id, $sub);

        if(!$role){
            throw new ForbiddenHttpException('No access');
        }

        $programSub = null;
        $program = $role->program;

        if($role->program->has_sub == 1){
            if($sub){
                $programSub = $role->programSub;
            }else{
                throw new NotFoundHttpException('Please provide sub program.');
            }
        }
        
        $searchModel = new ProgramRegistrationManagerSearch();
            $searchModel->program_id = $role->program_id;
            $searchModel->program_sub = $sub;
            $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('manager-view-certs', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'role' => $role,
            'programSub' => $programSub

        ]);
    }

    public function actionJuryAssignment()
    {
        if(!Yii::$app->user->identity->isJury) return false;

        $searchModel = new JuryAssignSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('jury-assignment', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionJuryDelete($id, $p, $s = null){
        if(!Yii::$app->user->identity->isManager) return false;

        $assign = $this->findAssignment($id);
        $reg = $assign->registration;

        $role = $this->findManagerRole($p, $s);

        if($role && $role->program){
            
            if($assign->status == 0){
                if($assign->delete()){
                    Yii::$app->session->addFlash('success', "Assignment Deleted");
                    
                }
            }
        }

        return $this->redirect(['manager', 'id' => $p, 'sub' => $s]);
    }

    public function actionJuryJudge($id){
        date_default_timezone_set("Asia/Kuala_Lumpur");
        $assign = $this->findAssignment($id);
        $register = $assign->registration;
        //kita ubah status kepada judging once buka
        if($assign->status == 0){
            $assign->status = 10;
            $assign->save();
        }
        
        //kita create terus klu takde
        //mapping one to one mcm xkena, tp just proceeds
        $ada = RubricAnswer::findOne([
            'rubric_id' => $assign->rubric_id,
            'assignment_id' => $assign->id
        ]);
        if($ada){
            $model = $ada;
        }else{
            $model = new RubricAnswer([
            'rubric_id' => $assign->rubric_id,
            'assignment_id' => $assign->id,
            'created_at' => new Expression('NOW()'),
            ]);
            $model->save();
        }

        if($assign->status <= 10 && $this->request->isPost && $model->load($this->request->post())) {
            
            $action = $this->request->post('action');
            //print_r($this->request->post());

            $nullify = $this->request->post('nullify');
            $assign->is_nullified = $nullify == 1 ? 1 : 0;
            $assign->reason_nullified = $this->request->post('reason_nullified');
            $valid = true;
            if($assign->is_nullified == 1 && !$assign->reason_nullified){
                Yii::$app->session->addFlash('error', "You need to state the reason of nullification.");
                $valid = false;
            }

            //echo $nullify;die();
            
            $model->updated_at = new Expression('NOW()');
            if($action == 'submit' && $valid){
                if($model->isComplete || $assign->is_nullified == 1){
                    $model->submitted_at = new Expression('NOW()');
                    $transaction = Yii::$app->db->beginTransaction();

                    try {
                        if($model->save()){
                            $assign->status = 20;

                            //put score in jury assign
                            if($assign->is_nullified == 1){
                                $assign->score = 0;
                            }else{
                                $assign->score = $model->scoreValue;
                            }
                            
                            if($assign->save()){
                                //calc average put score in registration
                                $register->setScoreAndAward();
                                $register->save();
                            }else{
                                Yii::$app->session->addFlash('error', "Failed to update status");
                            }
                        }
                        
                        
                        $transaction->commit();

                        Yii::$app->session->addFlash('success', "Thank you, you have completed the judging session for this participant.");
                        return $this->refresh();
                    }catch (\Exception $e){
                        $transaction->rollBack();
                        Yii::$app->session->addFlash('error', $e->getMessage());
                    }
                }else{
                    Yii::$app->session->addFlash('error', "You need to complete all first before submitting.");
                }
            }else{

                //put score in jury assign
                if($assign->is_nullified == 1){
                    $assign->score = 0;
                }else{
                    $assign->score = $model->scoreValue;
                }
                if($assign->save()){
                    if($model->save()){
                    Yii::$app->session->addFlash('success', "Data Updated");
                    return $this->refresh();
                }
                }
                
            }
        }

        return $this->render('jury-judge', [
            'assign' => $assign,
            'model' => $model,
            'plain' => false,
            'title' => 'Judging Session',
            'write' => true,
        ]);
    }

    /**
     * Displays a single ProgramRegistration model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
            'canDelete' => !$this->hasJuryAssignments($model->id),
        ]);
    }

    public function actionViewResult($id)
    {
        $assign = $this->findAssignment($id);
        $model = RubricAnswer::findOne([
            'rubric_id' => $assign->rubric_id,
            'assignment_id' => $assign->id
        ]);
        if(!$model){
            $model = new RubricAnswer([
                'rubric_id' => $assign->rubric_id,
                'assignment_id' => $assign->id,
            ]);
        }

        return $this->render('jury-judge', [
            'assign' => $assign,
            'model' => $model,
            'plain' => false,
            'title' => 'View Result',
            'write' => false,
        ]);
    }

    public function actionReturnResultJudging($id)
    {
        if(!Yii::$app->user->identity->isAdminJury) return false;
        if(!$this->request->isPost){
            throw new \yii\web\MethodNotAllowedHttpException('Method Not Allowed.');
        }

        $assign = $this->findAssignment($id);
        if((int)$assign->status !== 20){
            Yii::$app->session->addFlash('info', 'This result is already open for judging.');
            return $this->redirect(['view-result', 'id' => $assign->id]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try{
            $assign->status = 10;
            $assign->updated_at = time();
            $assign->save(false, ['status', 'updated_at']);

            $answer = RubricAnswer::findOne([
                'rubric_id' => $assign->rubric_id,
                'assignment_id' => $assign->id,
            ]);
            if($answer){
                $answer->submitted_at = null;
                $answer->save(false, ['submitted_at']);
            }

            if($assign->registration){
                $assign->registration->setScoreAndAward();
                $assign->registration->save(false, ['score', 'award']);
            }

            $transaction->commit();
            Yii::$app->session->addFlash('success', 'Result returned as Judging.');
        }catch(\Throwable $e){
            $transaction->rollBack();
            Yii::$app->session->addFlash('error', $e->getMessage());
        }

        return $this->redirect(['view-result', 'id' => $assign->id]);
    }

    public function actionMarkResultNotNullified($id)
    {
        if(!Yii::$app->user->identity->isAdminJury) return false;
        if(!$this->request->isPost){
            throw new \yii\web\MethodNotAllowedHttpException('Method Not Allowed.');
        }

        $assign = $this->findAssignment($id);
        if((int)$assign->is_nullified !== 1){
            Yii::$app->session->addFlash('info', 'This result is already not nullified.');
            return $this->redirect(['view-result', 'id' => $assign->id]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try{
            $answer = RubricAnswer::findOne([
                'rubric_id' => $assign->rubric_id,
                'assignment_id' => $assign->id,
            ]);

            $assign->is_nullified = 0;
            $assign->reason_nullified = null;
            if($answer && $answer->isComplete){
                $assign->status = 20;
                $assign->score = $answer->scoreValue;
                if(!$answer->submitted_at){
                    $answer->submitted_at = new Expression('NOW()');
                    $answer->save(false, ['submitted_at']);
                }
            }else{
                $assign->status = 10;
                $assign->score = null;
                if($answer){
                    $answer->submitted_at = null;
                    $answer->save(false, ['submitted_at']);
                }
            }
            $assign->updated_at = time();
            $assign->save(false, ['status', 'is_nullified', 'reason_nullified', 'score', 'updated_at']);

            if($assign->registration){
                $assign->registration->setScoreAndAward();
                $assign->registration->save(false, ['score', 'award']);
            }

            $transaction->commit();
            if($answer && $answer->isComplete){
                Yii::$app->session->addFlash('success', 'Result marked as not nullified and kept as Complete.');
            }else{
                Yii::$app->session->addFlash('warning', 'Result marked as not nullified, but returned to Judging because required answers are incomplete.');
            }
        }catch(\Throwable $e){
            $transaction->rollBack();
            Yii::$app->session->addFlash('error', $e->getMessage());
        }

        return $this->redirect(['view-result', 'id' => $assign->id]);
    }

    public function actionAchieveDelete($id){
        if(!Yii::$app->user->identity->isManager) return false;
        $model = $this->findAchievement($id);
        $reg = $model->registration->id;
        $sub = $model->registration->program_sub;
        if($model->delete()){
            Yii::$app->session->addFlash('success', "Achievement Deleted");
        }

        return $this->redirect(['manager-view', 'id' => $reg, 'sub' => $sub]);

    }

    public function actionManagerView($id, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;
        $model = $this->findModel($id);
        $role = $this->findManagerRole($model->program_id, $sub);

        if(!$role){
            return;
        }

        $programSub = null;
        $program = $role->program;
        if($model->program->has_sub == 1){
            if($sub){
                $programSub = $role->programSub;
            }else{
                throw new NotFoundHttpException('Please provide sub program.');
            }
        }



        return $this->render('manager-view', [
            'model' => $model,

        ]);
    }

    public function actionManagerAward($id, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;
        $model = $this->findModel($id);
        $role = $this->findManagerRole($model->program_id, $sub);

        if(!$role){
            return;
        }

        $programSub = null;
        $program = $role->program;
        if($model->program->has_sub == 1){
            if($sub){
                $programSub = $role->programSub;
            }else{
                throw new NotFoundHttpException('Please provide sub program.');
            }
        }

        if($programSub){
            $achievement = ProgramAchievement::find()
            ->where(['program_id' => $program->id, 'program_sub' => $sub])->all();
        }else{
            $achievement = ProgramAchievement::find()->where(['program_id' => $program->id])->all();
        }
        $list = ArrayHelper::map($achievement, 'id', 'name');


        $achieve = new ParticipantAchieve();
        $achieve->program_reg_id = $id;
        $post = $this->request->post();
        $action = (string)($post['action_type'] ?? '');
        if ($this->request->isPost && $action === 'achievement-add' && $achieve->load($post)) {
            $exists = ParticipantAchieve::find()->where([
                'program_reg_id' => (int)$achieve->program_reg_id,
                'achieve_id' => (int)$achieve->achieve_id,
            ])->exists();
            if($exists){
                Yii::$app->session->addFlash('error', 'This achievement has already been assigned to this participant.');
                return $this->refresh();
            }
            $achieve->achieved_at = time();
            if($achieve->save()){
                Yii::$app->session->addFlash('success', "Achievement Added");
                return $this->refresh();
            }
        }

        if($this->request->isPost && $action === 'achievement-title-update' && $this->hasParticipantAchieveWinnerTitleColumn()){
            $selectedTitles = $post['achievement_winner_title'] ?? [];
            $conflicts = [];
            if($selectedTitles){
                foreach($selectedTitles as $participantAchieveId => $winnerTitleId){
                    $participantAchieve = ParticipantAchieve::findOne([
                        'id' => (int)$participantAchieveId,
                        'program_reg_id' => (int)$model->id,
                    ]);
                    if(!$participantAchieve){
                        continue;
                    }

                    $winnerTitleId = (int)$winnerTitleId;
                    if($winnerTitleId === 0){
                        $participantAchieve->winner_title_id = null;
                    }else{
                        $winnerTitle = ProgramWinnerTitle::find()
                            ->where(['id' => $winnerTitleId, 'achievement_id' => (int)$participantAchieve->achieve_id])
                            ->one();
                        $winnerTitleId = $winnerTitle ? (int)$winnerTitle->id : 0;
                        if($winnerTitleId > 0){
                            $existing = ParticipantAchieve::find()
                                ->where([
                                    'achieve_id' => (int)$participantAchieve->achieve_id,
                                    'winner_title_id' => $winnerTitleId,
                                ])
                                ->andWhere(['<>', 'id', (int)$participantAchieve->id])
                                ->one();
                            if($existing){
                                $existingParticipant = $existing->registration ? $existing->registration->participantText : 'another participant';
                                $titleName = $winnerTitle && trim((string)$winnerTitle->title_name) !== ''
                                    ? trim((string)$winnerTitle->title_name)
                                    : ('Winner ' . ($winnerTitle ? (int)$winnerTitle->winner_order : ''));
                                $conflicts[] = $titleName . ' already assigned to ' . $existingParticipant . '.';
                                continue;
                            }
                            $participantAchieve->winner_title_id = $winnerTitleId;
                        }else{
                            $participantAchieve->winner_title_id = null;
                        }
                    }
                    $participantAchieve->save(false, ['winner_title_id']);
                }
            }

            if($conflicts){
                Yii::$app->session->addFlash('error', implode("\n", array_unique($conflicts)));
            }else{
                Yii::$app->session->addFlash('success', "Winner title updated");
            }
            return $this->refresh();
        }

        if ($this->request->isPost && $model->load($post)) {
            if($model->save()){
                Yii::$app->session->addFlash('success', "Medal updated");
                return $this->refresh();
            }
        }

        $hasWinnerTitleSelection = $this->hasParticipantAchieveWinnerTitleColumn()
            && $this->hasProgramWinnerTitleAchievementColumn();
        $winnerTitlesByAchievement = [];
        if($hasWinnerTitleSelection && $model->achievements){
            $achievementIds = [];
            foreach($model->achievements as $participantAchieve){
                if($participantAchieve->achieve_id){
                    $achievementIds[] = (int)$participantAchieve->achieve_id;
                }
            }
            if($achievementIds){
                $this->ensureWinnerTitleRows($achievementIds);
                $winnerTitles = ProgramWinnerTitle::find()
                    ->where(['achievement_id' => array_unique($achievementIds)])
                    ->orderBy(['achievement_id' => SORT_ASC, 'winner_order' => SORT_ASC])
                    ->all();
                foreach($winnerTitles as $winnerTitle){
                    $winnerTitlesByAchievement[(int)$winnerTitle->achievement_id][] = $winnerTitle;
                }
            }
        }

        return $this->render('manager-award', [
            'model' => $model,
            'achieve' => $achieve,
            'list' => $list,
            'hasWinnerTitleSelection' => $hasWinnerTitleSelection,
            'winnerTitlesByAchievement' => $winnerTitlesByAchievement,
        ]);
    }

    public function actionManagerAddJury($id){
        $model = new JuryAssign();
        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('manager-add-jury', [
            'model' => $model
        ]);
    }

    public function actionUserListJson($q = null, $id = null){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query();
            $query->select(new Expression('`id`, `fullname` AS `text`'))
                ->from('user')
                ->where(['like', 'fullname', $q])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => User::find($id)->fullname];
        }
        return $out;
    }

    public function actionMentorListJson($q = null, $id = null){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query();
            $query->select(new Expression('u.id, u.fullname AS text'))
                ->from('user u')
                ->innerJoin('user_role r','r.user_id = u.id')
                ->where(['like', 'u.fullname', $q])
                ->andWhere(['r.role_name' => 'mentor'])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => User::find($id)->fullname];
        }
        return $out;
    }

    public function actionManagerFlag($id, $flag, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;
        $reg = $this->findModel($id);
        if($flag == 1){
            $reg->flag = 1;
        }else{
            $reg->flag = 0;
        }
        if($reg->save()){
            Yii::$app->session->addFlash('success', "Flagged Participants Updated");
        }
        return $this->redirect(['manager', 'id' => $reg->program_id, 'sub' => $sub]);
    }

    public function actionManagerMembers($id, $sub = null)
    {
        if(!Yii::$app->user->identity->isManager) return false;

        $registration = $this->findModel($id);
        $role = $this->findManagerRole($registration->program_id, $sub);
        if(!$role){
            throw new ForbiddenHttpException('No access');
        }
        if((int)$registration->program->has_sub === 1 && (int)$registration->program_sub !== (int)$sub){
            throw new ForbiddenHttpException('No access');
        }

        $members = $registration->members;
        if(empty($members)){
            $members = [new Member()];
        }

        if(Yii::$app->request->isPost){
            $oldIDs = ArrayHelper::map($registration->members, 'id', 'id');
            $members = Model::createMultiple(Member::class, $registration->members);
            Model::loadMultiple($members, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($members, 'id', 'id')));

            $valid = Model::validateMultiple($members);
            if($valid){
                $transaction = Yii::$app->db->beginTransaction();
                try{
                    if($deletedIDs){
                        Member::deleteAll(['id' => $deletedIDs, 'program_reg_id' => $registration->id]);
                    }

                    foreach($members as $member){
                        $member->program_reg_id = $registration->id;
                        $member->member_name = strtoupper(trim((string)$member->member_name));
                        $member->member_matric = strtoupper(trim((string)$member->member_matric));
                        if(!$member->save(false)){
                            throw new \RuntimeException('Unable to save group member.');
                        }
                    }

                    $registration->updated_at = time();
                    $registration->save(false, ['updated_at']);
                    $transaction->commit();
                    Yii::$app->session->addFlash('success', 'Group members updated.');
                    return $this->redirect(['manager-view', 'id' => $registration->id, 'sub' => $sub]);
                }catch(\Throwable $e){
                    $transaction->rollBack();
                    Yii::$app->session->addFlash('error', $e->getMessage());
                }
            }else{
                Yii::$app->session->addFlash('error', 'Please correct the highlighted member fields.');
            }
        }

        return $this->render('manager-members', [
            'registration' => $registration,
            'members' => $members,
            'programSub' => $registration->programSub,
            'sub' => $sub,
        ]);
    }

    public function actionManagerClearForm($id, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;

        $session = Yii::$app->session;
        $this->clearSession($session);
        $session->remove('keep-open');
        Yii::$app->session->addFlash('success', "Form Cleared");
        return $this->redirect(['manager', 'id' => $id, 'sub' => $sub]);
    }


    public function actionJuryResult($id, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;

        $role = $this->findManagerRole($id, $sub);
        if(!$role){
            throw new ForbiddenHttpException('No access');
        }
        $programSub = null;
        $program = $role->program;
        $rubricsQuery = $program->getProgramRubrics()->joinWith(['rubric r']);

        $programSub = null;
        $program = $role->program;

        if($role->program->has_sub == 1){
            if($sub){
                $programSub = $role->programSub;
                $rubricsQuery = $program->getProgramRubricsSub($sub)->joinWith(['rubric r']);
            }else{
                throw new NotFoundHttpException('Please provide sub program.');
            }
        }else{
            $rubricsQuery->andWhere(['or',
                [ProgramRubric::tableName() . '.program_sub' => null],
                [ProgramRubric::tableName() . '.program_sub' => 0],
            ]);
        }

        $programRubricTable = Yii::$app->db->schema->getTableSchema(ProgramRubric::tableName());
        if($programRubricTable && $programRubricTable->getColumn('is_active')){
            $rubricsQuery->andWhere([ProgramRubric::tableName() . '.is_active' => 1]);
        }else if($programRubricTable && $programRubricTable->getColumn('status')){
            $rubricsQuery->andWhere([ProgramRubric::tableName() . '.status' => 10]);
        }

        $rubricTable = Yii::$app->db->schema->getTableSchema(Rubric::tableName());
        if($rubricTable && $rubricTable->getColumn('is_active')){
            $rubricsQuery->andWhere(['r.is_active' => 1]);
        }else if($rubricTable && $rubricTable->getColumn('status')){
            $rubricsQuery->andWhere(['r.status' => 10]);
        }

        $rubrics = $rubricsQuery->orderBy([ProgramRubric::tableName() . '.id' => SORT_ASC])->all();

        $firstRubric = null;
        if($rubrics){
            $firstRubric = $rubrics[0]->rubric_id;
        }

        $searchModel = new JuryResultSearch();
        $searchModel->program_id = $id;
        $searchModel->program_sub = $sub;
        $allowedRubricIds = $rubrics ? ArrayHelper::getColumn($rubrics, 'rubric_id') : [];

        $params = $this->request->queryParams;
        $formName = $searchModel->formName();
        $requestedRubric = $params[$formName]['rubric'] ?? null;
        $requestedRubric = ($requestedRubric === '' || $requestedRubric === null) ? null : (int)$requestedRubric;

        if($requestedRubric !== null && in_array($requestedRubric, $allowedRubricIds, true)){
            $searchModel->rubric = $requestedRubric;
        }else{
            $searchModel->rubric = $firstRubric;
        }

        $params[$formName]['rubric'] = $searchModel->rubric;
        $dataProvider = $searchModel->search($params);

        $selectedRubric = Rubric::findOne($searchModel->rubric);

        return $this->render('jury-result', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'program' => $program,
            'rubrics' => $rubrics,
            'selectedRubric' => $selectedRubric,
            'programSub' => $programSub,
        ]);
    }

    public function actionManager($id, $sub = null){
        $session = Yii::$app->session;
        //print_r($session->get('keep-data'));die();
        if(!Yii::$app->user->identity->isManager) return false;

        $role = $this->findManagerRole($id, $sub);
        if(!$role){
            throw new ForbiddenHttpException('No access');
        }
        $programSub = null;
        $program = $role->program;
        if($program->program_type == 2){
            return $this->redirect(['manager-session', 'id' => $id, 'sub' => $sub]);
        }

        if($role->program->has_sub == 1){
            if($sub){
                $programSub = $role->programSub;
            }else{
                throw new NotFoundHttpException('Please provide sub program.');
            }
        }

        if($role && $role->program){
            
            $model = new JuryAssign();
            $model->scenario = 'assign';
            if ($session->has('keep-data') && $session->get('keep-data') == 1){
                $model->users = $session->get('users');
                $model->rubric_id = $session->get('rubric_id');
                $model->judging_session_id = $session->get('judging_session_id');
                $model->keep_data = $session->get('keep_data');
                $model->keep_open = $session->get('keep_open');
            }

            //cari ada stage tak
            $stages = $program->programStages;
            if($stages){
                $model->scenario = 'stage';
            }
            $model->stage = 0;
            //////////////////post
                if ($this->request->isPost) {
                    $post = Yii::$app->request->post();
                    $action = $post['action'] ?? null;

                    if($action === 'reset_judging_input'){
                        $selection = $post['selection'] ?? [];
                        if(!$selection){
                            Yii::$app->session->addFlash('error', 'Please select at least one participant first.');
                            return $this->refresh();
                        }

                        $registrations = ProgramRegistration::find()
                            ->where(['id' => $selection, 'program_id' => $role->program_id])
                            ->andFilterWhere(['program_sub' => $sub])
                            ->all();

                        $resetCount = 0;
                        $transaction = Yii::$app->db->beginTransaction();
                        try{
                            foreach($registrations as $registration){
                                $assignments = JuryAssign::find()
                                    ->where(['reg_id' => $registration->id])
                                    ->andWhere(['>', 'status', 0])
                                    ->all();

                                foreach($assignments as $assignment){
                                    RubricAnswer::deleteAll([
                                        'assignment_id' => $assignment->id,
                                        'rubric_id' => $assignment->rubric_id,
                                    ]);

                                    $assignment->status = 0;
                                    $assignment->score = null;
                                    $assignment->is_nullified = 0;
                                    $assignment->reason_nullified = null;
                                    $assignment->updated_at = time();
                                    $assignment->save(false, ['status', 'score', 'is_nullified', 'reason_nullified', 'updated_at']);
                                    $resetCount++;
                                }

                                $registration->setScoreAndAward();
                                $registration->save(false, ['score', 'award']);
                            }

                            $transaction->commit();
                            Yii::$app->session->addFlash('success', 'Judging input deleted for ' . $resetCount . ' assignment(s).');
                        }catch(\Throwable $e){
                            $transaction->rollBack();
                            Yii::$app->session->addFlash('error', $e->getMessage());
                        }

                        return $this->refresh();
                    }

                    if($action === 'delete_jury_assignments'){
                        $selection = array_filter(array_map('intval', (array)($post['selection'] ?? [])));
                        if(!$selection){
                            Yii::$app->session->addFlash('error', 'Please select at least one participant first.');
                            return $this->refresh();
                        }

                        $registrations = ProgramRegistration::find()
                            ->where(['id' => $selection, 'program_id' => $role->program_id])
                            ->andFilterWhere(['program_sub' => $sub])
                            ->all();

                        $deletedCount = 0;
                        $emptyCount = 0;
                        $blockedCount = 0;
                        $transaction = Yii::$app->db->beginTransaction();
                        try{
                            foreach($registrations as $registration){
                                $assignments = JuryAssign::find()
                                    ->where(['reg_id' => $registration->id])
                                    ->all();

                                if(!$assignments){
                                    $emptyCount++;
                                    continue;
                                }

                                $hasStartedAssignment = false;
                                foreach($assignments as $assignment){
                                    if((int)$assignment->status !== 0){
                                        $hasStartedAssignment = true;
                                        break;
                                    }
                                }

                                if($hasStartedAssignment){
                                    $blockedCount++;
                                    continue;
                                }

                                foreach($assignments as $assignment){
                                    if($assignment->delete() === false){
                                        throw new \RuntimeException('Unable to delete jury assignment #' . $assignment->id . '.');
                                    }
                                    $deletedCount++;
                                }

                                $registration->setScoreAndAward();
                                $registration->save(false, ['score', 'award']);
                            }

                            $transaction->commit();
                            if($deletedCount > 0){
                                Yii::$app->session->addFlash('success', 'Jury assignments deleted: ' . $deletedCount . '.');
                            }
                            if($emptyCount > 0){
                                Yii::$app->session->addFlash('info', $emptyCount . ' selected participant(s) skipped because they have no jury assignments.');
                            }
                            if($blockedCount > 0){
                                Yii::$app->session->addFlash('error', $blockedCount . ' selected participant(s) skipped because at least one jury assignment is already Judging or Complete.');
                            }
                        }catch(\Throwable $e){
                            $transaction->rollBack();
                            Yii::$app->session->addFlash('error', $e->getMessage());
                        }

                        return $this->refresh();
                    }

                    if($action === 'assign_applied_juries' || $action === 'auto_assign_applied_juries'){
                        $applicationIds = array_filter(array_map('intval', (array)($post['jury_application_ids'] ?? [])));
                        $rubricId = (int)($post['applied_rubric_id'] ?? 0);
                        $judgingSessionId = (int)($post['applied_judging_session_id'] ?? 0);
                        $stage = (int)($post['applied_stage'] ?? 0);
                        $perGroup = max(1, min(5, (int)($post['applied_juries_per_group'] ?? 1)));

                        if(!$applicationIds){
                            Yii::$app->session->addFlash('error', 'Please select at least one applied jury first.');
                            return $this->refresh();
                        }
                        if(!$rubricId){
                            Yii::$app->session->addFlash('error', 'Please select a rubric first.');
                            return $this->refresh();
                        }
                        $rubricScopeQuery = ProgramRubric::find()->where([
                            'program_id' => (int)$role->program_id,
                            'rubric_id' => $rubricId,
                        ]);
                        if($sub === null){
                            $rubricScopeQuery->andWhere(['or', ['program_sub' => null], ['program_sub' => 0]]);
                        }else{
                            $rubricScopeQuery->andWhere(['program_sub' => (int)$sub]);
                        }
                        if(!$rubricScopeQuery->exists()){
                            Yii::$app->session->addFlash('error', 'Selected rubric is not available for this program/category.');
                            return $this->refresh();
                        }

                        $appliedJuryData = $this->findApprovedAppliedJuryData($applicationIds, (int)$role->program_id, $sub ? (int)$sub : null);
                        $juryUsers = $appliedJuryData['users'];
                        $jurySessionMap = $appliedJuryData['sessionMap'];
                        if(!$juryUsers){
                            Yii::$app->session->addFlash('error', 'No approved jury applications found for the selected juries.');
                            return $this->refresh();
                        }

                        if($action === 'assign_applied_juries'){
                            $registrationIds = array_filter(array_map('intval', (array)($post['selection'] ?? [])));
                            if(!$registrationIds){
                                Yii::$app->session->addFlash('error', 'Please select participant(s) first before assigning applied juries.');
                                return $this->refresh();
                            }
                        }else{
                            $registrationQuery = ProgramRegistration::find()
                                ->select(['id'])
                                ->where(['program_id' => (int)$role->program_id])
                                ->andWhere(['in', 'status', [ProgramRegistration::STATUS_REGISTERED, ProgramRegistration::STATUS_COMPLETE]])
                                ->orderBy(['group_name' => SORT_ASC, 'id' => SORT_ASC]);
                            if($sub === null){
                                $registrationQuery->andWhere(['or', ['program_sub' => null], ['program_sub' => 0]]);
                            }else{
                                $registrationQuery->andWhere(['program_sub' => (int)$sub]);
                            }
                            $registrationIds = $registrationQuery->column();
                        }

                        $registrationQuery = ProgramRegistration::find()
                            ->where(['id' => $registrationIds, 'program_id' => (int)$role->program_id])
                            ->orderBy(['group_name' => SORT_ASC, 'id' => SORT_ASC]);
                        if($sub === null){
                            $registrationQuery->andWhere(['or', ['program_sub' => null], ['program_sub' => 0]]);
                        }else{
                            $registrationQuery->andWhere(['program_sub' => (int)$sub]);
                        }
                        $registrations = $registrationQuery->all();
                        if(!$registrations){
                            Yii::$app->session->addFlash('error', 'No participant groups found for this program/category.');
                            return $this->refresh();
                        }

                        $created = 0;
                        $skipped = 0;
                        $alreadyEnoughCount = 0;
                        $noAvailableJuryCount = 0;
                        $transaction = Yii::$app->db->beginTransaction();
                        try{
                            $juryLoad = $this->buildJuryAssignmentLoad($juryUsers, (int)$role->program_id, $sub ? (int)$sub : null, $stage);

                            foreach($registrations as $registration){
                                if($action === 'assign_applied_juries'){
                                    foreach($juryUsers as $juryUser){
                                        $userId = (int)$juryUser->id;
                                        if(isset($juryLoad[$userId]['regs'][(int)$registration->id])){
                                            $skipped++;
                                            continue;
                                        }

                                        $assign = new JuryAssign([
                                            'user_id' => $userId,
                                            'reg_id' => (int)$registration->id,
                                            'stage' => $stage,
                                            'rubric_id' => $rubricId,
                                            'judging_session_id' => ($jurySessionMap[$userId] ?? null) ?: ($judgingSessionId ?: null),
                                            'created_at' => time(),
                                            'updated_at' => time(),
                                        ]);

                                        if(!$assign->save()){
                                            throw new \RuntimeException('Unable to assign ' . $juryUser->fullname . ': ' . implode('; ', $assign->getFirstErrors()));
                                        }

                                        $juryLoad[$userId]['count']++;
                                        $juryLoad[$userId]['regs'][(int)$registration->id] = true;
                                        $created++;
                                    }
                                    continue;
                                }

                                $currentCount = (int)JuryAssign::find()
                                    ->where(['reg_id' => (int)$registration->id, 'stage' => $stage])
                                    ->count();
                                $needed = max(0, $perGroup - $currentCount);
                                if($needed === 0){
                                    $alreadyEnoughCount++;
                                    $skipped++;
                                    continue;
                                }

                                for($i = 0; $i < $needed; $i++){
                                    $juryUser = $this->pickLeastAssignedJuryUser($juryUsers, $juryLoad, (int)$registration->id, $stage);
                                    if(!$juryUser){
                                        $noAvailableJuryCount++;
                                        $skipped++;
                                        break;
                                    }

                                    $assign = new JuryAssign([
                                        'user_id' => (int)$juryUser->id,
                                        'reg_id' => (int)$registration->id,
                                        'stage' => $stage,
                                        'rubric_id' => $rubricId,
                                        'judging_session_id' => ($jurySessionMap[(int)$juryUser->id] ?? null) ?: ($judgingSessionId ?: null),
                                        'created_at' => time(),
                                        'updated_at' => time(),
                                    ]);

                                    if(!$assign->save()){
                                        throw new \RuntimeException('Unable to assign ' . $juryUser->fullname . ': ' . implode('; ', $assign->getFirstErrors()));
                                    }

                                    $juryLoad[(int)$juryUser->id]['count']++;
                                    $juryLoad[(int)$juryUser->id]['regs'][(int)$registration->id] = true;
                                    $created++;
                                }
                            }

                            $transaction->commit();
                            if($created > 0){
                                Yii::$app->session->addFlash('success', 'Applied jury assignments created: ' . $created . '.');
                            }else{
                                Yii::$app->session->addFlash('info', 'No new applied jury assignments were created. The selected juries may already be assigned or the groups may already have enough juries.');
                            }
                            Yii::$app->session->addFlash('info', 'Auto assign checked ' . count($registrations) . ' participant group(s), ' . count($juryUsers) . ' selected applied juries, target ' . $perGroup . ' jury/juries per group.');
                            if($alreadyEnoughCount > 0){
                                Yii::$app->session->addFlash('info', $alreadyEnoughCount . ' group(s) already had enough jury assignments for the selected stage.');
                            }
                            if($noAvailableJuryCount > 0){
                                Yii::$app->session->addFlash('info', $noAvailableJuryCount . ' group(s) had no available selected jury left to assign.');
                            }
                            if($skipped > 0){
                                Yii::$app->session->addFlash('info', $skipped . ' participant group(s) skipped or already had enough jury assignments.');
                            }
                        }catch(\Throwable $e){
                            $transaction->rollBack();
                            Yii::$app->session->addFlash('error', $e->getMessage());
                        }

                        return $this->refresh();
                    }

                    if($action === 'update_applied_jury_session'){
                        $applicationId = (int)($post['application_id'] ?? 0);
                        $judgingSessionId = (int)($post['application_judging_session_id'] ?? 0);

                        $applicationQuery = JuryApplication::find()
                            ->where([
                                'id' => $applicationId,
                                'program_id' => (int)$role->program_id,
                                'status' => 10,
                            ]);
                        if($sub === null){
                            $applicationQuery->andWhere(['or', ['program_sub_id' => null], ['program_sub_id' => 0]]);
                        }else{
                            $applicationQuery->andWhere(['program_sub_id' => (int)$sub]);
                        }
                        $application = $applicationQuery->one();

                        if(!$application){
                            Yii::$app->session->addFlash('error', 'Selected jury application was not found in this program/category.');
                            return $this->refresh();
                        }

                        if($judgingSessionId > 0){
                            $sessionModel = RubricJudgingSession::findOne($judgingSessionId);
                            if(!$sessionModel){
                                Yii::$app->session->addFlash('error', 'Selected judging session was not found.');
                                return $this->refresh();
                            }

                            $rubricScopeQuery = ProgramRubric::find()->where([
                                'program_id' => (int)$role->program_id,
                                'rubric_id' => (int)$sessionModel->rubric_id,
                            ]);
                            if($sub === null){
                                $rubricScopeQuery->andWhere(['or', ['program_sub' => null], ['program_sub' => 0]]);
                            }else{
                                $rubricScopeQuery->andWhere(['program_sub' => (int)$sub]);
                            }
                            if(!$rubricScopeQuery->exists()){
                                Yii::$app->session->addFlash('error', 'Selected judging session is not available for this program/category.');
                                return $this->refresh();
                            }

                            $application->judging_session_id = $judgingSessionId;
                        }else{
                            $application->judging_session_id = null;
                        }

                        if($application->save(false, ['judging_session_id'])){
                            Yii::$app->session->addFlash('success', 'Applied jury session updated.');
                        }else{
                            Yii::$app->session->addFlash('error', 'Unable to update applied jury session.');
                        }

                        return $this->refresh();
                    }
                }

                if ($this->request->isPost && $model->load($this->request->post())) {
                    $post = Yii::$app->request->post();

                    //proses session
                    //echo $model->keep_data;die();
                    if($model->keep_data == 1){
                        //die('keep data 1');
                        $session->set('keep-data', 1);
                        $session->set('users', $model->users);
                        $session->set('rubric_id', $model->rubric_id);
                        $session->set('judging_session_id', $model->judging_session_id);
                        $session->set('keep_data', $model->keep_data);
                        $session->set('keep_open', $model->keep_data);
                    }else{
                        $this->clearSession($session);
                    }

                    if($model->keep_open == 1){
                        $session->set('keep-open', 1);
                    }else{
                        $session->remove('keep-open');
                    }
                    // echo '<pre>';
                    // print_r($this->request->post());die();
                    $users = $model->users;
                    if(isset($post['selection'])){
                        $kira_juri = 0;
                        $selection = $post['selection'];
                        foreach($selection as $select){

                            if($users){
                                foreach($users as $u){
                                    //validate dh assign ke belum
                                    $ada = JuryAssign::findOne(['user_id' => $u, 'reg_id' => $select, 'stage' => $model->stage]);
                                    if($ada){
                                        $name = $ada->user->fullname;
                                        $peserta = $ada->registration->participantText;
                                        Yii::$app->session->addFlash('error', 'Failed: ' .$name . ' had been assigned to ' . $peserta);
                                    }else{
                                        try{
                                            $registration = ProgramRegistration::findOne((int)$select);
                                            if(!$registration){
                                                throw new \RuntimeException('Participant registration not found.');
                                            }
                                            $this->ensureApprovedJuryApplicationForUser(
                                                User::findOne((int)$u),
                                                (int)$registration->program_id,
                                                $registration->program_sub ? (int)$registration->program_sub : null,
                                                $model->judging_session_id ? (int)$model->judging_session_id : null
                                            );
                                        }catch(\Throwable $e){
                                            Yii::$app->session->addFlash('error', 'Failed to prepare jury application: ' . $e->getMessage());
                                            continue;
                                        }

                                        $jury = new JuryAssign([
                                            'user_id' => $u,
                                            'reg_id' => $select,
                                            'stage' => $model->stage,
                                            'rubric_id' => $model->rubric_id,
                                            'judging_session_id' => $model->judging_session_id,
                                            'created_at' => time(),
                                            'updated_at' => time(),
                                        ]);
                                        if($jury->save()){
                                            $kira_juri++;
                                        }else{
                                            $jury->flashError();
                                        }
                                        
                                    }
                                    
                                }
                            }

                        }
                        Yii::$app->session->addFlash('success', "Juries (".$kira_juri.") have been assigned to participants");
                    }
                    return $this->refresh();
                }
        
            $searchModel = new ProgramRegistrationManagerSearch();
            $searchModel->program_id = $role->program_id;
            $searchModel->program_sub = $sub;
            $dataProvider = $searchModel->search($this->request->queryParams);

            $juryStatusSummary = array_fill_keys(array_keys(JuryAssign::getStatusArray()), 0);
            $summaryRows = JuryAssign::find()->alias('j')
                ->innerJoinWith(['registration r'], false)
                ->select(['j.status', 'total' => 'COUNT(*)'])
                ->where(['r.program_id' => $role->program_id])
                ->andFilterWhere(['r.program_sub' => $sub])
                ->groupBy('j.status')
                ->asArray()
                ->all();

            foreach($summaryRows as $summaryRow){
                $juryStatusSummary[(int)$summaryRow['status']] = (int)$summaryRow['total'];
            }

            $unassignedCountQuery = (new Query())
                ->from(['r' => ProgramRegistration::tableName()])
                ->leftJoin(['j' => JuryAssign::tableName()], 'j.reg_id = r.id')
                ->where(['r.program_id' => (int)$role->program_id])
                ->andWhere(['in', 'r.status', [ProgramRegistration::STATUS_REGISTERED, ProgramRegistration::STATUS_COMPLETE]])
                ->andWhere(['j.id' => null]);

            if($sub === null){
                $unassignedCountQuery->andWhere(['or', ['r.program_sub' => null], ['r.program_sub' => 0]]);
            }else{
                $unassignedCountQuery->andWhere(['r.program_sub' => (int)$sub]);
            }

            $unassignedCount = (int)$unassignedCountQuery->count('DISTINCT r.id');

            $juryStatsQuery = (new Query())
                ->select([
                    'user_id' => 'u.id',
                    'fullname' => 'u.fullname',
                    'assigned' => new Expression('SUM(CASE WHEN j.status = 0 THEN 1 ELSE 0 END)'),
                    'judging' => new Expression('SUM(CASE WHEN j.status = 10 THEN 1 ELSE 0 END)'),
                    'complete' => new Expression('SUM(CASE WHEN j.status = 20 THEN 1 ELSE 0 END)'),
                ])
                ->from(['j' => JuryAssign::tableName()])
                ->innerJoin(['r' => ProgramRegistration::tableName()], 'r.id = j.reg_id')
                ->innerJoin(['u' => User::tableName()], 'u.id = j.user_id')
                ->where(['r.program_id' => (int)$role->program_id])
                ->groupBy(['u.id', 'u.fullname'])
                ->orderBy(['u.fullname' => SORT_ASC]);

            if($sub === null){
                $juryStatsQuery->andWhere(['or', ['r.program_sub' => null], ['r.program_sub' => 0]]);
            }else{
                $juryStatsQuery->andWhere(['r.program_sub' => (int)$sub]);
            }

            $juryStats = $juryStatsQuery->all();

            $appliedJuryQuery = JuryApplication::find()->alias('ja')
                ->innerJoin(['jp' => JuryProfile::tableName()], 'jp.id = ja.jury_profile_id')
                ->innerJoin(['u' => User::tableName()], 'u.id = jp.user_id')
                ->where([
                    'ja.program_id' => (int)$role->program_id,
                    'ja.status' => 10,
                ])
                ->with(['juryProfile.user', 'judgingSession'])
                ->orderBy(['jp.fullname' => SORT_ASC]);
            if($sub === null){
                $appliedJuryQuery->andWhere(['or', ['ja.program_sub_id' => null], ['ja.program_sub_id' => 0]]);
            }else{
                $appliedJuryQuery->andWhere(['ja.program_sub_id' => (int)$sub]);
            }
            $appliedJuries = $appliedJuryQuery->all();

            $participantQuery = (new Query())
                ->from(['r' => ProgramRegistration::tableName()])
                ->leftJoin(['u' => User::tableName()], 'u.id = r.user_id')
                ->leftJoin(['m' => Member::tableName()], 'm.program_reg_id = r.id')
                ->where(['r.program_id' => (int)$role->program_id])
                ->andWhere(['in', 'r.status', [ProgramRegistration::STATUS_REGISTERED, ProgramRegistration::STATUS_COMPLETE]]);

            if($sub === null){
                $participantQuery->andWhere(['or', ['r.program_sub' => null], ['r.program_sub' => 0]]);
            }else{
                $participantQuery->andWhere(['r.program_sub' => (int)$sub]);
            }

            $groupCount = (int)$participantQuery->count('DISTINCT r.id');
            $memberCount = (int)$participantQuery->count('m.id');
            $leaderMemberCount = (int)(clone $participantQuery)
                ->select(new Expression("COUNT(DISTINCT CASE WHEN ((m.member_matric IS NOT NULL AND m.member_matric <> '' AND u.matric IS NOT NULL AND u.matric <> '' AND m.member_matric = u.matric) OR ((m.member_matric IS NULL OR m.member_matric = '') AND m.member_name = u.fullname)) THEN m.id ELSE NULL END)"))
                ->scalar();

            $registrationSummary = [
                'participantCount' => $groupCount + $memberCount - $leaderMemberCount,
                'groupCount' => $groupCount,
                'unassignedCount' => $unassignedCount,
            ];
    
            return $this->render('manager', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'role' => $role,
                'model' => $model,
                'programSub' => $programSub,
                'juryStatusSummary' => $juryStatusSummary,
                'registrationSummary' => $registrationSummary,
                'juryStats' => $juryStats,
                'appliedJuries' => $appliedJuries,
            ]);
        }

        
    }

    public function actionManagerExportAssignments($id, $sub = null)
    {
        if(!Yii::$app->user->identity->isManager) return false;

        $role = $this->findManagerRole($id, $sub);
        if(!$role){
            throw new ForbiddenHttpException('No access');
        }

        $program = $role->program;
        $programSub = null;
        if($program->has_sub == 1){
            if($sub){
                $programSub = $role->programSub;
            }else{
                throw new NotFoundHttpException('Please provide sub program.');
            }
        }

        $registrations = ProgramRegistration::find()
            ->where(['program_id' => (int)$program->id])
            ->andWhere(['in', 'status', [ProgramRegistration::STATUS_REGISTERED, ProgramRegistration::STATUS_COMPLETE]])
            ->andFilterWhere(['program_sub' => $sub])
            ->with(['user', 'members', 'juries.user', 'juries.judgingSession'])
            ->orderBy(['group_name' => SORT_ASC, 'id' => SORT_ASC])
            ->all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Assignments');
        $sheet->fromArray([
            'group name',
            'names',
            'matric',
            'jury name',
            'jury email',
            'session',
            'session_id',
        ], null, 'A1');

        $rowNo = 2;
        foreach($registrations as $registration){
            $participantRows = $this->buildRegistrationParticipantExportRows($registration);
            $juries = $registration->juries ?: [null];

            foreach($participantRows as $participantRow){
                foreach($juries as $jury){
                    $session = $jury ? $jury->judgingSession : null;
                    $juryUser = $jury ? $jury->user : null;
                    $sheet->fromArray([
                        $registration->group_name,
                        $participantRow['name'],
                        $participantRow['matric'],
                        $juryUser ? $juryUser->fullname : '',
                        $juryUser ? $juryUser->email : '',
                        $session ? $this->formatJudgingSessionExportText($session) : '',
                        $session ? $session->id : '',
                    ], null, 'A' . $rowNo);
                    $rowNo++;
                }
            }
        }

        foreach(range('A', 'G') as $column){
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $filenameParts = ['I-CREATE_ASSIGNMENTS', $program->program_abbr];
        if($programSub){
            $filenameParts[] = $programSub->sub_abbr ?: ('SUB' . $programSub->id);
        }
        $filenameParts[] = date('Y-m-d');
        $filename = preg_replace('/[^A-Za-z0-9_\-]+/', '_', implode('_', $filenameParts)) . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();
        $spreadsheet->disconnectWorksheets();

        return Yii::$app->response->sendContentAsFile($content, $filename, [
            'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'inline' => false,
        ]);
    }

    public function actionManagerImportJuryAssignments($id, $sub = null)
    {
        if(!Yii::$app->user->identity->isManager) return false;

        [$role, $program, $programSub] = $this->findManagerProgramScope($id, $sub);

        $rubrics = $this->getScopedProgramRubrics($program, $programSub);
        if((int)$program->has_sub === 1 && $programSub){
            $subQuery = ProgramSub::find()->where(['program_id' => (int)$program->id]);
            $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
            if($subTable && $subTable->getColumn('is_active')){
                $subQuery->andWhere(['is_active' => 1]);
            }
            $activeSubIds = ArrayHelper::getColumn($subQuery->all(), 'id');
            $rubrics = [];
            if($activeSubIds){
                $rubrics = ProgramRubric::find()
                    ->alias('pr')
                    ->where(['pr.program_id' => (int)$program->id])
                    ->andWhere(['pr.program_sub' => $activeSubIds])
                    ->all();
            }
        }
        $selectedStage = (int)Yii::$app->request->post('stage', Yii::$app->request->get('stage', 0));
        $stages = $program->programStages;

        $rubricIds = ArrayHelper::getColumn($rubrics, 'rubric_id');
        $rubricScopeMap = [];
        foreach($rubrics as $rubricLink){
            $rid = (int)$rubricLink->rubric_id;
            if(!isset($rubricScopeMap[$rid])){
                $rubricScopeMap[$rid] = [];
            }
            $sid = $rubricLink->program_sub ? (int)$rubricLink->program_sub : 0;
            if(!in_array($sid, $rubricScopeMap[$rid], true)){
                $rubricScopeMap[$rid][] = $sid;
            }
        }

        $availableSessions = [];
        if($rubricIds){
            $availableSessions = RubricJudgingSession::find()
                ->where(['rubric_id' => $rubricIds])
                ->orderBy(['rubric_id' => SORT_ASC, 'datetime_start' => SORT_ASC, 'session_name' => SORT_ASC])
                ->all();
        }
        $availableSessionIds = ArrayHelper::getColumn($availableSessions, 'id');
        $sessionReferenceRows = [];
        foreach($availableSessions as $session){
            $sessionRubricId = (int)$session->rubric_id;
            $scopeSubIds = $rubricScopeMap[$sessionRubricId] ?? [0];
            foreach($scopeSubIds as $scopeSubId){
                $sessionReferenceRows[] = [
                    'session' => $session,
                    'program_sub_id' => $scopeSubId ?: null,
                ];
            }
        }

        if(Yii::$app->request->isPost){
            $csvFile = UploadedFile::getInstanceByName('csv_file');
            if(!$csvFile){
                Yii::$app->session->addFlash('error', 'Please upload a CSV file.');
                return $this->refresh();
            }

            $handle = $this->openImportedCsvFile($csvFile->tempName);
            if($handle === false){
                Yii::$app->session->addFlash('error', 'Unable to read the uploaded CSV file.');
                return $this->refresh();
            }

            $headers = fgetcsv($handle);
            if(!$headers){
                fclose($handle);
                Yii::$app->session->addFlash('error', 'The CSV file is empty.');
                return $this->refresh();
            }

            $headers = array_map(function($header){
                return strtolower(trim((string)$header));
            }, $headers);

            $requiredColumns = ['jury_name', 'jury_email', 'group_name', 'rubric_id', 'session_id'];
            foreach($requiredColumns as $column){
                if(!in_array($column, $headers, true)){
                    fclose($handle);
                    Yii::$app->session->addFlash('error', 'Missing required column: ' . $column);
                    return $this->refresh();
                }
            }

            $unknownHeaders = array_diff($headers, $requiredColumns);
            if($unknownHeaders){
                fclose($handle);
                Yii::$app->session->addFlash('error', 'Unknown CSV column(s): ' . implode(', ', $unknownHeaders));
                return $this->refresh();
            }

            $idx = array_flip($headers);
            $created = 0;
            $skipped = 0;
            $messages = [];
            $warnings = [];
            $createdUsers = 0;
            $createdProfiles = 0;
            $createdRoles = 0;
            $rowNo = 1;
            $lastJuryName = '';
            $lastJuryEmail = '';
            $lastRubricId = 0;
            $lastSessionIdRaw = '';
            $hasLastSessionId = false;

            $transaction = Yii::$app->db->beginTransaction();
            try{
                while(($row = fgetcsv($handle)) !== false){
                    $rowNo++;

                    $juryName = trim((string)($row[$idx['jury_name']] ?? ''));
                    $juryEmail = strtolower(trim((string)($row[$idx['jury_email']] ?? '')));
                    $groupName = trim((string)($row[$idx['group_name']] ?? ''));
                    $rubricIdRaw = trim((string)($row[$idx['rubric_id']] ?? ''));
                    $sessionIdRaw = trim((string)($row[$idx['session_id']] ?? ''));

                    if($juryName === '' && $lastJuryName !== ''){
                        $juryName = $lastJuryName;
                    }else if($juryName !== ''){
                        $lastJuryName = $juryName;
                    }

                    if($juryEmail === '' && $lastJuryEmail !== ''){
                        $juryEmail = $lastJuryEmail;
                    }else if($juryEmail !== ''){
                        $lastJuryEmail = $juryEmail;
                    }

                    if($rubricIdRaw === '' && $lastRubricId > 0){
                        $rubricId = $lastRubricId;
                    }else{
                        $rubricId = (int)$rubricIdRaw;
                        if($rubricId > 0){
                            $lastRubricId = $rubricId;
                        }
                    }

                    if($sessionIdRaw === '' && $hasLastSessionId){
                        $sessionIdRaw = $lastSessionIdRaw;
                    }else if($sessionIdRaw !== ''){
                        $lastSessionIdRaw = $sessionIdRaw;
                        $hasLastSessionId = true;
                    }
                    $sessionId = $sessionIdRaw === '' ? 0 : (int)$sessionIdRaw;

                    if($juryName === '' && $juryEmail === '' && $groupName === '' && $rubricId === 0 && $sessionId === 0){
                        continue;
                    }

                    if($juryName === '' || $juryEmail === '' || $groupName === '' || $rubricId <= 0){
                        $skipped++;
                        $messages[] = 'Row ' . $rowNo . ': jury_name, jury_email, group_name and rubric_id are required.';
                        continue;
                    }

                    if(!in_array($rubricId, $rubricIds, true)){
                        $skipped++;
                        $messages[] = 'Row ' . $rowNo . ': rubric_id is not available in this manager scope - ' . $rubricId;
                        continue;
                    }

                    $jury = User::find()
                        ->where('LOWER(email) = :email', [':email' => $juryEmail])
                        ->one();
                    if(!$jury){
                        $jury = new User();
                        $jury->scenario = 'create';
                        $jury->email = $juryEmail;
                        $jury->username = $juryEmail;
                        $jury->fullname = $juryName;
                        $jury->status = User::STATUS_ACTIVE;
                        $jury->is_student = 0;
                        $jury->is_internal = 0;
                        $jury->generateAuthKey();
                        $jury->setPassword($juryEmail);

                        if(!$jury->save()){
                            $skipped++;
                            $messages[] = 'Row ' . $rowNo . ': failed to create jury user - ' . implode('; ', $jury->getFirstErrors());
                            continue;
                        }
                        $createdUsers++;
                    }else{
                        $dirty = [];
                        if((int)$jury->status !== (int)User::STATUS_ACTIVE){
                            $jury->status = User::STATUS_ACTIVE;
                            $dirty[] = 'status';
                        }
                        if(!$jury->username){
                            $jury->username = $juryEmail;
                            $dirty[] = 'username';
                        }
                        if(!$jury->fullname){
                            $jury->fullname = $juryName;
                            $dirty[] = 'fullname';
                        }
                        if($dirty){
                            $jury->save(false, $dirty);
                        }
                    }

                    if($jury->fullname && strcasecmp(trim((string)$jury->fullname), $juryName) !== 0){
                        $warnings[] = 'Row ' . $rowNo . ': name mismatch for ' . $juryEmail . ' - user fullname: "' . $jury->fullname . '", CSV jury_name: "' . $juryName . '"';
                    }

                    $juryRole = UserRole::find()->where([
                        'user_id' => (int)$jury->id,
                        'role_name' => 'jury',
                    ])->one();
                    if(!$juryRole){
                        $juryRole = new UserRole();
                        $juryRole->user_id = (int)$jury->id;
                        $juryRole->role_name = 'jury';
                        $juryRole->status = 10;
                        $juryRole->approve_at = new Expression('NOW()');
                        if(!$juryRole->save()){
                            $skipped++;
                            $messages[] = 'Row ' . $rowNo . ': failed to create jury role - ' . implode('; ', $juryRole->getFirstErrors());
                            continue;
                        }
                        $createdRoles++;
                    }else if((int)$juryRole->status !== 10){
                        $juryRole->status = 10;
                        $juryRole->approve_at = new Expression('NOW()');
                        $juryRole->save(false, ['status', 'approve_at']);
                    }

                    $juryProfile = JuryProfile::find()->where(['user_id' => (int)$jury->id])->one();
                    if(!$juryProfile){
                        $juryProfile = new JuryProfile();
                        $juryProfile->user_id = (int)$jury->id;
                        $juryProfile->created_at = time();
                        $createdProfiles++;
                    }
                    $juryProfile->fullname = (string)$jury->fullname;
                    $juryProfile->category = $juryProfile->category ?: 'General';
                    $juryProfile->updated_at = time();
                    if(!$juryProfile->save()){
                        $skipped++;
                        $messages[] = 'Row ' . $rowNo . ': failed to create/update jury profile - ' . implode('; ', $juryProfile->getFirstErrors());
                        continue;
                    }

                    if($sessionId > 0){
                        if(!in_array($sessionId, $availableSessionIds, true)){
                            $skipped++;
                            $messages[] = 'Row ' . $rowNo . ': session_id is not available in this manager scope - ' . $sessionId;
                            continue;
                        }

                        $session = null;
                        foreach($availableSessions as $availableSession){
                            if((int)$availableSession->id === $sessionId){
                                $session = $availableSession;
                                break;
                            }
                        }
                        if(!$session || (int)$session->rubric_id !== $rubricId){
                            $skipped++;
                            $messages[] = 'Row ' . $rowNo . ': session_id does not belong to rubric_id.';
                            continue;
                        }
                    }else{
                        $sessionRequired = RubricJudgingSession::find()->where(['rubric_id' => $rubricId])->exists();
                        if($sessionRequired){
                            $skipped++;
                            $messages[] = 'Row ' . $rowNo . ': session_id is required for rubric_id ' . $rubricId;
                            continue;
                        }
                    }

                    $targetProgramSubId = null;
                    if((int)$program->has_sub === 1){
                        $scopeSubIds = $rubricScopeMap[$rubricId] ?? [];
                        if(!$scopeSubIds){
                            $skipped++;
                            $messages[] = 'Row ' . $rowNo . ': rubric_id is not mapped to any active sub program.';
                            continue;
                        }
                        if(count($scopeSubIds) > 1){
                            $skipped++;
                            $messages[] = 'Row ' . $rowNo . ': rubric_id/session_id is mapped to multiple sub programs under the same parent. Please use a unique rubric/session mapping.';
                            continue;
                        }
                        $targetProgramSubId = $scopeSubIds[0] ?: null;
                    }

                    $registrations = $this->findRegistrationsByImportedGroupNameOrRange(
                        (int)$program->id,
                        (int)$program->has_sub === 1 ? $targetProgramSubId : null,
                        $groupName
                    );
                    if(!$registrations){
                        $skipped++;
                        $messages[] = 'Row ' . $rowNo . ': group_name did not match any participant group in this scope after normalizing spaces/punctuation/leading zeroes - ' . $groupName;
                        continue;
                    }

                    foreach($registrations as $registration){
                        if(count($registrations) === 1 && strcasecmp(trim((string)$registration->group_name), $groupName) !== 0){
                            $warnings[] = 'Row ' . $rowNo . ': matched CSV group_name "' . $groupName . '" to participant group "' . $registration->group_name . '".';
                        }

                        try{
                            $this->ensureApprovedJuryApplicationForUser(
                                $jury,
                                (int)$registration->program_id,
                                $registration->program_sub ? (int)$registration->program_sub : null,
                                $sessionId ?: null
                            );
                        }catch(\Throwable $e){
                            $skipped++;
                            $messages[] = 'Row ' . $rowNo . ': failed to prepare approved jury application for group ' . $registration->group_name . ' - ' . $e->getMessage();
                            continue;
                        }

                        $existingAssign = JuryAssign::findOne([
                            'user_id' => (int)$jury->id,
                            'reg_id' => (int)$registration->id,
                            'stage' => $selectedStage,
                        ]);
                        if($existingAssign){
                            $skipped++;
                            $messages[] = 'Row ' . $rowNo . ': jury already assigned to group - ' . $registration->group_name;
                            continue;
                        }

                        $assign = new JuryAssign([
                            'user_id' => (int)$jury->id,
                            'reg_id' => (int)$registration->id,
                            'stage' => $selectedStage,
                            'rubric_id' => $rubricId,
                            'judging_session_id' => $sessionId ?: null,
                            'created_at' => time(),
                            'updated_at' => time(),
                        ]);

                        if(!$assign->save()){
                            $skipped++;
                            $firstErrors = $assign->getFirstErrors();
                            $messages[] = 'Row ' . $rowNo . ': failed to save assignment for group ' . $registration->group_name . ($firstErrors ? ' - ' . implode('; ', $firstErrors) : '');
                            continue;
                        }

                        $created++;
                    }
                }

                fclose($handle);
                $transaction->commit();

                if($created > 0){
                    Yii::$app->session->addFlash('success', $created . ' jury assignment(s) imported successfully.');
                }
                if($createdUsers > 0){
                    Yii::$app->session->addFlash('success', 'Created ' . $createdUsers . ' jury user(s). Default password: email.');
                }
                if($createdProfiles > 0){
                    Yii::$app->session->addFlash('success', 'Created ' . $createdProfiles . ' jury profile(s).');
                }
                if($createdRoles > 0){
                    Yii::$app->session->addFlash('success', 'Added jury role for ' . $createdRoles . ' user(s).');
                }
                if($warnings){
                    Yii::$app->session->addFlash('warning', implode('<br>', array_slice($warnings, 0, 10)));
                }
                if($skipped > 0){
                    Yii::$app->session->addFlash('warning', $skipped . ' row(s) skipped.');
                }
                if($messages){
                    Yii::$app->session->addFlash('error', implode('<br>', array_slice($messages, 0, 10)));
                }

                return $this->refresh();
            }catch(\Throwable $e){
                fclose($handle);
                $transaction->rollBack();
                Yii::$app->session->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('manager-import-jury-assignments', [
            'role' => $role,
            'program' => $program,
            'programSub' => $programSub,
            'rubrics' => $rubrics,
            'availableSessions' => $availableSessions,
            'sessionReferenceRows' => $sessionReferenceRows,
            'stages' => $stages,
            'selectedStage' => $selectedStage,
        ]);
    }

    public function actionManagerJuryApplications($id, $sub = null)
    {
        if(!Yii::$app->user->identity->isManager) return false;

        $role = $this->findManagerRole($id, $sub);
        if(!$role){
            throw new ForbiddenHttpException('No access');
        }

        $programSub = null;
        if((int)$role->program->has_sub === 1){
            if(!$sub){
                throw new NotFoundHttpException('Please provide sub program.');
            }
            $programSub = $role->programSub;
        }

        $searchModel = new JuryApplicationSearch();
        $searchModel->program_id = (int)$id;
        $searchModel->program_sub_id = $sub ? (int)$sub : null;

        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('manager-jury-applications', [
            'role' => $role,
            'programSub' => $programSub,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAdminJuryApplications($id, $sub = null)
    {
        if(!Yii::$app->user->identity->isAdminJury) return false;

        $program = Program::findOne((int)$id);
        if(!$program){
            throw new NotFoundHttpException('Program not found.');
        }

        $programSub = null;
        if((int)$program->has_sub === 1){
            if(!$sub){
                throw new NotFoundHttpException('Please provide sub program.');
            }
            $programSub = ProgramSub::findOne((int)$sub);
            if(!$programSub || (int)$programSub->program_id !== (int)$program->id){
                throw new NotFoundHttpException('Sub program not found.');
            }
        }

        $searchModel = new JuryApplicationSearch();
        $searchModel->program_id = (int)$id;
        $searchModel->program_sub_id = $sub ? (int)$sub : null;

        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('manager-jury-applications', [
            'role' => null,
            'program' => $program,
            'programSub' => $programSub,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAdminJuryApplicationsAll()
    {
        if(!Yii::$app->user->identity->isAdminJury) return false;

        $searchModel = new JuryApplicationSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('manager-jury-applications', [
            'role' => null,
            'program' => null,
            'programSub' => null,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionJuryApplicationView($id)
    {
        $model = JuryApplication::findOne((int)$id);
        if(!$model){
            throw new NotFoundHttpException('Application not found.');
        }

        if(Yii::$app->user->identity->isAdminJury){
            return $this->render('jury-application-view', [
                'model' => $model,
            ]);
        }

        if(!Yii::$app->user->identity->isManager){
            throw new ForbiddenHttpException('No access');
        }

        $role = UserRole::findOne([
            'program_id' => $model->program_id,
            'user_id' => Yii::$app->user->identity->id,
            'role_name' => 'manager',
            'program_sub' => $model->program_sub_id,
            'status' => 10,
        ]);
        if(!$role){
            throw new ForbiddenHttpException('No access');
        }

        return $this->render('jury-application-view', [
            'model' => $model,
        ]);
    }

    public function actionJuryApplicationCreateManual()
    {
        if(!Yii::$app->user->identity->isAdminJury) return false;

        $model = new JuryApplicationManualCreateForm();
        $programScopeOptions = $this->buildManualJuryApplicationProgramScopeOptions();
        $sessionOptions = $this->buildManualJuryApplicationSessionOptions();
        $sessionScopeMap = $this->buildManualJuryApplicationSessionScopeMap();

        if($this->request->isPost && $model->load($this->request->post()) && $model->validate()){
            $scope = $this->parseManualJuryApplicationProgramScope($model->program_scope);
            if(!$scope){
                Yii::$app->session->addFlash('error', 'Please select a valid program/category.');
                return $this->render('jury-application-create-manual', [
                    'model' => $model,
                    'programScopeOptions' => $programScopeOptions,
                    'sessionOptions' => $sessionOptions,
                    'sessionScopeMap' => $sessionScopeMap,
                ]);
            }

            $judgingSessionId = $model->judging_session_id ? (int)$model->judging_session_id : null;
            if($judgingSessionId && !$this->isJudgingSessionValidForScope($judgingSessionId, $scope['program_id'], $scope['program_sub_id'])){
                Yii::$app->session->addFlash('error', 'Selected judging session is not available for the selected program/category.');
                return $this->render('jury-application-create-manual', [
                    'model' => $model,
                    'programScopeOptions' => $programScopeOptions,
                    'sessionOptions' => $sessionOptions,
                    'sessionScopeMap' => $sessionScopeMap,
                ]);
            }

            $transaction = Yii::$app->db->beginTransaction();
            try{
                $email = strtolower(trim((string)$model->email));
                $user = User::find()->where(['email' => $email])->one();
                $createdUser = false;

                if(!$user){
                    $user = new User();
                    $user->scenario = 'create';
                    $user->email = $email;
                    $user->username = $email;
                    $user->fullname = trim((string)$model->fullname);
                    $user->status = User::STATUS_ACTIVE;
                    $user->is_student = 0;
                    $user->is_internal = 0;
                    $user->phone = $model->phone;
                    $user->institution = $model->institution;
                    $user->generateAuthKey();
                    $user->setPassword($model->password ? $model->password : $email);
                    if(!$user->save()){
                        throw new \RuntimeException('Unable to create user: ' . implode('; ', $user->getFirstErrors()));
                    }
                    $createdUser = true;
                }else{
                    $user->fullname = trim((string)$model->fullname);
                    $user->status = User::STATUS_ACTIVE;
                    $user->is_student = 0;
                    if(!$user->phone){
                        $user->phone = $model->phone;
                    }
                    if(!$user->institution){
                        $user->institution = $model->institution;
                    }
                    if(!$user->save(false)){
                        throw new \RuntimeException('Unable to update existing user.');
                    }
                }

                $application = $this->ensureJuryPipelineForUser(
                    $user,
                    (int)$scope['program_id'],
                    $scope['program_sub_id'],
                    $judgingSessionId,
                    10,
                    [
                        'category' => $model->category,
                        'phone' => $model->phone,
                        'institution' => $model->institution,
                        'designation' => $model->designation,
                        'address' => $model->address,
                    ]
                );

                $profile = JuryProfile::find()->where(['user_id' => (int)$user->id])->one();
                if($profile){
                    $profile->fullname = trim((string)$model->fullname);
                    $profile->category = trim((string)$model->category);
                    $profile->phone = trim((string)$model->phone) !== '' ? trim((string)$model->phone) : null;
                    $profile->institution = trim((string)$model->institution) !== '' ? trim((string)$model->institution) : null;
                    $profile->designation = trim((string)$model->designation) !== '' ? trim((string)$model->designation) : null;
                    $profile->address = trim((string)$model->address) !== '' ? trim((string)$model->address) : null;
                    $profile->updated_at = time();
                    if(!$profile->save()){
                        throw new \RuntimeException('Unable to save jury profile: ' . implode('; ', $profile->getFirstErrors()));
                    }
                }

                $transaction->commit();
                Yii::$app->session->addFlash('success', ($createdUser ? 'Jury user created' : 'Existing user updated') . ' and approved jury application saved.');
                return $this->redirect(['jury-application-view', 'id' => $application->id]);
            }catch(\Throwable $e){
                $transaction->rollBack();
                Yii::$app->session->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('jury-application-create-manual', [
            'model' => $model,
            'programScopeOptions' => $programScopeOptions,
            'sessionOptions' => $sessionOptions,
            'sessionScopeMap' => $sessionScopeMap,
        ]);
    }

    public function actionJuryApplicationBulkUpdate()
    {
        if(!Yii::$app->request->isPost){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $ids = Yii::$app->request->post('selection', []);
        $action = Yii::$app->request->post('bulk_action');

        if(!in_array($action, ['approve', 'reject'], true)){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $targetStatus = $action === 'approve' ? 10 : 20;

        if(!$ids || !is_array($ids)){
            Yii::$app->session->addFlash('warning', 'No applications selected.');
            return $this->redirect(Yii::$app->request->referrer ?: ['admin-jury-applications-all']);
        }

        $isAdmin = !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdminJury;
        $isManager = !Yii::$app->user->isGuest && Yii::$app->user->identity->isManager;

        if(!$isAdmin && !$isManager){
            throw new ForbiddenHttpException('No access');
        }

        $updated = 0;
        $skipped = 0;
        foreach($ids as $id){
            $app = JuryApplication::findOne((int)$id);
            if(!$app){
                $skipped++;
                continue;
            }

            if($isManager && !$isAdmin){
                $role = UserRole::findOne([
                    'program_id' => $app->program_id,
                    'user_id' => Yii::$app->user->identity->id,
                    'role_name' => 'manager',
                    'program_sub' => $app->program_sub_id,
                    'status' => 10,
                ]);
                if(!$role){
                    $skipped++;
                    continue;
                }
            }

            $app->status = $targetStatus;
            if($app->save(false, ['status'])){
                $updated++;
            }else{
                $skipped++;
            }
        }

        if($updated > 0){
            Yii::$app->session->addFlash('success', 'Updated ' . $updated . ' application(s).');
        }
        if($skipped > 0){
            Yii::$app->session->addFlash('warning', 'Skipped ' . $skipped . ' application(s).');
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['admin-jury-applications-all']);
    }

    public function actionJuryApplicationImport()
    {
        if(!Yii::$app->user->identity->isAdminJury) return false;

        return $this->render('jury-application-import');
    }

    public function actionJuryApplicationImportCsv()
    {
        if(!Yii::$app->user->identity->isAdminJury) return false;

        if(!Yii::$app->request->isPost){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $file = UploadedFile::getInstanceByName('csv_file');
        if(!$file){
            Yii::$app->session->addFlash('error', 'No file uploaded.');
            return $this->redirect(['jury-application-import']);
        }

        $handle = $this->openImportedCsvFile($file->tempName);
        if(!$handle){
            Yii::$app->session->addFlash('error', 'Could not read uploaded file.');
            return $this->redirect(['jury-application-import']);
        }

        $header = fgetcsv($handle);
        if(!$header){
            fclose($handle);
            Yii::$app->session->addFlash('error', 'Empty CSV.');
            return $this->redirect(['jury-application-import']);
        }

        $header = array_map(function($h){
            return strtolower(trim((string)$h));
        }, $header);

        $required = ['email', 'judging_session_id'];
        foreach($required as $col){
            if(!in_array($col, $header, true)){
                fclose($handle);
                Yii::$app->session->addFlash('error', 'Missing required column: ' . $col);
                return $this->redirect(['jury-application-import']);
            }
        }

        $idx = array_flip($header);
        $created = 0;
        $skipped = 0;
        $errors = 0;

        $tx = Yii::$app->db->beginTransaction();
        try{
            while(($row = fgetcsv($handle)) !== false){
                $email = isset($idx['email']) ? trim((string)($row[$idx['email']] ?? '')) : '';
                $sessionId = isset($idx['judging_session_id']) ? (int)trim((string)($row[$idx['judging_session_id']] ?? '')) : 0;
                $programId = isset($idx['program_id']) ? (int)trim((string)($row[$idx['program_id']] ?? '')) : 0;
                $programSubId = isset($idx['program_sub_id']) ? (int)trim((string)($row[$idx['program_sub_id']] ?? '')) : null;
                $fullNameCsv = isset($idx['fullname']) ? trim((string)($row[$idx['fullname']] ?? '')) : '';

                if($email === '' || $sessionId <= 0){
                    $skipped++;
                    continue;
                }

                if($programId <= 0){
                    $session = RubricJudgingSession::findOne($sessionId);
                    if(!$session){
                        $skipped++;
                        continue;
                    }

                    $prRows = ProgramRubric::find()
                        ->select(['program_id', 'program_sub'])
                        ->where(['rubric_id' => $session->rubric_id])
                        ->asArray()
                        ->all();

                    if(!$prRows){
                        $skipped++;
                        continue;
                    }

                    if(count($prRows) !== 1){
                        $errors++;
                        continue;
                    }

                    $programId = (int)$prRows[0]['program_id'];
                    $programSubId = !empty($prRows[0]['program_sub']) ? (int)$prRows[0]['program_sub'] : null;
                }

                $user = User::find()->where(['email' => $email])->one();
                if(!$user){
                    if($fullNameCsv === ''){
                        $skipped++;
                        continue;
                    }

                    $user = new User();
                    $user->scenario = 'create';
                    $user->email = $email;
                    $user->username = $email;
                    $user->fullname = $fullNameCsv;
                    $user->status = User::STATUS_ACTIVE;
                    $user->is_student = 0;
                    $user->is_internal = 0;
                    $user->generateAuthKey();
                    $user->setPassword($email);

                    if(!$user->save()){
                        $errors++;
                        continue;
                    }
                }

                try{
                    $juryProfile = $this->ensureJuryRoleProfileRequirementForUser(
                        $user,
                        (int)$programId,
                        $programSubId ?: null,
                        $sessionId,
                        [
                            'category' => isset($idx['category']) ? trim((string)($row[$idx['category']] ?? '')) : '',
                            'phone' => isset($idx['phone']) ? trim((string)($row[$idx['phone']] ?? '')) : null,
                            'institution' => isset($idx['institution']) ? trim((string)($row[$idx['institution']] ?? '')) : null,
                            'designation' => isset($idx['designation']) ? trim((string)($row[$idx['designation']] ?? '')) : null,
                            'address' => isset($idx['address']) ? trim((string)($row[$idx['address']] ?? '')) : null,
                        ]
                    );

                    $existingApp = JuryApplication::find()->where([
                        'jury_profile_id' => (int)$juryProfile->id,
                        'program_id' => $programId,
                        'program_sub_id' => $programSubId ?: null,
                        'judging_session_id' => $sessionId,
                    ])
                    ->exists();
                    if($existingApp){
                        $skipped++;
                        continue;
                    }

                    $app = $this->ensureJuryPipelineForUser(
                        $user,
                        (int)$programId,
                        $programSubId ?: null,
                        $sessionId,
                        0,
                        []
                    );
                    if($app && (int)$app->status === 0){
                        $created++;
                    }else{
                        $errors++;
                    }
                }catch(\Throwable $e){
                    $errors++;
                    continue;
                }
            }

            fclose($handle);
            $tx->commit();
        }catch(\Throwable $e){
            fclose($handle);
            $tx->rollBack();
            throw $e;
        }

        if($created > 0){
            Yii::$app->session->addFlash('success', 'Imported ' . $created . ' application(s).');
        }
        if($skipped > 0){
            Yii::$app->session->addFlash('warning', 'Skipped ' . $skipped . ' row(s).');
        }
        if($errors > 0){
            Yii::$app->session->addFlash('error', 'Failed ' . $errors . ' row(s).');
        }

        return $this->redirect(['admin-jury-applications-all']);
    }

    public function actionManagerDashboard($id, $sub = null)
    {
        if(!Yii::$app->user->identity->isManager) return false;

        $program = Program::findOne((int)$id);
        if(!$program){
            throw new NotFoundHttpException('Program not found.');
        }

        if((int)$program->has_sub === 1 && !$sub){
            throw new NotFoundHttpException('Please provide sub program.');
        }

        $role = $this->findManagerRole($id, $sub);

        if(!$role){
            return;
        }

        $programSub = null;

        if((int)$program->has_sub === 1 && $sub){
            $programSub = $role->programSub;
        }

        return $this->render('manager-dashboard', [
            'role' => $role,
            'program' => $program,
            'programSub' => $programSub,
            'dashboardStats' => $this->buildDashboardStats($program, $programSub),
        ]);
    }

    public function actionManagerParent($id)
    {
        if(!Yii::$app->user->identity->isManager) return false;

        $isGeneralManager = UserRole::find()->where([
            'user_id' => Yii::$app->user->identity->id,
            'role_name' => 'general-manager',
            'status' => 10,
        ])->exists();

        if(Yii::$app->user->identity->isSuperadmin || $isGeneralManager){
            $role = new UserRole([
                'user_id' => Yii::$app->user->identity->id,
                'role_name' => 'manager',
                'program_id' => (int)$id,
                'status' => 10,
            ]);
        }else{
            $role = UserRole::find()->where([
                'program_id' => $id,
                'user_id' => Yii::$app->user->identity->id,
                'role_name' => 'manager',
            ])->andWhere(['not', ['status' => null]])->andWhere(['status' => 10])->one();
        }

        if(!$role){
            return;
        }

        $program = $role->program;
        if(!$program){
            return;
        }

        if((int)$program->has_sub === 1){
            $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
            $hasSubActiveColumn = $subTable && $subTable->getColumn('is_active');
            $activeSubIds = [];
            foreach($program->programSubs as $sp){
                if($hasSubActiveColumn && (int)$sp->getAttribute('is_active') !== 1){
                    continue;
                }
                $activeSubIds[(int)$sp->id] = true;
            }

            if(Yii::$app->user->identity->isSuperadmin || $isGeneralManager){
                $roles = [new UserRole([
                    'user_id' => Yii::$app->user->identity->id,
                    'role_name' => 'manager',
                    'program_id' => (int)$id,
                    'status' => 10,
                ])];
            }else{
                $roles = UserRole::find()->where([
                    'program_id' => $id,
                    'user_id' => Yii::$app->user->identity->id,
                    'role_name' => 'manager',
                    'status' => 10,
                ])->all();
            }

            $hasProgramLevel = false;
            $allowedSubs = [];
            if($roles){
                foreach($roles as $r){
                    if(empty($r->program_sub)){
                        $hasProgramLevel = true;
                        break;
                    }
                    $allowedSubs[(int)$r->program_sub] = true;
                }
            }

            if(!$hasProgramLevel){
                foreach(array_keys($activeSubIds) as $sid){
                    if(!array_key_exists((int)$sid, $allowedSubs)){
                        throw new ForbiddenHttpException('You do not have access to the parent dashboard for this program.');
                    }
                }
            }
        }

        $subs = $program->programSubs;
        $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
        $hasSubActiveColumn = $subTable && $subTable->getColumn('is_active');
        $subStats = [];

        $rubricCounts = ProgramRubric::find()
            ->select(['program_sub', 'COUNT(*) AS cnt'])
            ->where(['program_id' => (int)$program->id])
            ->andWhere(['not', ['program_sub' => null]])
            ->groupBy(['program_sub'])
            ->asArray()
            ->all();
        $rubricsBySub = [];
        foreach($rubricCounts as $row){
            $rubricsBySub[(int)$row['program_sub']] = (int)$row['cnt'];
        }

        if($subs){
            foreach($subs as $sp){
                if($hasSubActiveColumn && (int)$sp->getAttribute('is_active') !== 1){
                    continue;
                }
                $subStats[(int)$sp->id] = $this->buildDashboardStats($program, $sp);
                $subStats[(int)$sp->id]['rubrics_count'] = $rubricsBySub[(int)$sp->id] ?? 0;
            }
        }

        return $this->render('manager-parent', [
            'role' => $role,
            'program' => $program,
            'dashboardStats' => $this->buildDashboardStats($program, null),
            'subs' => $subs,
            'subStats' => $subStats,
        ]);
    }

    public function actionImportParticipants($id, $sub = null)
    {
        if(!Yii::$app->user->identity->isManager) return false;

        $role = $this->findManagerRole($id, $sub);

        if(!$role){
            return;
        }

        $program = $role->program;
        $managerSub = null;

        if($program->has_sub == 1 && $sub){
            $managerSub = $role->programSub;
        }

        $enabledFields = ProgramRegistration::getProgramFields((int)$program->id);
        $requiredFields = ProgramRegistration::getProgramRequiredFields((int)$program->id);
        $fieldLabels = ProgramRegistration::availableRegistrationFields();
        $fieldTypes = ProgramRegistration::availableRegistrationFieldTypes();
        $showMemberMatric = ProgramRegistration::groupMemberShowMatric((int)$program->id);

        $importableFieldTypes = [
            'project_name' => 'Text',
            'project_desc' => 'Textarea',
            'participant_cat_local' => 'Integer option',
            'participant_cat_group' => 'Integer option',
            'participant_mode' => 'Integer option',
            'participant_cat_umk' => 'Integer option',
            'participant_program' => 'Integer option',
            'other_program' => 'Text',
            'program_sub' => 'Integer ID',
            'advisor_dropdown' => 'Integer option',
            'booth_number' => 'Text',
            'advisor' => 'Text',
            'institution' => 'Text',
            'contact_person' => 'Text',
            'contact_no' => 'Text',
            'contact_email' => 'Email',
            'group_code' => 'Text',
            'group_name' => 'Text',
            'nric' => 'Text',
            'competition_type' => 'Integer option',
            'participant_cat_program' => 'Integer option',
            'competition_cat_program' => 'Integer option',
        ];

        $importableFields = [];
        $unsupportedFields = [];
        foreach($enabledFields as $fieldName){
            if(array_key_exists($fieldName, $importableFieldTypes)){
                $importableFields[$fieldName] = [
                    'label' => array_key_exists($fieldName, $fieldLabels) ? $fieldLabels[$fieldName] : $fieldName,
                    'type' => $importableFieldTypes[$fieldName],
                    'required' => in_array($fieldName, $requiredFields, true),
                ];
            }else{
                $unsupportedFields[$fieldName] = [
                    'label' => array_key_exists($fieldName, $fieldLabels) ? $fieldLabels[$fieldName] : $fieldName,
                    'type' => array_key_exists($fieldName, $fieldTypes) ? $fieldTypes[$fieldName] : 'Input',
                    'required' => in_array($fieldName, $requiredFields, true),
                ];
            }
        }

        if($program->has_sub == 1 && !array_key_exists('program_sub', $importableFields)){
            $importableFields = array_merge([
                'program_sub' => [
                    'label' => array_key_exists('program_sub', $fieldLabels) ? $fieldLabels['program_sub'] : 'program_sub',
                    'type' => $importableFieldTypes['program_sub'],
                    'required' => true,
                ],
            ], $importableFields);
            unset($unsupportedFields['program_sub']);
        }

        $extraColumns = [];
        if(in_array('group_member', $enabledFields, true)){
            $extraColumns['member_names'] = [
                'label' => 'Additional Members',
                'type' => 'Text',
                'required' => in_array('group_member', $requiredFields, true),
            ];
            if($showMemberMatric){
                $extraColumns['member_matrics'] = [
                    'label' => 'Additional Member Matrics',
                    'type' => 'Text',
                    'required' => false,
                ];
            }
        }

        $sampleHeaders = array_merge(array_keys($importableFields), array_keys($extraColumns));
        $sampleRows = [];

        // Create sample data for a group with 3 members
        $baseSampleRow = [];
        foreach(array_keys($importableFields) as $fieldName){
            switch($fieldName){
                case 'project_name':
                    $baseSampleRow[] = 'Sample Project';
                    break;
                case 'project_desc':
                    $baseSampleRow[] = 'Short project description';
                    break;
                case 'participant_cat_group':
                case 'participant_cat_local':
                case 'participant_cat_umk':
                case 'participant_mode':
                case 'participant_program':
                case 'competition_type':
                case 'participant_cat_program':
                case 'competition_cat_program':
                case 'advisor_dropdown':
                    $baseSampleRow[] = '1';
                    break;
                case 'program_sub':
                    $baseSampleRow[] = $managerSub ? (string)$managerSub->id : '1';
                    break;
                case 'booth_number':
                case 'group_code':
                    $baseSampleRow[] = 'G01';
                    break;
                case 'group_name':
                    $baseSampleRow[] = 'Team Alpha';
                    break;
                case 'institution':
                    $baseSampleRow[] = 'UMK';
                    break;
                case 'contact_person':
                    $baseSampleRow[] = 'Aisyah';
                    break;
                case 'contact_no':
                    $baseSampleRow[] = '0123456789';
                    break;
                case 'contact_email':
                    $baseSampleRow[] = 'team@example.com';
                    break;
                case 'advisor':
                    $baseSampleRow[] = 'Dr. Advisor';
                    break;
                case 'nric':
                    $baseSampleRow[] = '900101101010';
                    break;
                default:
                    $baseSampleRow[] = 'Sample';
                    break;
            }
        }

        // Create sample rows for team members
        $teamMembers = [
            ['name' => 'John Doe', 'matric' => 'A25A1001'],
            ['name' => 'Jane Smith', 'matric' => 'A25A1002'],
            ['name' => 'Bob Johnson', 'matric' => 'A25A1003'],
        ];

        foreach($teamMembers as $index => $member){
            $sampleRow = $index === 0 ? $baseSampleRow : array_fill(0, count($baseSampleRow), '');
            if(array_key_exists('member_names', $extraColumns)){
                $sampleRow[] = $member['name'];
            }
            if(array_key_exists('member_matrics', $extraColumns)){
                $sampleRow[] = $member['matric'];
            }
            $sampleRows[] = $sampleRow;
        }

        if(Yii::$app->request->isPost){
            $csvFile = UploadedFile::getInstanceByName('csv_file');

            if(!$csvFile){
                Yii::$app->session->addFlash('error', 'Please upload a CSV file.');
                return $this->refresh();
            }

            $handle = $this->openImportedCsvFile($csvFile->tempName);
            if($handle === false){
                Yii::$app->session->addFlash('error', 'Unable to read the uploaded CSV file.');
                return $this->refresh();
            }

            $headers = fgetcsv($handle);
            if(!$headers){
                fclose($handle);
                Yii::$app->session->addFlash('error', 'The CSV file is empty.');
                return $this->refresh();
            }

            $headers = array_map(function($header){
                return trim((string)$header);
            }, $headers);

            $validColumns = array_merge(array_keys($importableFields), array_keys($extraColumns));
            $unknownHeaders = array_diff($headers, $validColumns);
            if($unknownHeaders){
                fclose($handle);
                Yii::$app->session->addFlash('error', 'Unknown CSV column(s): ' . implode(', ', $unknownHeaders));
                return $this->refresh();
            }

            // Read all rows and group by group_name
            $groupedData = [];
            $rowNo = 1;
            $lastGroupName = '';
            $lastProgramSub = '';
            while(($row = fgetcsv($handle)) !== false){
                $rowNo++;
                $rowAssoc = [];
                foreach($headers as $index => $header){
                    $rowAssoc[$header] = array_key_exists($index, $row) ? $this->normalizeImportedCsvValue($row[$index]) : '';
                }

                $hasContent = false;
                foreach($rowAssoc as $value){
                    if($value !== ''){
                        $hasContent = true;
                        break;
                    }
                }
                if(!$hasContent){
                    continue;
                }

                if((int)$program->has_sub === 1 && array_key_exists('program_sub', $rowAssoc)){
                    $programSub = trim((string)$rowAssoc['program_sub']);
                    if($programSub === '' && $lastProgramSub !== ''){
                        $rowAssoc['program_sub'] = $lastProgramSub;
                    }elseif($programSub !== ''){
                        $lastProgramSub = $programSub;
                    }
                }

                $groupName = isset($rowAssoc['group_name']) ? trim((string)$rowAssoc['group_name']) : '';
                if($groupName === ''){
                    if($lastGroupName === ''){
                        continue; // Skip rows without group_name (no previous row to refer to)
                    }
                    $groupName = $lastGroupName;
                    $rowAssoc['group_name'] = $groupName;
                }else{
                    $lastGroupName = $groupName;
                }

                if(!isset($groupedData[$groupName])){
                    $groupedData[$groupName] = [];
                }
                $groupedData[$groupName][] = $rowAssoc;
            }
            fclose($handle);

            if(empty($groupedData)){
                Yii::$app->session->addFlash('error', 'No valid data found in CSV file.');
                return $this->refresh();
            }

            $transaction = Yii::$app->db->beginTransaction();
            $created = 0;
            $skipped = 0;

            try{
                foreach($groupedData as $groupName => $groupRows){
                    // Use the first row for registration data
                    $firstRow = $groupRows[0];

                    // Create user account for the first member
                    $firstMemberMatric = isset($firstRow['member_matrics']) ? trim((string)$firstRow['member_matrics']) : '';
                    $firstMemberName = isset($firstRow['member_names']) ? $this->removeLeadingNameNumbering($firstRow['member_names']) : '';

                    if($firstMemberMatric === '' || $firstMemberName === ''){
                        throw new \RuntimeException('First member must have matric and name for group: ' . $groupName);
                    }

                    $user = User::findOrCreateImportedStudentAccount($firstMemberMatric, $firstMemberName);
                    if(!$user){
                        throw new \RuntimeException('Failed to create user account for matric: ' . $firstMemberMatric . '. Database error.');
                    }
                    $this->cleanExistingUserFullname($user, $firstMemberMatric);

                    foreach($groupRows as $memberRow){
                        $memberMatric = isset($memberRow['member_matrics']) ? trim((string)$memberRow['member_matrics']) : '';
                        if($memberMatric === ''){
                            continue;
                        }

                        $memberUser = User::findAccountForRegistration($memberMatric, User::dummyEmailForMatric($memberMatric));
                        if($memberUser){
                            $this->cleanExistingUserFullname($memberUser, $memberMatric);
                        }
                    }

                    $programSubValue = isset($firstRow['program_sub']) && $firstRow['program_sub'] !== '' ? (int)$firstRow['program_sub'] : null;
                    $existingRegistrationQuery = ProgramRegistration::find()->where([
                        'user_id' => $user->id,
                        'program_id' => (int)$program->id,
                    ]);
                    if($program->has_sub == 1){
                        $existingRegistrationQuery->andWhere(['program_sub' => $programSubValue]);
                    }
                    $existingRegistration = $existingRegistrationQuery->one();
                    if($existingRegistration){
                        $skipped++;
                        continue;
                    }

                    // Create program registration
                    $registration = new ProgramRegistration();
                    $registration->user_id = $user->id;
                    $registration->program_id = (int)$program->id;
                    $registration->program_sub = $programSubValue;
                    $registration->status = ProgramRegistration::STATUS_REGISTERED;
                    $registration->created_at = time();
                    $registration->updated_at = time();
                    $registration->submitted_at = new Expression('NOW()');

                    // Set registration fields from the first row
                    foreach(array_keys($importableFields) as $fieldName){
                        if(!array_key_exists($fieldName, $firstRow)){
                            continue;
                        }

                        $value = $firstRow[$fieldName];
                        $registration->$fieldName = ($value === '') ? null : $value;
                    }

                    // Set group_name from the group
                    $registration->group_name = $groupName;

                    if(!$registration->save(false)){
                        throw new \RuntimeException('Failed to save registration for group: ' . $groupName);
                    }

                    // Create participant role if not already present
                    $existingRole = UserRole::find()->where(['user_id' => $user->id, 'role_name' => 'participant'])->one();
                    if(!$existingRole){
                        $participantRole = new UserRole();
                        $participantRole->user_id = $user->id;
                        $participantRole->role_name = 'participant';
                        $participantRole->status = 10;
                        $participantRole->request_at = new Expression('NOW()');
                        $participantRole->approve_at = new Expression('NOW()');
                        if(!$participantRole->save(false)){
                            throw new \RuntimeException('Failed to create participant role for user: ' . $firstMemberMatric);
                        }
                    }

                    // Create member records for all members in the group
                    foreach($groupRows as $memberRow){
                        $memberMatric = isset($memberRow['member_matrics']) ? trim((string)$memberRow['member_matrics']) : '';
                        $memberName = isset($memberRow['member_names']) ? $this->removeLeadingNameNumbering($memberRow['member_names']) : '';

                        if($memberName === ''){
                            continue; // Skip empty member names
                        }

                        $member = new Member();
                        $member->program_reg_id = $registration->id;
                        $member->member_name = $memberName;
                        $member->member_matric = $memberMatric;
                        $member->save(false);
                    }

                    $created++;
                }

                $transaction->commit();
                $msg = $created . ' group registration(s) imported successfully.';
                if($skipped > 0){
                    $msg .= ' ' . $skipped . ' group(s) skipped (already registered).';
                }
                Yii::$app->session->addFlash('success', $msg);
                return $this->refresh();
            }catch(\Throwable $e){
                $transaction->rollBack();
                Yii::$app->session->addFlash('error', $e->getMessage());
            }
        }

        // Get available program subs for reference
        $subQuery = ProgramSub::find()->where(['program_id' => $program->id]);
        $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
        if($subTable && $subTable->getColumn('is_active')){
            $subQuery->andWhere(['is_active' => 1]);
        }
        $availableProgramSubs = $subQuery->orderBy('id')->all();

        return $this->render('import-participants', [
            'role' => $role,
            'program' => $program,
            'managerSub' => $managerSub,
            'importableFields' => $importableFields,
            'unsupportedFields' => $unsupportedFields,
            'extraColumns' => $extraColumns,
            'sampleHeaders' => $sampleHeaders,
            'sampleRows' => $sampleRows,
            'availableProgramSubs' => $availableProgramSubs,
        ]);
    }

    public function actionCleanupParticipants($id, $sub = null)
    {
        if(!Yii::$app->user->identity->isManager) return false;

        $role = $this->findManagerRole($id, $sub);

        if(!$role){
            return;
        }

        $program = $role->program;
        $managerSub = null;
        if($program->has_sub == 1 && $sub){
            $managerSub = $role->programSub;
        }

        $subQuery = ProgramSub::find()->where(['program_id' => $program->id]);
        $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
        if($subTable && $subTable->getColumn('is_active')){
            $subQuery->andWhere(['is_active' => 1]);
        }
        $availableProgramSubs = $subQuery->orderBy('id')->all();

        $selectedSub = $sub !== null ? (string)(int)$sub : 'all';
        if(Yii::$app->request->isPost){
            $selectedSub = (string)Yii::$app->request->post('program_sub', $selectedSub);
        }elseif(Yii::$app->request->get('program_sub') !== null){
            $selectedSub = (string)Yii::$app->request->get('program_sub');
        }

        if((int)$program->has_sub !== 1){
            $selectedSub = 'all';
        }

        $selectedSubId = $selectedSub === 'all' || $selectedSub === '' ? null : (int)$selectedSub;
        $stats = $this->buildParticipantCleanupStats((int)$program->id, $selectedSubId);

        if(Yii::$app->request->isPost && Yii::$app->request->post('cleanup_confirm') === 'DELETE'){
            if((int)$stats['registrations'] <= 0){
                Yii::$app->session->addFlash('error', 'No participant registrations found for the selected scope.');
                return $this->refresh();
            }
            if((int)$stats['jury_assignments'] > 0){
                Yii::$app->session->addFlash('error', 'Cannot delete participants because this scope has jury assignments. Remove jury assignments first.');
                return $this->refresh();
            }

            $transaction = Yii::$app->db->beginTransaction();
            try{
                $deleted = $this->deleteParticipantRegistrationsForScope((int)$program->id, $selectedSubId);
                $transaction->commit();
                Yii::$app->session->addFlash('success', 'Deleted ' . $deleted['registrations'] . ' participant registration(s). User accounts were not deleted.');
                return $this->redirect(['cleanup-participants', 'id' => $program->id, 'sub' => $managerSub ? $managerSub->id : null, 'program_sub' => $selectedSub]);
            }catch(\Throwable $e){
                $transaction->rollBack();
                Yii::$app->session->addFlash('error', $e->getMessage());
            }
        }elseif(Yii::$app->request->isPost){
            Yii::$app->session->addFlash('error', 'Type DELETE to confirm participant cleanup.');
        }

        return $this->render('cleanup-participants', [
            'role' => $role,
            'program' => $program,
            'managerSub' => $managerSub,
            'availableProgramSubs' => $availableProgramSubs,
            'selectedSub' => $selectedSub,
            'stats' => $stats,
        ]);
    }

    protected function buildDashboardStats($program, $programSub = null)
    {
        $programId = (int)$program->id;
        $subId = $programSub ? (int)$programSub->id : null;

        $registrationQuery = ProgramRegistration::find()->where(['program_id' => $programId]);
        if($subId){
            $registrationQuery->andWhere(['program_sub' => $subId]);
        }

        $registrationTotal = (clone $registrationQuery)->count();
        $registeredTotal = (clone $registrationQuery)->andWhere(['status' => ProgramRegistration::STATUS_REGISTERED])->count();
        $completeTotal = (clone $registrationQuery)->andWhere(['status' => ProgramRegistration::STATUS_COMPLETE])->count();

        $assignmentQuery = JuryAssign::find()->alias('j')
            ->innerJoin('program_reg r', 'r.id = j.reg_id')
            ->where(['r.program_id' => $programId]);
        if($subId){
            $assignmentQuery->andWhere(['r.program_sub' => $subId]);
        }

        $assignmentTotal = (clone $assignmentQuery)->count();
        $assignmentComplete = (clone $assignmentQuery)->andWhere(['j.status' => 20])->count();

        $rubricQuery = ProgramRubric::find()->where(['program_id' => $programId]);
        if($subId){
            $rubricQuery->andWhere(['program_sub' => $subId]);
        }else{
            $rubricQuery->andWhere(['program_sub' => null]);
        }
        $rubricCount = (clone $rubricQuery)->count();
        $rubricIds = (clone $rubricQuery)->select(['rubric_id'])->column();
        $sessionCount = $rubricIds ? RubricJudgingSession::find()->where(['rubric_id' => $rubricIds])->count() : 0;

        $achievementCount = ProgramAchievement::find()->where(['program_id' => $programId])->count();

        $awardedQuery = ParticipantAchieve::find()->alias('pa')
            ->innerJoin('program_reg r', 'r.id = pa.program_reg_id')
            ->where(['r.program_id' => $programId]);
        if($subId){
            $awardedQuery->andWhere(['r.program_sub' => $subId]);
        }
        $awardedCount = (clone $awardedQuery)->count();

        $fieldEnabledCount = ProgramRegField::find()
            ->where(['program_id' => $programId, 'is_enabled' => 1])
            ->count();
        $fieldRequiredCount = ProgramRegField::find()
            ->where(['program_id' => $programId, 'is_required' => 1])
            ->count();

        $memberQuery = Member::find()->alias('m')
            ->innerJoin('program_reg r', 'r.id = m.program_reg_id')
            ->where(['r.program_id' => $programId]);
        if($subId){
            $memberQuery->andWhere(['r.program_sub' => $subId]);
        }
        $memberTotal = (clone $memberQuery)->count();

        $juryAppQuery = JuryApplication::find()->where(['program_id' => $programId]);
        if($subId){
            $juryAppQuery->andWhere(['program_sub_id' => $subId]);
        }else{
            $juryAppQuery->andWhere(['program_sub_id' => null]);
        }
        $juryAppTotal = (clone $juryAppQuery)->count();
        $juryAppNew = (clone $juryAppQuery)->andWhere(['status' => 0])->count();
        $juryAppApproved = (clone $juryAppQuery)->andWhere(['status' => 10])->count();

        return [
            'registrations_total' => (int)$registrationTotal,
            'registrations_registered' => (int)$registeredTotal,
            'registrations_complete' => (int)$completeTotal,
            'members_total' => (int)$memberTotal,
            'assignments_total' => (int)$assignmentTotal,
            'assignments_complete' => (int)$assignmentComplete,
            'rubrics_count' => (int)$rubricCount,
            'rubric_sessions_count' => (int)$sessionCount,
            'achievements_count' => (int)$achievementCount,
            'awarded_count' => (int)$awardedCount,
            'jury_applications_total' => (int)$juryAppTotal,
            'jury_applications_new' => (int)$juryAppNew,
            'jury_applications_approved' => (int)$juryAppApproved,
            'fields_enabled_count' => (int)$fieldEnabledCount,
            'fields_required_count' => (int)$fieldRequiredCount,
            'registration_status' => ((int)$program->getAttribute('reg_closed') === 1) ? 'Closed' : 'Open',
            'date_start' => $program->date_start,
            'date_end' => $program->date_end,
        ];
    }

    public function actionManagerSession($id, $sub = null){
        $session = Yii::$app->session;
        //print_r($session->get('keep-data'));die();
        if(!Yii::$app->user->identity->isManager) return false;

        $role = $this->findManagerRole($id, $sub);
        if(!$role){
            throw new ForbiddenHttpException('No access');
        }
        $programSub = null;
        $program = $role->program;

        if($role->program->has_sub == 1){
            if($sub){
                $programSub = $role->programSub;
            }else{
                throw new NotFoundHttpException('Please provide sub program.');
            }
        }

        if($role && $role->program){
        
            $searchModel = new ManagerSessionSearch();
            $searchModel->program_id = $role->program_id;
            $searchModel->program_sub = $sub;
            $dataProvider = $searchModel->search($this->request->queryParams);
    
            return $this->render('manager-session', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'role' => $role,
                'programSub' => $programSub
            ]);
        }

        
    }

    protected function findManagerProgramScope($id, $sub = null)
    {
        $role = $this->findManagerRole($id, $sub);
        if(!$role){
            throw new ForbiddenHttpException('No access');
        }

        $programSub = null;
        $program = $role->program;
        if((int)$program->has_sub === 1){
            if($sub){
                $programSub = $role->programSub;
            }else{
                throw new NotFoundHttpException('Please provide sub program.');
            }
        }

        return [$role, $program, $programSub];
    }

    protected function findManagerRole($id, $sub = null)
    {
        $isGeneralManager = false;
        if(!Yii::$app->user->isGuest){
            $isGeneralManager = UserRole::find()->where([
                'user_id' => Yii::$app->user->identity->id,
                'role_name' => 'general-manager',
                'status' => 10,
            ])->exists();
        }

        if(!Yii::$app->user->isGuest && (Yii::$app->user->identity->isSuperadmin || $isGeneralManager)){
            $program = Program::findOne((int)$id);
            if(!$program || !$this->isActiveModel($program)){
                return null;
            }

            if((int)$program->has_sub === 1){
                if(!$sub){
                    return null;
                }
                $programSub = ProgramSub::findOne(['id' => (int)$sub, 'program_id' => (int)$program->id]);
                if(!$programSub || !$this->isActiveModel($programSub)){
                    return null;
                }
            }

            return new UserRole([
                'user_id' => Yii::$app->user->identity->id,
                'role_name' => 'manager',
                'program_id' => (int)$program->id,
                'program_sub' => $sub ? (int)$sub : null,
                'status' => 10,
            ]);
        }

        return UserRole::findOne([
            'program_id' => $id,
            'user_id' => Yii::$app->user->identity->id,
            'role_name' => 'manager',
            'program_sub' => $sub,
            'status' => 10,
        ]);
    }

    protected function isActiveModel($model)
    {
        if($model->hasAttribute('is_active')){
            return (int)$model->getAttribute('is_active') === 1;
        }
        if($model->hasAttribute('status')){
            return (int)$model->getAttribute('status') === 10;
        }
        return true;
    }

    protected function getScopedProgramRubrics($program, $programSub = null)
    {
        $query = ProgramRubric::find()
            ->alias('pr')
            ->where(['pr.program_id' => (int)$program->id]);

        if($programSub){
            $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
            $hasSubActiveColumn = $subTable && $subTable->getColumn('is_active');

            if($hasSubActiveColumn && (int)$programSub->getAttribute('is_active') !== 1){
                $query->andWhere('0=1');
            }else{
                $query->andWhere(['pr.program_sub' => (int)$programSub->id]);
            }
        }else{
            $query->andWhere(['or', ['pr.program_sub' => null], ['pr.program_sub' => 0]]);
        }

        return $query->all();
    }

    protected function ensureApprovedJuryApplicationForUser($user, $programId, $programSubId = null, $judgingSessionId = null)
    {
        return $this->ensureJuryPipelineForUser($user, $programId, $programSubId, $judgingSessionId, 10);
    }

    protected function buildManualJuryApplicationProgramScopeOptions()
    {
        $programQuery = Program::find()->orderBy(['program_abbr' => SORT_ASC, 'program_name' => SORT_ASC]);
        $programTable = Yii::$app->db->schema->getTableSchema(Program::tableName());
        if($programTable && $programTable->getColumn('is_active')){
            $programQuery->andWhere(['is_active' => 1]);
        }

        $programs = $programQuery->all();
        $options = [];
        foreach($programs as $program){
            $programLabel = $program->program_abbr ?: $program->program_name;
            if((int)$program->has_sub === 1){
                $subQuery = $program->getProgramSubs()->orderBy(['sub_name' => SORT_ASC]);
                $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
                if($subTable && $subTable->getColumn('is_active')){
                    $subQuery->andWhere(['is_active' => 1]);
                }
                foreach($subQuery->all() as $sub){
                    $subLabel = $sub->sub_abbr ?: $sub->sub_name;
                    $options['s:' . $sub->id] = $programLabel . ' / ' . $subLabel;
                }
            }else{
                $options['p:' . $program->id] = $programLabel;
            }
        }
        return $options;
    }

    protected function buildManualJuryApplicationSessionOptions()
    {
        $activeProgramIds = Program::find()->select(['id']);
        $programTable = Yii::$app->db->schema->getTableSchema(Program::tableName());
        if($programTable && $programTable->getColumn('is_active')){
            $activeProgramIds->andWhere(['is_active' => 1]);
        }

        $programRubricQuery = ProgramRubric::find()
            ->where(['program_rubric.program_id' => $activeProgramIds])
            ->with(['program', 'programSub']);

        $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
        if($subTable && $subTable->getColumn('is_active')){
            $programRubricQuery
                ->leftJoin(['ps' => ProgramSub::tableName()], 'ps.id = program_rubric.program_sub')
                ->andWhere(['or', ['program_rubric.program_sub' => null], ['program_rubric.program_sub' => 0], ['ps.is_active' => 1]]);
        }

        $programRubrics = $programRubricQuery->all();
        if(!$programRubrics){
            return [];
        }

        $rubricIds = array_values(array_unique(array_map(function($programRubric){
            return (int)$programRubric->rubric_id;
        }, $programRubrics)));

        $sessions = RubricJudgingSession::find()
            ->where(['rubric_id' => $rubricIds])
            ->orderBy(['datetime_start' => SORT_ASC, 'session_name' => SORT_ASC])
            ->all();

        $options = [];
        foreach($sessions as $session){
            $scopeLabels = [];
            foreach($programRubrics as $programRubric){
                if((int)$programRubric->rubric_id !== (int)$session->rubric_id){
                    continue;
                }
                $programLabel = $programRubric->program ? ($programRubric->program->program_abbr ?: $programRubric->program->program_name) : ('Program #' . $programRubric->program_id);
                if($programRubric->programSub){
                    $programLabel .= ' / ' . ($programRubric->programSub->sub_abbr ?: $programRubric->programSub->sub_name);
                }
                $scopeLabels[] = $programLabel;
            }

            $label = $this->formatJudgingSessionOptionText($session);
            if($scopeLabels){
                $label = implode(', ', array_unique($scopeLabels)) . ' - ' . $label;
            }
            $options[(int)$session->id] = $label;
        }

        return $options;
    }

    protected function buildManualJuryApplicationSessionScopeMap()
    {
        $map = [];
        $programRubricQuery = ProgramRubric::find()->with(['program', 'programSub']);

        $programTable = Yii::$app->db->schema->getTableSchema(Program::tableName());
        if($programTable && $programTable->getColumn('is_active')){
            $programRubricQuery
                ->innerJoin(['p' => Program::tableName()], 'p.id = program_rubric.program_id')
                ->andWhere(['p.is_active' => 1]);
        }

        $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
        if($subTable && $subTable->getColumn('is_active')){
            $programRubricQuery
                ->leftJoin(['ps' => ProgramSub::tableName()], 'ps.id = program_rubric.program_sub')
                ->andWhere(['or', ['program_rubric.program_sub' => null], ['program_rubric.program_sub' => 0], ['ps.is_active' => 1]]);
        }

        $programRubrics = $programRubricQuery->all();
        foreach($programRubrics as $programRubric){
            $sessions = RubricJudgingSession::find()->where(['rubric_id' => (int)$programRubric->rubric_id])->all();
            $scopeKey = $programRubric->program_sub ? ('s:' . (int)$programRubric->program_sub) : ('p:' . (int)$programRubric->program_id);
            foreach($sessions as $session){
                $map[(int)$session->id][] = $scopeKey;
            }
        }

        foreach($map as $sessionId => $scopeKeys){
            $map[$sessionId] = array_values(array_unique($scopeKeys));
        }

        return $map;
    }

    protected function parseManualJuryApplicationProgramScope($scope)
    {
        $scope = trim((string)$scope);
        if(strpos($scope, 's:') === 0){
            $sub = ProgramSub::findOne((int)substr($scope, 2));
            if(!$sub){
                return null;
            }
            return [
                'program_id' => (int)$sub->program_id,
                'program_sub_id' => (int)$sub->id,
            ];
        }

        if(strpos($scope, 'p:') === 0){
            $program = Program::findOne((int)substr($scope, 2));
            if(!$program){
                return null;
            }
            return [
                'program_id' => (int)$program->id,
                'program_sub_id' => null,
            ];
        }

        return null;
    }

    protected function isJudgingSessionValidForScope($judgingSessionId, $programId, $programSubId = null)
    {
        $session = RubricJudgingSession::findOne((int)$judgingSessionId);
        if(!$session){
            return false;
        }

        $query = ProgramRubric::find()->where([
            'program_id' => (int)$programId,
            'rubric_id' => (int)$session->rubric_id,
        ]);
        if($programSubId === null){
            $query->andWhere(['or', ['program_sub' => null], ['program_sub' => 0]]);
        }else{
            $query->andWhere(['program_sub' => (int)$programSubId]);
        }

        return $query->exists();
    }

    protected function findApprovedAppliedJuryData(array $applicationIds, $programId, $programSubId = null)
    {
        if(!$applicationIds){
            return [
                'users' => [],
                'sessionMap' => [],
            ];
        }

        $applicationQuery = JuryApplication::find()->alias('ja')
            ->innerJoin(['jp' => JuryProfile::tableName()], 'jp.id = ja.jury_profile_id')
            ->innerJoin(['u' => User::tableName()], 'u.id = jp.user_id')
            ->where([
                'ja.id' => $applicationIds,
                'ja.program_id' => (int)$programId,
                'ja.status' => 10,
            ])
            ->with(['juryProfile.user'])
            ->orderBy(['jp.fullname' => SORT_ASC]);
        if($programSubId === null){
            $applicationQuery->andWhere(['or', ['ja.program_sub_id' => null], ['ja.program_sub_id' => 0]]);
        }else{
            $applicationQuery->andWhere(['ja.program_sub_id' => (int)$programSubId]);
        }
        $applications = $applicationQuery->all();

        $users = [];
        $sessionMap = [];
        foreach($applications as $application){
            if($application->juryProfile && $application->juryProfile->user){
                $userId = (int)$application->juryProfile->user->id;
                $users[$userId] = $application->juryProfile->user;
                if($application->judging_session_id){
                    $sessionMap[$userId] = (int)$application->judging_session_id;
                }
            }
        }

        return [
            'users' => array_values($users),
            'sessionMap' => $sessionMap,
        ];
    }

    protected function buildJuryAssignmentLoad(array $juryUsers, $programId, $programSubId = null, $stage = 0)
    {
        $userIds = array_map(function($user){
            return (int)$user->id;
        }, $juryUsers);

        $load = [];
        foreach($userIds as $userId){
            $load[$userId] = [
                'count' => 0,
                'regs' => [],
            ];
        }

        if(!$userIds){
            return $load;
        }

        $assignmentQuery = JuryAssign::find()->alias('j')
            ->innerJoinWith(['registration r'], false)
            ->where([
                'j.user_id' => $userIds,
                'j.stage' => (int)$stage,
                'r.program_id' => (int)$programId,
            ])
            ->orderBy(['j.id' => SORT_ASC]);
        if($programSubId === null){
            $assignmentQuery->andWhere(['or', ['r.program_sub' => null], ['r.program_sub' => 0]]);
        }else{
            $assignmentQuery->andWhere(['r.program_sub' => (int)$programSubId]);
        }
        $assignments = $assignmentQuery->all();

        foreach($assignments as $assignment){
            $userId = (int)$assignment->user_id;
            if(!isset($load[$userId])){
                continue;
            }
            $load[$userId]['count']++;
            $load[$userId]['regs'][(int)$assignment->reg_id] = true;
        }

        return $load;
    }

    protected function pickLeastAssignedJuryUser(array $juryUsers, array $juryLoad, $registrationId, $stage = 0)
    {
        $available = [];
        foreach($juryUsers as $juryUser){
            $userId = (int)$juryUser->id;
            if(isset($juryLoad[$userId]['regs'][(int)$registrationId])){
                continue;
            }
            $available[] = $juryUser;
        }

        if(!$available){
            return null;
        }

        usort($available, function($a, $b) use ($juryLoad){
            $aCount = $juryLoad[(int)$a->id]['count'] ?? 0;
            $bCount = $juryLoad[(int)$b->id]['count'] ?? 0;
            if($aCount === $bCount){
                return strcasecmp((string)$a->fullname, (string)$b->fullname);
            }
            return $aCount <=> $bCount;
        });

        return $available[0];
    }

    protected function ensureJuryRoleProfileRequirementForUser($user, $programId, $programSubId = null, $judgingSessionId = null, array $profileData = [])
    {
        if(!$user || !$user->id){
            throw new \RuntimeException('Jury user not found.');
        }

        $role = UserRole::find()->where([
            'user_id' => (int)$user->id,
            'role_name' => 'jury',
        ])->one();
        if(!$role){
            $role = new UserRole();
            $role->user_id = (int)$user->id;
            $role->role_name = 'jury';
            $role->status = 10;
            $role->approve_at = new Expression('NOW()');
        }else if((int)$role->status !== 10){
            $role->status = 10;
            $role->approve_at = new Expression('NOW()');
        }
        if(!$role->save()){
            throw new \RuntimeException('Unable to save jury role: ' . implode('; ', $role->getFirstErrors()));
        }

        $profile = JuryProfile::find()->where(['user_id' => (int)$user->id])->one();
        if(!$profile){
            $profile = new JuryProfile();
            $profile->user_id = (int)$user->id;
            $profile->fullname = (string)$user->fullname;
            $profile->category = !empty($profileData['category']) ? (string)$profileData['category'] : 'General';
            $profile->created_at = time();
        }
        $profile->fullname = (string)$user->fullname;
        if(!$profile->category){
            $profile->category = !empty($profileData['category']) ? (string)$profileData['category'] : 'General';
        }
        if(array_key_exists('phone', $profileData) && $profile->phone === null){
            $profile->phone = $profileData['phone'] !== '' ? $profileData['phone'] : null;
        }
        if(array_key_exists('institution', $profileData) && $profile->institution === null){
            $profile->institution = $profileData['institution'] !== '' ? $profileData['institution'] : null;
        }
        if(array_key_exists('designation', $profileData) && $profile->designation === null){
            $profile->designation = $profileData['designation'] !== '' ? $profileData['designation'] : null;
        }
        if(array_key_exists('address', $profileData) && $profile->address === null){
            $profile->address = $profileData['address'] !== '' ? $profileData['address'] : null;
        }
        $profile->updated_at = time();
        if(!$profile->save()){
            throw new \RuntimeException('Unable to save jury profile: ' . implode('; ', $profile->getFirstErrors()));
        }

        $existingApp = JuryApplication::find()->where([
            'jury_profile_id' => (int)$profile->id,
            'program_id' => (int)$programId,
            'program_sub_id' => $programSubId ?: null,
            'judging_session_id' => $judgingSessionId ?: null,
        ])->one();

        $requirement = JuryRequirement::find()->where([
            'program_id' => (int)$programId,
            'program_sub_id' => $programSubId ?: null,
            'judging_session_id' => $judgingSessionId ?: null,
        ])->one();

        if(!$requirement){
            $requirement = new JuryRequirement();
            $requirement->program_id = (int)$programId;
            $requirement->program_sub_id = $programSubId ?: null;
            $requirement->judging_session_id = $judgingSessionId ?: null;
            $requirement->is_required = 0;
            $requirement->is_active = 0;
            $requirement->created_at = time();
        }

        $requiredLimit = (int)JuryApplication::find()->where([
            'program_id' => (int)$programId,
            'program_sub_id' => $programSubId ?: null,
            'judging_session_id' => $judgingSessionId ?: null,
        ])->count();
        if(!$existingApp){
            $requiredLimit++;
        }

        if($requirement->jury_limit === null || (int)$requirement->jury_limit < $requiredLimit){
            $requirement->jury_limit = $requiredLimit;
        }
        $requirement->updated_at = time();
        if(!$requirement->save()){
            throw new \RuntimeException('Unable to save jury requirement: ' . implode('; ', $requirement->getFirstErrors()));
        }

        return $profile;
    }

    protected function ensureJuryPipelineForUser($user, $programId, $programSubId = null, $judgingSessionId = null, $applicationStatus = 10, array $profileData = [])
    {
        $profile = $this->ensureJuryRoleProfileRequirementForUser($user, $programId, $programSubId, $judgingSessionId, $profileData);
        $app = JuryApplication::find()->where([
            'jury_profile_id' => (int)$profile->id,
            'program_id' => (int)$programId,
            'program_sub_id' => $programSubId ?: null,
            'judging_session_id' => $judgingSessionId ?: null,
        ])->one();

        if(!$app){
            $app = new JuryApplication();
            $app->jury_profile_id = (int)$profile->id;
            $app->program_id = (int)$programId;
            $app->program_sub_id = $programSubId ?: null;
            $app->judging_session_id = $judgingSessionId ?: null;
            $app->created_at = time();
        }

        $app->declaration_accepted = 1;
        $app->status = (int)$applicationStatus;

        if(!$app->save()){
            throw new \RuntimeException('Unable to save jury application: ' . implode('; ', $app->getFirstErrors()));
        }

        return $app;
    }

    private function clearSession($session){
        $session->remove('keep-data');
        $session->remove('users');
        $session->remove('rubric_id');
        $session->remove('judging_session_id');
        $session->remove('date_start');
        $session->remove('date_end');
        $session->remove('location');
        $session->remove('note');
        $session->remove('link');
        $session->remove('keep_open');
        $session->remove('keep_data');
    }

    public function actionJudgingSessionListJson($rubric_id, $program_id = null, $sub = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $rubricId = (int)$rubric_id;
        if($rubricId <= 0){
            return [];
        }

        $programId = $program_id !== null ? (int)$program_id : null;
        $programSubId = $sub !== null ? (int)$sub : null;
        if($programId){
            $rubricQuery = ProgramRubric::find()->where([
                'program_id' => $programId,
                'rubric_id' => $rubricId,
            ]);

            if($programSubId){
                $rubricQuery->andWhere(['program_sub' => $programSubId]);
            }else{
                $rubricQuery->andWhere(['or', ['program_sub' => null], ['program_sub' => 0]]);
            }

            if(!$rubricQuery->exists()){
                return ['results' => []];
            }
        }

        $sessions = RubricJudgingSession::find()
            ->where(['rubric_id' => $rubricId])
            ->orderBy(['datetime_start' => SORT_ASC, 'session_name' => SORT_ASC])
            ->all();

        $out = [];
        foreach($sessions as $s){
            $out[] = ['id' => (int)$s->id, 'text' => $this->formatJudgingSessionOptionText($s)];
        }
        return ['results' => $out];
    }

    public function actionManagerAnalysis($id, $sub = null){
        if(!Yii::$app->user->identity->isManager) return false;
        $role = $this->findManagerRole($id, $sub);
        if(!$role){
            throw new ForbiddenHttpException('No access');
        }
        $programSub = null;
        $program = $role->program;
        $rubricsQuery = $program->getProgramRubrics()->joinWith(['rubric r']);
        $achievementsQuery = ProgramAchievement::find()->where(['program_id' => $role->program_id]);

        if($role->program->has_sub == 1){
            if($sub){
                $programSub = $role->programSub;
                $rubricsQuery = $program->getProgramRubricsSub($sub)->joinWith(['rubric r']);
                $achievementsQuery->andWhere(['program_sub' => $sub]);
            }else{
                throw new NotFoundHttpException('Please provide sub program.');
            }
        }else{
            $rubricsQuery->andWhere(['or',
                [ProgramRubric::tableName() . '.program_sub' => null],
                [ProgramRubric::tableName() . '.program_sub' => 0],
            ]);
            $achievementsQuery->andWhere(['or', ['program_sub' => null], ['program_sub' => 0]]);
        }

        $programRubricTable = Yii::$app->db->schema->getTableSchema(ProgramRubric::tableName());
        if($programRubricTable && $programRubricTable->getColumn('is_active')){
            $rubricsQuery->andWhere([ProgramRubric::tableName() . '.is_active' => 1]);
        }else if($programRubricTable && $programRubricTable->getColumn('status')){
            $rubricsQuery->andWhere([ProgramRubric::tableName() . '.status' => 10]);
        }

        $rubricTable = Yii::$app->db->schema->getTableSchema(Rubric::tableName());
        if($rubricTable && $rubricTable->getColumn('is_active')){
            $rubricsQuery->andWhere(['r.is_active' => 1]);
        }else if($rubricTable && $rubricTable->getColumn('status')){
            $rubricsQuery->andWhere(['r.status' => 10]);
        }

        $rubrics = $rubricsQuery->orderBy([ProgramRubric::tableName() . '.id' => SORT_ASC])->all();
        $achievements = $achievementsQuery->orderBy(['name' => SORT_ASC])->all();
        $hasWinnerTitleSelection = $this->hasParticipantAchieveWinnerTitleColumn()
            && $this->hasProgramWinnerTitleAchievementColumn();
        $winnerTitlesByAchievement = [];
        if($hasWinnerTitleSelection && $achievements){
            $achievementIds = ArrayHelper::getColumn($achievements, 'id');
            $this->ensureWinnerTitleRows($achievementIds);
            $winnerTitles = ProgramWinnerTitle::find()
                ->where(['achievement_id' => $achievementIds])
                ->orderBy(['achievement_id' => SORT_ASC, 'winner_order' => SORT_ASC])
                ->all();
            foreach($winnerTitles as $winnerTitle){
                $winnerTitlesByAchievement[(int)$winnerTitle->achievement_id][] = $winnerTitle;
            }
        }

        $post = $this->request->post();
        if($this->request->isPost && (string)($post['action_type'] ?? '') === 'analysis-achievement-bulk'){
            Yii::$app->session->set('managerAnalysisOpenCard', 'achievement-form');
            $assignment = (array)($post['achievement_form'] ?? []);
            $allowedAchievementIds = $achievements ? array_map('intval', ArrayHelper::getColumn($achievements, 'id')) : [];
            $conflicts = [];
            $modelsToSave = [];
            $modelsToDelete = [];
            $seenParticipants = [];
            $seenWinnerTitles = [];

            foreach($assignment as $achievementId => $achievementRows){
                $achievementId = (int)$achievementId;
                if($achievementId <= 0 || !in_array($achievementId, $allowedAchievementIds, true)){
                    continue;
                }

                $rows = (array)($achievementRows['rows'] ?? []);
                foreach($rows as $rowKey => $row){
                    $row = (array)$row;
                    $registrationId = (int)($row['program_reg_id'] ?? 0);
                    $winnerTitleId = (int)($row['winner_title_id'] ?? 0);
                    $participantAchieve = null;

                    if(is_numeric($rowKey) && (int)$rowKey > 0){
                        $participantAchieve = ParticipantAchieve::findOne([
                            'id' => (int)$rowKey,
                            'achieve_id' => $achievementId,
                        ]);
                        if(!$participantAchieve){
                            $conflicts[] = 'One achievement row could not be found.';
                            continue;
                        }
                        if((int)($row['remove'] ?? 0) === 1){
                            if(!$participantAchieve->registration || (int)$participantAchieve->registration->program_id !== (int)$role->program_id){
                                throw new ForbiddenHttpException('No access');
                            }
                            if($role->program->has_sub == 1 && (int)$participantAchieve->registration->program_sub !== (int)$sub){
                                throw new ForbiddenHttpException('No access');
                            }
                            $modelsToDelete[] = $participantAchieve;
                            continue;
                        }
                    }else if($registrationId <= 0 && $winnerTitleId <= 0){
                        continue;
                    }

                    if($registrationId <= 0){
                        $conflicts[] = 'Please select a participant group for each achievement row.';
                        continue;
                    }

                    $registration = ProgramRegistration::findOne($registrationId);
                    if(!$registration || (int)$registration->program_id !== (int)$role->program_id){
                        throw new ForbiddenHttpException('No access');
                    }
                    if($role->program->has_sub == 1 && (int)$registration->program_sub !== (int)$sub){
                        throw new ForbiddenHttpException('No access');
                    }

                    if(!$participantAchieve){
                        $participantAchieve = new ParticipantAchieve([
                            'achieve_id' => $achievementId,
                        ]);
                        if($participantAchieve->hasAttribute('achieved_at')){
                            $participantAchieve->achieved_at = time();
                        }
                    }

                    $seenParticipantKey = $achievementId . ':' . $registrationId;
                    if(isset($seenParticipants[$seenParticipantKey])){
                        $conflicts[] = $registration->participantText . ' is selected more than once for the same achievement.';
                        continue;
                    }
                    $seenParticipants[$seenParticipantKey] = true;

                    $existingParticipantAchieve = ParticipantAchieve::find()
                        ->where([
                            'program_reg_id' => $registrationId,
                            'achieve_id' => $achievementId,
                        ]);
                    if(!$participantAchieve->isNewRecord){
                        $existingParticipantAchieve->andWhere(['<>', 'id', (int)$participantAchieve->id]);
                    }
                    if($existingParticipantAchieve->exists()){
                        $conflicts[] = $registration->participantText . ' already has this achievement.';
                        continue;
                    }

                    $participantAchieve->program_reg_id = $registrationId;
                    if($hasWinnerTitleSelection){
                        if($winnerTitleId <= 0){
                            $participantAchieve->winner_title_id = null;
                        }else{
                            $winnerTitle = ProgramWinnerTitle::find()
                                ->where(['id' => $winnerTitleId, 'achievement_id' => $achievementId])
                                ->one();
                            if(!$winnerTitle){
                                $participantAchieve->winner_title_id = null;
                            }else{
                                $seenTitleKey = $achievementId . ':' . $winnerTitleId;
                                if(isset($seenWinnerTitles[$seenTitleKey])){
                                    $conflicts[] = 'The same winner title is selected more than once for one achievement.';
                                    continue;
                                }
                                $seenWinnerTitles[$seenTitleKey] = true;

                                $existing = ParticipantAchieve::find()
                                    ->where([
                                        'achieve_id' => $achievementId,
                                        'winner_title_id' => (int)$winnerTitle->id,
                                    ]);
                                if(!$participantAchieve->isNewRecord){
                                    $existing->andWhere(['<>', 'id', (int)$participantAchieve->id]);
                                }
                                $existing = $existing->one();
                                if($existing){
                                    $existingParticipant = $existing->registration ? $existing->registration->participantText : 'another participant';
                                    $titleName = trim((string)$winnerTitle->title_name);
                                    if($titleName === ''){
                                        $titleName = 'Winner ' . (int)$winnerTitle->winner_order;
                                    }
                                    $conflicts[] = $titleName . ' already assigned to ' . $existingParticipant . '.';
                                }else{
                                    $participantAchieve->winner_title_id = (int)$winnerTitle->id;
                                }
                            }
                        }
                    }

                    $modelsToSave[] = $participantAchieve;
                }
            }

            if($conflicts){
                Yii::$app->session->addFlash('error', implode("\n", array_unique($conflicts)));
            }else{
                $transaction = Yii::$app->db->beginTransaction();
                try{
                    foreach($modelsToDelete as $participantAchieve){
                        if($participantAchieve->delete() === false){
                            throw new \RuntimeException('Unable to remove achievement row.');
                        }
                    }
                    foreach($modelsToSave as $participantAchieve){
                        if(!$participantAchieve->save()){
                            throw new \RuntimeException(implode("\n", $participantAchieve->getFirstErrors()));
                        }
                    }
                    $transaction->commit();
                    Yii::$app->session->addFlash('success', count($modelsToSave) . ' achievement row(s) saved. ' . count($modelsToDelete) . ' row(s) removed.');
                }catch(\Throwable $e){
                    $transaction->rollBack();
                    Yii::$app->session->addFlash('error', $e->getMessage());
                }
            }

            return $this->refresh();
        }

        $firstRubric = null;
        if($rubrics){
            $firstRubric = $rubrics[0]->rubric_id;
        }
        $firstStage = null;
        $stages = $program->programStages;
            if($stages){
                $firstStage = $stages[0]->id;
            }
        

        if($role && $role->program){
            $model = new JuryAssign();
            //cari ada stage tak
            
        
            $searchModel = new ManagerAnalysisSearch();
            $searchModel->program_id = $role->program_id;
            $searchModel->program_sub = $sub;

            $allowedRubricIds = $rubrics ? ArrayHelper::getColumn($rubrics, 'rubric_id') : [];

            $queryParams = $this->request->queryParams;
            $formName = $searchModel->formName();
            $requestedRubric = $queryParams[$formName]['rubric'] ?? null;
            $requestedRubric = ($requestedRubric === '' || $requestedRubric === null) ? null : (int)$requestedRubric;

            if($requestedRubric !== null && in_array($requestedRubric, $allowedRubricIds, true)){
                $searchModel->rubric = $requestedRubric;
            }else{
                $searchModel->rubric = $firstRubric;
            }

            $searchModel->stage = $firstStage;
            //TODO: set klu ada get request yg pilih lain
            
            $queryParams[$formName]['rubric'] = $searchModel->rubric;
            $dataProvider = $searchModel->search($queryParams);

            $selectedRubric = Rubric::findOne($searchModel->rubric);
    
            return $this->render('manager-analysis', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'role' => $role,
                'model' => $model,
                'programSub' => $programSub,
                'rubrics' => $rubrics,
                'achievements' => $achievements,
                'stages' =>$stages,
                'selectedRubric' => $selectedRubric,
                'hasWinnerTitleSelection' => $hasWinnerTitleSelection,
                'winnerTitlesByAchievement' => $winnerTitlesByAchievement,
            ]);
        }

        
    }

    public function actionAchievementSummary()
    {
        if(!Yii::$app->user->identity->isAdminJury) return false;

        $achievements = ProgramAchievement::find()
            ->with(['program', 'programSub'])
            ->joinWith(['program p', 'programSub ps'])
            ->orderBy([
                'p.id' => SORT_ASC,
                'ps.id' => SORT_ASC,
                ProgramAchievement::tableName() . '.name' => SORT_ASC,
            ])
            ->all();

        $assignedWinners = [];
        $participantAchievements = ParticipantAchieve::find()
            ->with(['registration.user', 'winnerTitle'])
            ->joinWith(['registration r'])
            ->where(['>', 'r.status', 0])
            ->all();

        foreach($participantAchievements as $pa){
            if(!$pa->achieve_id || !$pa->registration){
                continue;
            }

            $aid = (int)$pa->achieve_id;
            if(!isset($assignedWinners[$aid])){
                $assignedWinners[$aid] = [];
            }

            $registration = $pa->registration;
            $participantName = \yii\helpers\Html::encode((string)$registration->participantText);
            $groupName = trim((string)($registration->group_name ?? ''));
            if($groupName !== ''){
                $participantName = \yii\helpers\Html::tag('span', \yii\helpers\Html::encode($groupName), ['class' => 'badge bg-secondary me-2']) . $participantName;
            }
            if($pa->winnerTitle && trim((string)$pa->winnerTitle->title_name) !== ''){
                $participantName .= ' <span class="text-muted">(' . \yii\helpers\Html::encode((string)$pa->winnerTitle->title_name) . ')</span>';
            }

            $assignedWinners[$aid][] = $participantName;
        }

        $rows = [];
        foreach($achievements as $achievement){
            $aid = (int)$achievement->id;
            $programName = $achievement->program ? ($achievement->program->program_abbr ?: $achievement->program->program_name) : '-';
            if($achievement->programSub){
                $programName .= ' / ' . $achievement->programSub->sub_name;
            }

            $participants = $assignedWinners[$aid] ?? [];
            $rows[] = [
                'program' => $programName,
                'name' => (string)$achievement->name,
                'winner_count' => (int)$achievement->winner_count,
                'assigned_count' => count($participants),
                'participants' => $participants,
            ];
        }

        return $this->render('achievement-summary', [
            'rows' => $rows,
        ]);
    }

    /**
     * Creates a new ProgramRegistration model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ProgramRegistration();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProgramRegistration model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionManagerAnalysisSuggestion($id, $sub = null)
    {
        if(!Yii::$app->user->identity->isManager) return false;

        $role = $this->findManagerRole($id, $sub);
        if(!$role){
            throw new ForbiddenHttpException('No access');
        }

        if($this->request->isPost && (string)$this->request->post('action_type') === 'accept-suggestions'){
            $accepted = (array)$this->request->post('accept', []);
            $successCount = 0;
            $skippedCount = 0;

            $transaction = Yii::$app->db->beginTransaction();
            try{
                foreach($accepted as $achievementId => $titleMap){
                    $achievementId = (int)$achievementId;
                    if(!$achievementId){
                        continue;
                    }

                    $achievement = ProgramAchievement::findOne([
                        'id' => $achievementId,
                        'program_id' => (int)$role->program_id,
                    ]);
                    if(!$achievement){
                        $skippedCount++;
                        continue;
                    }
                    if($role->program->has_sub == 1){
                        if((int)$achievement->program_sub !== (int)$sub){
                            $skippedCount++;
                            continue;
                        }
                    }

                    foreach((array)$titleMap as $winnerTitleId => $programRegId){
                        $winnerTitleId = (int)$winnerTitleId;
                        $programRegId = (int)$programRegId;
                        if(!$programRegId){
                            continue;
                        }

                        $registration = ProgramRegistration::findOne([
                            'id' => $programRegId,
                            'program_id' => (int)$role->program_id,
                        ]);
                        if(!$registration){
                            $skippedCount++;
                            continue;
                        }
                        if($role->program->has_sub == 1 && (int)$registration->program_sub !== (int)$sub){
                            $skippedCount++;
                            continue;
                        }

                        $participantAchieve = ParticipantAchieve::find()
                            ->where([
                                'program_reg_id' => $programRegId,
                                'achieve_id' => $achievementId,
                            ])
                            ->one();

                        if(!$participantAchieve){
                            $participantAchieve = new ParticipantAchieve([
                                'program_reg_id' => $programRegId,
                                'achieve_id' => $achievementId,
                                'achieved_at' => time(),
                            ]);
                        }

                        if($winnerTitleId > 0 && $this->hasParticipantAchieveWinnerTitleColumn()){
                            $winnerTitle = ProgramWinnerTitle::findOne([
                                'id' => $winnerTitleId,
                                'achievement_id' => $achievementId,
                            ]);
                            $participantAchieve->winner_title_id = $winnerTitle ? $winnerTitleId : null;
                        }

                        if($participantAchieve->isNewRecord){
                            if($participantAchieve->save()){
                                $successCount++;
                            }else{
                                $skippedCount++;
                            }
                        }else{
                            // already exists; only update winner title if possible
                            if($this->hasParticipantAchieveWinnerTitleColumn()){
                                $participantAchieve->save(false, ['winner_title_id']);
                            }
                            $skippedCount++;
                        }
                    }
                }

                $transaction->commit();
                if($successCount > 0){
                    Yii::$app->session->addFlash('success', 'Accepted suggestion(s): ' . $successCount . '.');
                }
                if($skippedCount > 0){
                    Yii::$app->session->addFlash('info', 'Skipped/unchanged suggestion(s): ' . $skippedCount . '.');
                }
            }catch(\Throwable $e){
                $transaction->rollBack();
                Yii::$app->session->addFlash('error', $e->getMessage());
            }

            return $this->refresh();
        }

        $programSub = null;
        if($role->program->has_sub == 1){
            if($sub){
                $programSub = $role->programSub;
            }else{
                throw new NotFoundHttpException('Please provide sub program.');
            }
        }

        $rubrics = $role->program->programRubrics;
        $stages = $role->program->programStages;
        if($programSub){
            $rubrics = $role->program->getProgramRubricsSub($sub)->all();
        }

        $firstRubric = null;
        if($rubrics){
            $firstRubric = $rubrics[0]->rubric_id;
        }
        $firstStage = null;
        if($stages){
            $firstStage = $stages[0]->id;
        }

        $searchModel = new ManagerAnalysisSearch();
        $searchModel->program_id = $role->program_id;
        $searchModel->program_sub = $sub;

        // Apply defaults like manager-analysis page (so suggestion works even when user didn't pick rubric/stage)
        $queryParams = $this->request->queryParams;
        $rubricFromGet = $queryParams['ManagerAnalysisSearch']['rubric'] ?? null;
        $stageFromGet = $queryParams['ManagerAnalysisSearch']['stage'] ?? null;
        $searchModel->rubric = $rubricFromGet !== null && $rubricFromGet !== '' ? (int)$rubricFromGet : $firstRubric;
        $searchModel->stage = $stageFromGet !== null && $stageFromGet !== '' ? (int)$stageFromGet : $firstStage;

        $dataProvider = $searchModel->search($queryParams);
        $models = $dataProvider->getModels();

        $registrationIds = [];
        foreach($models as $m){
            if(!empty($m->id)){
                $registrationIds[] = (int)$m->id;
            }
        }
        $registrationIds = array_values(array_unique(array_filter($registrationIds)));

        $achievementsQuery = ProgramAchievement::find()->where(['program_id' => (int)$role->program_id]);
        if($programSub){
            $achievementsQuery->andWhere(['program_sub' => (int)$programSub->id]);
        }else{
            $achievementsQuery->andWhere(['or', ['program_sub' => null], ['program_sub' => 0]]);
        }
        $achievements = $achievementsQuery->orderBy(['name' => SORT_ASC])->all();

        $suggestionsByAchievement = [];

        // Map rubric_item_id => rubric_item.colum_ans so we can read the answer value from rubric_answer
        $rubricItemIds = [];
        foreach($achievements as $a){
            if(!empty($a->rubric_item_id)){
                $rubricItemIds[] = (int)$a->rubric_item_id;
            }
        }
        $rubricItemIds = array_values(array_unique(array_filter($rubricItemIds)));

        $rubricItemColumnById = [];
        if($rubricItemIds){
            $items = RubricItem::find()->where(['id' => $rubricItemIds])->all();
            foreach($items as $it){
                $col = trim((string)$it->colum_ans);
                if($col !== ''){
                    $rubricItemColumnById[(int)$it->id] = $col;
                }
            }
        }

        $globallySuggestedRegistrationIds = [];

        $winnerTitlesByAchievement = [];
        if($achievements){
            $achievementIds = array_map(function($a){
                return (int)$a->id;
            }, $achievements);
            $winnerTitles = ProgramWinnerTitle::find()
                ->where(['achievement_id' => $achievementIds])
                ->orderBy(['achievement_id' => SORT_ASC, 'winner_order' => SORT_ASC])
                ->all();
            foreach($winnerTitles as $wt){
                $winnerTitlesByAchievement[(int)$wt->achievement_id][] = $wt;
            }
        }

        foreach($achievements as $achievement){
            $aid = (int)$achievement->id;

            $alreadyAwardedIds = [];
            if($registrationIds){
                $alreadyAwardedIds = ParticipantAchieve::find()
                    ->select(['program_reg_id'])
                    ->where([
                        'achieve_id' => $aid,
                        'program_reg_id' => $registrationIds,
                    ])
                    ->column();
                $alreadyAwardedIds = array_fill_keys(array_map('intval', $alreadyAwardedIds), true);
            }

            $itemId = (int)($achievement->rubric_item_id ?? 0);
            $recommendColumn = ($itemId && isset($rubricItemColumnById[$itemId])) ? $rubricItemColumnById[$itemId] : null;

            $rows = [];
            foreach($models as $m){
                $avg = (float)($m->purata ?? 0);

                $recommendScore = 0.0;
                if($recommendColumn){
                    $assignments = $m->juriesCompleted ?? [];
                    $sum = 0.0;
                    $count = 0;

                    foreach($assignments as $as){
                        $answer = $as->rubricAnswer;
                        if($answer && $answer->hasAttribute($recommendColumn)){
                            $val = $answer->$recommendColumn;
                            if($val !== null && $val !== ''){
                                $sum += (float)$val;
                                $count++;
                            }
                        }
                    }

                    if($count > 0){
                        $recommendScore = $sum / $count;
                    }
                }

                $total = $avg + $recommendScore;

                $rows[] = [
                    'reg_id' => (int)$m->id,
                    'participant' => (string)$m->participantText,
                    'group_name' => (string)($m->group_name ?? ''),
                    'avg_score' => $avg,
                    'recommend_score' => $recommendScore,
                    'total' => $total,
                ];
            }

            usort($rows, function($a, $b){
                if($a['total'] === $b['total']){
                    return 0;
                }
                return ($a['total'] < $b['total']) ? 1 : -1;
            });

            $winnerTitles = $winnerTitlesByAchievement[$aid] ?? [];
            if(!$winnerTitles){
                $fallbackCount = max(0, (int)($achievement->winner_count ?? 0));
                $fallbackCount = $fallbackCount > 0 ? $fallbackCount : 1;
                for($i = 1; $i <= $fallbackCount; $i++){
                    $winnerTitles[] = new ProgramWinnerTitle([
                        'id' => 0,
                        'achievement_id' => $aid,
                        'winner_order' => $i,
                        'title_name' => 'Winner ' . $i,
                    ]);
                }
            }

            $selected = [];
            $cursor = 0;
            foreach($winnerTitles as $wt){
                $picked = null;
                while($cursor < count($rows)){
                    $candidate = $rows[$cursor];
                    $cursor++;
                    $rid = (int)$candidate['reg_id'];
                    if(isset($alreadyAwardedIds[$rid])){
                        continue;
                    }
                    if(isset($globallySuggestedRegistrationIds[$rid])){
                        continue;
                    }
                    $picked = $candidate;
                    $globallySuggestedRegistrationIds[$rid] = true;
                    break;
                }

                if(!$picked){
                    continue;
                }

                $picked['winner_title_id'] = (int)($wt->id ?? 0);
                $picked['winner_title_name'] = (string)($wt->title_name ?? '');
                $picked['winner_order'] = (int)($wt->winner_order ?? 0);
                $selected[] = $picked;
            }

            $rows = $selected;

            $suggestionsByAchievement[$aid] = $rows;
        }

        return $this->render('manager-analysis-suggestion', [
            'searchModel' => $searchModel,
            'achievements' => $achievements,
            'suggestionsByAchievement' => $suggestionsByAchievement,
            'programSub' => $programSub,
            'role' => $role,
        ]);
    }

    /**
     * Deletes an existing ProgramRegistration model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if(!Yii::$app->user->identity->isAdmin && !Yii::$app->user->identity->isManager) return false;

        if(!$this->request->isPost){
            throw new \yii\web\MethodNotAllowedHttpException('Method Not Allowed.');
        }

        $model = $this->findModel($id);

        if($this->hasJuryAssignments($id)){
            Yii::$app->session->addFlash('error', "Could not delete this registration because it has been assigned to juries");
            return $this->redirect(['view', 'id' => $id]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            Member::deleteAll(['program_reg_id' => $id]);
            Mentor::deleteAll(['program_reg_id' => $id]);
            $model->delete();
            $transaction->commit();
            Yii::$app->session->addFlash('success', "Registration Deleted");
        } catch(\yii\db\IntegrityException $e) {
            $transaction->rollBack();
            Yii::$app->session->addFlash('error', "Could not delete this registration because other records are related to it");
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->redirect(['index']);
    }

    public function actionDeleteRegistration($id)
    {
        //kita delete member & mentor shj
        $model = $this->findModel($id);
        $program_id = $model->program_id;
        $program_sub = $model->program_sub;

        if($this->hasJuryAssignments($id)){
            Yii::$app->session->addFlash('error', "Could not delete this registration because it has been assigned to juries");
            return $this->redirect(['manager-view', 'id' => $id, 'sub' => $program_sub]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            Member::deleteAll(['program_reg_id' => $id]);
            Mentor::deleteAll(['program_reg_id' => $id]);
            $model->delete();
            $transaction->commit();
            //$this->findModel($id)->delete();
            Yii::$app->session->addFlash('success', "Registration Deleted");
            //return $this->redirect(['index']);
        } catch(\yii\db\IntegrityException $e) {
            throw new \yii\web\ForbiddenHttpException('Could not delete this registration, other record related to it (jury or achievement)');
        }

        return $this->redirect(['manager', 'id' => $program_id, 'sub' => $program_sub]);

    }

    protected function hasJuryAssignments($id)
    {
        return JuryAssign::find()->where(['reg_id' => $id])->exists();
    }

    protected function hasParticipantAchieveWinnerTitleColumn()
    {
        $table = Yii::$app->db->schema->getTableSchema(ParticipantAchieve::tableName());
        return $table && $table->getColumn('winner_title_id');
    }

    protected function hasProgramWinnerTitleAchievementColumn()
    {
        $table = Yii::$app->db->schema->getTableSchema(ProgramWinnerTitle::tableName());
        return $table && $table->getColumn('achievement_id') && $table->getColumn('winner_order');
    }

    protected function ensureWinnerTitleRows($achievementIds)
    {
        $achievements = ProgramAchievement::find()
            ->where(['id' => array_unique(array_map('intval', $achievementIds))])
            ->all();

        foreach($achievements as $achievement){
            $winnerCount = max(0, (int)$achievement->winner_count);
            if($winnerCount <= 0){
                continue;
            }

            $existingOrders = ProgramWinnerTitle::find()
                ->select('winner_order')
                ->where(['achievement_id' => (int)$achievement->id])
                ->column();
            $existingOrders = array_map('intval', $existingOrders);

            for($i = 1; $i <= $winnerCount; $i++){
                if(in_array($i, $existingOrders, true)){
                    continue;
                }

                $winnerTitle = new ProgramWinnerTitle([
                    'achievement_id' => (int)$achievement->id,
                    'winner_order' => $i,
                    'title_name' => '',
                ]);
                $winnerTitle->save(false);
            }
        }
    }

    protected function findRegistrationsByImportedGroupName($programId, $programSubId, $groupName)
    {
        $baseQuery = ProgramRegistration::find()
            ->where(['program_id' => (int)$programId])
            ->andWhere(['in', 'status', [ProgramRegistration::STATUS_REGISTERED, ProgramRegistration::STATUS_COMPLETE]]);

        if($programSubId !== null){
            $baseQuery->andWhere(['program_sub' => (int)$programSubId]);
        }

        $exactQuery = clone $baseQuery;
        $exactMatches = $exactQuery
            ->andWhere('LOWER(TRIM(group_name)) = :group_name', [':group_name' => strtolower(trim((string)$groupName))])
            ->all();

        if($exactMatches){
            return $exactMatches;
        }

        $normalizedGroupName = $this->normalizeGroupNameForImportMatch($groupName);
        if($normalizedGroupName === ''){
            return [];
        }

        $candidates = $baseQuery
            ->andWhere(['not', ['group_name' => null]])
            ->all();
        $matches = [];
        foreach($candidates as $candidate){
            if($this->normalizeGroupNameForImportMatch($candidate->group_name) === $normalizedGroupName){
                $matches[] = $candidate;
            }
        }

        return $matches;
    }

    protected function findRegistrationsByImportedGroupNameOrRange($programId, $programSubId, $groupName)
    {
        $range = $this->parseImportedGroupNameRange($groupName);
        if(!$range){
            return $this->findRegistrationsByImportedGroupName($programId, $programSubId, $groupName);
        }

        $baseQuery = ProgramRegistration::find()
            ->where(['program_id' => (int)$programId])
            ->andWhere(['in', 'status', [ProgramRegistration::STATUS_REGISTERED, ProgramRegistration::STATUS_COMPLETE]])
            ->andWhere(['not', ['group_name' => null]]);

        if($programSubId !== null){
            $baseQuery->andWhere(['program_sub' => (int)$programSubId]);
        }

        $matches = [];
        foreach($baseQuery->all() as $candidate){
            $candidateParts = $this->parseImportedGroupNameEndpoint($candidate->group_name);
            if(!$candidateParts){
                continue;
            }
            if($candidateParts['prefix'] !== $range['prefix'] || $candidateParts['suffix'] !== $range['suffix']){
                continue;
            }
            if($candidateParts['number'] < $range['start'] || $candidateParts['number'] > $range['end']){
                continue;
            }
            $matches[] = $candidate;
        }

        usort($matches, function($a, $b){
            $aParts = $this->parseImportedGroupNameEndpoint($a->group_name);
            $bParts = $this->parseImportedGroupNameEndpoint($b->group_name);
            return ($aParts['number'] ?? 0) <=> ($bParts['number'] ?? 0);
        });

        return $matches;
    }

    protected function parseImportedGroupNameRange($groupName)
    {
        $parts = preg_split('/\s*-\s*/', trim((string)$groupName));
        if(!$parts || count($parts) !== 2 || trim($parts[0]) === '' || trim($parts[1]) === ''){
            return null;
        }

        $start = $this->parseImportedGroupNameEndpoint($parts[0]);
        $end = $this->parseImportedGroupNameEndpoint($parts[1]);
        if(!$start || !$end){
            return null;
        }
        if($start['prefix'] !== $end['prefix'] || $start['suffix'] !== $end['suffix']){
            return null;
        }
        if($start['number'] > $end['number']){
            return null;
        }

        return [
            'prefix' => $start['prefix'],
            'suffix' => $start['suffix'],
            'start' => $start['number'],
            'end' => $end['number'],
        ];
    }

    protected function parseImportedGroupNameEndpoint($groupName)
    {
        $normalized = $this->normalizeGroupNameForImportMatch($groupName);
        if(!preg_match('/^([a-z]*)(\d+)([a-z]*)$/', $normalized, $matches)){
            return null;
        }

        return [
            'prefix' => $matches[1],
            'number' => (int)$matches[2],
            'suffix' => $matches[3],
        ];
    }

    protected function normalizeGroupNameForImportMatch($groupName)
    {
        $groupName = strtolower(trim((string)$groupName));
        $normalized = preg_replace('/[^a-z0-9]+/', '', $groupName);
        return preg_replace_callback('/\d+/', function($matches){
            return (string)(int)$matches[0];
        }, $normalized);
    }

    protected function removeLeadingNameNumbering($name)
    {
        $name = $this->normalizeImportedCsvValue($name);
        return trim(preg_replace('/^(?:\d+\s*(?:[\.\)\-:]\s*|\s+))+/', '', $name));
    }

    protected function normalizeImportedCsvValue($value)
    {
        $value = trim((string)$value);
        return str_replace([
            "\xEF\xBF\xBD",
            "\xE2\x80\x98",
            "\xE2\x80\x99",
            "\x91",
            "\x92",
        ], "'", $value);
    }

    protected function openImportedCsvFile($path)
    {
        $content = file_get_contents($path);
        if($content === false){
            return false;
        }

        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $handle = fopen('php://temp', 'r+');
        if($handle === false){
            return false;
        }

        fwrite($handle, $content);
        rewind($handle);
        return $handle;
    }

    protected function findParticipantRegistrationIdsForScope($programId, $programSubId = null)
    {
        $query = ProgramRegistration::find()
            ->select(['id'])
            ->where(['program_id' => (int)$programId]);

        if($programSubId !== null){
            $query->andWhere(['program_sub' => (int)$programSubId]);
        }

        return array_map('intval', $query->column());
    }

    protected function buildParticipantCleanupStats($programId, $programSubId = null)
    {
        $registrationIds = $this->findParticipantRegistrationIdsForScope($programId, $programSubId);
        if(!$registrationIds){
            return [
                'registrations' => 0,
                'members' => 0,
                'mentors' => 0,
                'jury_assignments' => 0,
                'achievements' => 0,
            ];
        }

        return [
            'registrations' => count($registrationIds),
            'members' => (int)Member::find()->where(['program_reg_id' => $registrationIds])->count(),
            'mentors' => (int)Mentor::find()->where(['program_reg_id' => $registrationIds])->count(),
            'jury_assignments' => (int)JuryAssign::find()->where(['reg_id' => $registrationIds])->count(),
            'achievements' => (int)ParticipantAchieve::find()->where(['program_reg_id' => $registrationIds])->count(),
        ];
    }

    protected function deleteParticipantRegistrationsForScope($programId, $programSubId = null)
    {
        $registrationIds = $this->findParticipantRegistrationIdsForScope($programId, $programSubId);
        if(!$registrationIds){
            return [
                'registrations' => 0,
            ];
        }

        if(JuryAssign::find()->where(['reg_id' => $registrationIds])->exists()){
            throw new \RuntimeException('Cannot delete participants because this scope has jury assignments. Remove jury assignments first.');
        }

        ParticipantAchieve::deleteAll(['program_reg_id' => $registrationIds]);
        Member::deleteAll(['program_reg_id' => $registrationIds]);
        Mentor::deleteAll(['program_reg_id' => $registrationIds]);
        ProgramRegistration::deleteAll(['id' => $registrationIds]);

        return [
            'registrations' => count($registrationIds),
        ];
    }

    protected function buildRegistrationParticipantExportRows(ProgramRegistration $registration)
    {
        $rows = [];
        foreach($registration->members as $member){
            $name = trim((string)$member->member_name);
            $matric = trim((string)$member->member_matric);
            if($name === '' && $matric === ''){
                continue;
            }
            $rows[] = [
                'name' => $name,
                'matric' => $matric,
            ];
        }

        if($rows){
            return $rows;
        }

        return [[
            'name' => $registration->user ? trim((string)$registration->user->fullname) : trim((string)$registration->contact_person),
            'matric' => $registration->user ? trim((string)$registration->user->matric) : '',
        ]];
    }

    protected function formatJudgingSessionExportText(RubricJudgingSession $session)
    {
        $parts = [];
        if($session->session_name !== ''){
            $parts[] = $session->session_name;
        }
        if($session->datetime_start){
            $parts[] = 'Start: ' . $session->datetime_start;
        }
        if($session->datetime_end){
            $parts[] = 'End: ' . $session->datetime_end;
        }
        if($session->location){
            $parts[] = 'Location: ' . $session->location;
        }
        $modes = RubricJudgingSession::listMode();
        if($session->mode && isset($modes[(int)$session->mode])){
            $parts[] = 'Mode: ' . $modes[(int)$session->mode];
        }

        return implode(' | ', $parts);
    }

    protected function formatJudgingSessionOptionText(RubricJudgingSession $session)
    {
        $parts = [];
        if($session->session_name !== ''){
            $parts[] = $session->session_name;
        }

        $range = $this->formatJudgingSessionDateRange($session->datetime_start, $session->datetime_end);
        if($range !== ''){
            $parts[] = $range;
        }
        if($session->location){
            $parts[] = $session->location;
        }

        return implode(' | ', $parts);
    }

    protected function formatJudgingSessionDateRange($startValue, $endValue)
    {
        if(!$startValue && !$endValue){
            return '';
        }
        if(!$startValue){
            return date('d M Y, h:i A', strtotime($endValue));
        }
        if(!$endValue){
            return date('d M Y, h:i A', strtotime($startValue));
        }

        $startTime = strtotime($startValue);
        $endTime = strtotime($endValue);
        if(date('Y-m-d', $startTime) === date('Y-m-d', $endTime)){
            return date('d M Y, h:i A', $startTime) . ' - ' . date('h:i A', $endTime);
        }

        return date('d M Y, h:i A', $startTime) . ' - ' . date('d M Y, h:i A', $endTime);
    }

    protected function cleanExistingUserFullname(User $user, $matric)
    {
        $cleanFullname = $this->removeLeadingNameNumbering($user->fullname);
        if($cleanFullname === (string)$user->fullname){
            return;
        }

        $user->fullname = $cleanFullname;
        if(!$user->save(false)){
            throw new \RuntimeException('Failed to update user fullname for matric: ' . $matric . '. Database error.');
        }
    }

    /**
     * Finds the ProgramRegistration model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ProgramRegistration the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProgramRegistration::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findUser($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist. (user)');
    }

    protected function findAssignment($id)
    {
        if (($model = JuryAssign::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    

    protected function findAssignmentByProgram($u, $p, $s = null)
    {
        $model = JuryAssign::find()->alias('a')
        ->joinWith(['registration r'])
        ->where(['a.user_id' => $u, 'r.program_id' => $p]);
        if($s){
            $model = $model->andWhere(['r.program_sub' => $s]);
        }
        $model = $model->one();

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findAchievement($id)
    {
        if (($model = ParticipantAchieve::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
