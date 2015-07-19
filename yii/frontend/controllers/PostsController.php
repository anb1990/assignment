<?php

namespace frontend\controllers;

use Yii;
use app\models\Posts;
use app\models\PostsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\classescustom\security\EncDecrypt;

//use yii\rest\ActiveController;

/**
 * PostsController implements the CRUD actions for Posts model.
 */
class PostsController extends Controller {

    //public $modelClass = 'frontend\models\Posts';
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Posts models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new PostsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Posts model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        
        $model = new Posts();
        $encDecrypt = new EncDecrypt();
        $id = json_encode($id);
        $id = $encDecrypt->encrypt($id);
        
        $curl = curl_init("http://api.com/fetchpost/$id");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $curl_response = json_decode($encDecrypt->decrypt($curl_response));
        $model = new Posts();


        $model->id = $curl_response->id;
        $model->author = $curl_response->author;
        $model->title = $curl_response->title;
        $model->content = $curl_response->content;
        
        return $this->render('view', [
                    'model' => $model,
        ]);
    }

    /**
     * Creates a new Posts model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Posts();

        if ($model->load(Yii::$app->request->post())) {
            $encDecrypt = new EncDecrypt();
            if(isset(Yii::$app->user->id)){
        $userID = Yii::$app->user->id;
        }else{$userID = -1;}
       
            $post = json_encode(Yii::$app->request->post('Posts'));

            //Data encryption for websevice
            $userID = $encDecrypt->encrypt($userID);
            $post = $encDecrypt->encrypt($post);
            $curl = curl_init("http://api.com/createpost/$post/$userID");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $curl_response = curl_exec($curl);
            if ($curl_response && $curl_response != -2) {
                return $this->redirect(['index']);
            }else if($curl_response == -2){
                 echo "Sorry, you dont have permossion to add posts";
            }
         }else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Posts model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {

        $encDecrypt = new EncDecrypt();
        $id = json_encode($id);
        $id = $encDecrypt->encrypt($id);
        $curl = curl_init("http://api.com/fetchpost/$id");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $curl_response = json_decode($encDecrypt->decrypt($curl_response));
        $model = new Posts();


        $model->id = $curl_response->id;
        $model->author = $curl_response->author;
        $model->title = $curl_response->title;
        $model->content = $curl_response->content;



        if ($model->load(Yii::$app->request->post())) {
$model->id = $curl_response->id;
        $model->author = $curl_response->author;
        $model->title = $curl_response->title;
        $model->content = $curl_response->content;
        
            $encDecrypt = new EncDecrypt();
            if(isset(Yii::$app->user->id)){
        $userID = Yii::$app->user->id;
        }else{$userID = -1;}
            $post = json_encode(Yii::$app->request->post('Posts'));

            $post = $encDecrypt->encrypt($post);
            $userID = $encDecrypt->encrypt($userID);
            $curl = curl_init("http://api.com/updatepost/$userID/$id/$post");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $curl_responseUpdate = curl_exec($curl);

            if ($curl_responseUpdate && $curl_responseUpdate != -2) {
                return $this->redirect(['index']);
            } elseif($curl_responseUpdate == -2){
                 echo "Sorry, you dont have permossion to edit posts";
            }
            else {
                return $this->render('update', [
                            'model' => $model,
                ]);
            }
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Posts model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        
         $encDecrypt = new EncDecrypt();
        $id = json_encode($id);
        $id = $encDecrypt->encrypt($id);
        if(isset(Yii::$app->user->id)){
        $userID = Yii::$app->user->id;
        }else{$userID = -1;}
        
        $userID = $encDecrypt->encrypt($userID);
        $curl = curl_init("http://api.com/deletepost/$userID/$id");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $curl_response = json_decode($encDecrypt->decrypt($curl_response));
        if($curl_response == -2){
            echo "Sorry, you dont have permossion to delete posts";
        }
        $model = new Posts();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Posts model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Posts the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Posts::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelElastic($id) {
        if (($model = Posts::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
