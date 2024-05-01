<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use backend\models\Data;
use common\models\User;
use backend\models\Entrepreneur;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'language'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'error', 'import-data'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        /* Yii::$app->language = 'ms-my';
        $cookie = new yii\web\Cookie([
            'name' =>'lang',
            'value' =>'ms-my'
        ]);
        
        Yii::$app->getResponse()->getCookies()->add($cookie);
         */
        
        return $this->render('index');
    }
    
    /* public function actionImportData()
    {

            $list = Data::find()
         //   ->where(['<=', 'id', 92])
            
            ->all()
            
            ;
            foreach($list as $b){
                $transaction = Yii::$app->db->beginTransaction();
                try {
                $user = new User();
                //mula2 creaate user
                if($b->nric){
                    $user->username = $b->nric;
                    $pass =  $b->nric;
                }else if($b->phone){
                    $user->username = $b->phone;
                    $pass = $b->phone;
                }
                if($user->username){
                    $user->fullname = $b->fullname;
                    $user->email = null;
                    $user->nric = $b->nric;
                    $user->role = 1;
                    $user->status = 10;
                    $user->setPassword($pass);
                    if($user->save()){
                        $bene = new Entrepreneur();
                        $bene->user_id = $user->id;
                        $bene->phone = $b->phone;
                        $bene->state = $b->state;
                        $bene->address = $b->address;
                        $bene->biz_info = $b->biz_info;
                        $bene->postcode = $b->postcode;
                        $bene->city = $b->city;
                        $bene->note = $b->note;
                        if($bene->save()){
                            echo '<br /> Creating Benecifiary ' . $b->fullname . ' successful';
                        }
                        
                    }else{
                        echo '<br /> Error creating user ' . $b->fullname;
                    }
                    
                    
                    
                }
                
                $transaction->commit();
                
                }
                catch (\Exception $e)
                {
                    $transaction->rollBack();
                    echo  $e->getMessage();
                }
                    
                }
                
           
        
        
        
    } */
    
    

     public function actionLanguage()
    {
         if(isset($_POST['lang'])){
             //echo $_POST['lang'];
             
             
             Yii::$app->language = $_POST['lang'];
            $cookie = new yii\web\Cookie([
                'name' =>'lang',
                'value' =>$_POST['lang']
            ]);

            Yii::$app->getResponse()->getCookies()->add($cookie); 
            echo 'good';
        } 
 
    }


    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $this->layout = 'main-login';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
