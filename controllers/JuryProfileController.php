<?php

namespace app\controllers;

use app\models\JuryProfile;
use app\models\JuryManualCreateForm;
use app\models\JuryProfileSearch;
use app\models\JuryApplication;
use app\models\JuryAssign;
use app\models\User;
use app\models\UserRole;
use Yii;
use yii\db\Expression;
use yii\db\IntegrityException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class JuryProfileController extends Controller
{
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

    public function actionIndex()
    {
        if(!Yii::$app->user->identity->isAdminJury) return false;

        $searchModel = new JuryProfileSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pager' => [
                'class' => 'yii\\bootstrap5\\LinkPager',
            ],
        ]);
    }

    public function actionImport()
    {
        if(!Yii::$app->user->identity->isAdminJury) return false;

        return $this->render('import');
    }

    public function actionCreate()
    {
        if(!Yii::$app->user->identity->isAdminJury) return false;

        $model = new JuryManualCreateForm();

        if($this->request->isPost && $model->load($this->request->post()) && $model->validate()){
            $tx = Yii::$app->db->beginTransaction();
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
                    if($user->phone === null || $user->phone === ''){
                        $user->phone = $model->phone;
                    }
                    if($user->institution === null || $user->institution === ''){
                        $user->institution = $model->institution;
                    }
                    if(!$user->save(false)){
                        throw new \RuntimeException('Unable to update existing user.');
                    }
                }

                $profile = JuryProfile::find()->where(['user_id' => (int)$user->id])->one();
                if(!$profile){
                    $profile = new JuryProfile();
                    $profile->user_id = (int)$user->id;
                    $profile->created_at = time();
                }

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

                $role = UserRole::findOne(['user_id' => (int)$user->id, 'role_name' => 'jury']);
                if(!$role){
                    $role = new UserRole();
                    $role->user_id = (int)$user->id;
                    $role->role_name = 'jury';
                }
                $role->status = 10;
                $role->approve_at = new Expression('NOW()');
                if(!$role->save()){
                    throw new \RuntimeException('Unable to save jury role: ' . implode('; ', $role->getFirstErrors()));
                }

                $tx->commit();
                Yii::$app->session->addFlash('success', ($createdUser ? 'Jury user created' : 'Existing user updated') . ' and jury profile saved.');
                return $this->redirect(['index']);
            }catch(\Throwable $e){
                $tx->rollBack();
                Yii::$app->session->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionImportCsv()
    {
        if(!Yii::$app->user->identity->isAdminJury) return false;

        if(!Yii::$app->request->isPost){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $file = UploadedFile::getInstanceByName('csv_file');
        if(!$file){
            Yii::$app->session->addFlash('error', 'No file uploaded.');
            return $this->redirect(['import']);
        }

        $handle = fopen($file->tempName, 'r');
        if(!$handle){
            Yii::$app->session->addFlash('error', 'Could not read uploaded file.');
            return $this->redirect(['import']);
        }

        $header = fgetcsv($handle);
        if(!$header){
            fclose($handle);
            Yii::$app->session->addFlash('error', 'Empty CSV.');
            return $this->redirect(['import']);
        }

        $header = array_map(function($h){
            return strtolower(trim((string)$h));
        }, $header);

        $required = ['email', 'fullname', 'category'];
        foreach($required as $col){
            if(!in_array($col, $header, true)){
                fclose($handle);
                Yii::$app->session->addFlash('error', 'Missing required column: ' . $col);
                return $this->redirect(['import']);
            }
        }

        $idx = array_flip($header);

        $createdUser = 0;
        $createdProfile = 0;
        $updatedProfile = 0;
        $createdRole = 0;
        $skipped = 0;
        $errors = 0;

        $tx = Yii::$app->db->beginTransaction();
        try{
            while(($row = fgetcsv($handle)) !== false){
                $email = trim((string)($row[$idx['email']] ?? ''));
                $fullname = trim((string)($row[$idx['fullname']] ?? ''));
                $category = trim((string)($row[$idx['category']] ?? ''));

                if($email === '' || $fullname === '' || $category === ''){
                    $skipped++;
                    continue;
                }

                $phone = isset($idx['phone']) ? trim((string)($row[$idx['phone']] ?? '')) : '';
                $institution = isset($idx['institution']) ? trim((string)($row[$idx['institution']] ?? '')) : '';
                $designation = isset($idx['designation']) ? trim((string)($row[$idx['designation']] ?? '')) : '';
                $address = isset($idx['address']) ? trim((string)($row[$idx['address']] ?? '')) : '';

                $user = User::find()->where(['email' => $email])->one();
                if(!$user){
                    $user = new User();
                    $user->scenario = 'create';
                    $user->email = $email;
                    $user->username = $email;
                    $user->fullname = $fullname;
                    $user->status = User::STATUS_ACTIVE;
                    $user->is_student = 0;
                    $user->is_internal = 0;
                    $user->generateAuthKey();
                    $user->setPassword($email);

                    if(!$user->save()){
                        $errors++;
                        continue;
                    }
                    $createdUser++;
                }else{
                    if(!$user->fullname){
                        $user->fullname = $fullname;
                        $user->save(false, ['fullname']);
                    }
                }

                $profile = JuryProfile::find()->where(['user_id' => $user->id])->one();
                $isNew = false;
                if(!$profile){
                    $profile = new JuryProfile();
                    $profile->user_id = (int)$user->id;
                    $profile->created_at = time();
                    $isNew = true;
                }

                $profile->fullname = (string)$user->fullname;
                $profile->category = $category;
                $profile->phone = $phone !== '' ? $phone : null;
                $profile->institution = $institution !== '' ? $institution : null;
                $profile->designation = $designation !== '' ? $designation : null;
                $profile->address = $address !== '' ? $address : null;
                $profile->updated_at = time();

                if(!$profile->save()){
                    $errors++;
                    continue;
                }

                if($isNew){
                    $createdProfile++;
                }else{
                    $updatedProfile++;
                }

                $role = UserRole::findOne(['user_id' => $user->id, 'role_name' => 'jury']);
                if(!$role){
                    $role = new UserRole();
                    $role->user_id = (int)$user->id;
                    $role->role_name = 'jury';
                    $role->status = 10;
                    $role->approve_at = new Expression('NOW()');
                    if($role->save()){
                        $createdRole++;
                    }else{
                        $errors++;
                    }
                }
            }

            fclose($handle);
            $tx->commit();
        }catch(\Throwable $e){
            fclose($handle);
            $tx->rollBack();
            throw $e;
        }

        if($createdUser > 0){
            Yii::$app->session->addFlash('success', 'Created ' . $createdUser . ' user(s).');
        }
        if($createdProfile > 0){
            Yii::$app->session->addFlash('success', 'Created ' . $createdProfile . ' jury profile(s).');
        }
        if($updatedProfile > 0){
            Yii::$app->session->addFlash('info', 'Updated ' . $updatedProfile . ' jury profile(s).');
        }
        if($createdRole > 0){
            Yii::$app->session->addFlash('success', 'Added jury role for ' . $createdRole . ' user(s).');
        }
        if($skipped > 0){
            Yii::$app->session->addFlash('warning', 'Skipped ' . $skipped . ' row(s).');
        }
        if($errors > 0){
            Yii::$app->session->addFlash('error', 'Failed ' . $errors . ' row(s).');
        }

        return $this->redirect(['index']);
    }

    public function actionDelete($id)
    {
        if(!Yii::$app->user->identity->isAdminJury) return false;

        $model = $this->findModel($id);

        if($this->hasJuryAssignments($model->user_id)){
            Yii::$app->session->addFlash('error', 'Could not delete this jury profile because the user has jury assignments.');
            return $this->redirect(['index']);
        }

        $tx = Yii::$app->db->beginTransaction();
        try{
            JuryApplication::deleteAll(['jury_profile_id' => $model->id]);
            UserRole::deleteAll(['user_id' => $model->user_id, 'role_name' => 'jury']);
            $model->delete();
            $tx->commit();
            Yii::$app->session->addFlash('success', 'Jury profile deleted.');
        }catch(IntegrityException $e){
            $tx->rollBack();
            Yii::$app->session->addFlash('error', 'Could not delete this jury profile because related records exist.');
        }

        return $this->redirect(['index']);
    }

    protected function hasJuryAssignments($userId)
    {
        return JuryAssign::find()->where(['user_id' => $userId])->exists();
    }

    protected function findModel($id)
    {
        if(($model = JuryProfile::findOne((int)$id)) !== null){
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
