<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Document;
use app\models\FirmaForm;
use yii\helpers\Json;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout','index','uploaded','view', 'download', 'delete'],
                'rules' => [
                    [
                        'actions' => ['logout','index','uploaded','view', 'download', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
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
        $model = new FirmaForm();
        $mensaje = "";
        if (Yii::$app->request->isPost) {
            $model->files = UploadedFile::getInstances($model, 'files');
            $result = $model->upload();
            $userid = Yii::$app->user->id;
            $unsigned = Document::find()->where(['user_id'=>$userid,'uploaded'=>0])->all();
            if($result == count($unsigned)){
                return $this->render('previsualizar',['unsigned'=>$unsigned]);
            }
            else{
                $mensaje = "ATENCIÓN: NO SE PUDIERON CARGAR LOS DOCUMENTOS, POR FAVOR REINTENTE.";
            }
        }
        return $this->render('firmar',['model'=>$model,'mensaje'=>$mensaje]);
    }

    public function actionUploaded()
    {
        $subidos = Document::find()->where(['user_id'=>Yii::$app->user->id,'uploaded'=>1])->orderBy(['id'=>'DESC'])->all();
        return $this->render('subidos',['subidos'=>$subidos]);
    }

    public function actionDelete($itemsJson){
        $userid = Yii::$app->user->id;
        if($userid <= 0){
            throw new \yii\web\ForbiddenHttpException();
        }
        $items = Json::decode($itemsJson);
        $ok = true;
        foreach($items as $documentId){
            $document = Document::findOne($documentId);
            if(isset($document)){
                if($document->user_id != $userid || $document->uploaded != 0){
                    throw new \yii\web\ForbiddenHttpException();
                }
                $pathdocuments = Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'documents';
                $pathunsigned = $pathdocuments. DIRECTORY_SEPARATOR . "unsigned";
                $path = $pathunsigned . DIRECTORY_SEPARATOR . $userid . DIRECTORY_SEPARATOR . $documentId . '.pdf';
                if(file_exists($path)){
                    if(!unlink($path)){
                        $ok = false;
                    }
                }
                else{
                    $ok = false;
                }
            }
        }
        if($ok){
            echo Json::encode("OK");
        }
        else{
            throw new \yii\web\NotFoundHttpException();
        }
    }

    public function actionDownload($id)
    {
        $userid = Yii::$app->user->id;
        if($userid <= 0){
            throw new \yii\web\ForbiddenHttpException();
        }
        $document = Document::findOne($id);
        if(!isset($document)){
            throw new \yii\web\NotFoundHttpException();
        }
        if($document->user_id != $userid || $document->uploaded != 0){
            throw new \yii\web\ForbiddenHttpException();
        }
        $pathdocuments = Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'documents';
        $pathunsigned = $pathdocuments. DIRECTORY_SEPARATOR . "unsigned";
        $path = $pathunsigned . DIRECTORY_SEPARATOR . $userid . DIRECTORY_SEPARATOR . $id . '.pdf';
        $fullname = realpath($path);
        if(file_exists($fullname)){
            $file = Yii::$app->response->sendFile($fullname,$document->name, ['inline'=>true]); 
            return $file;
        }
        throw new \yii\web\NotFoundHttpException();
    }

    public function actionView($id)
    {
        $userid = Yii::$app->user->id;
        if($userid <= 0){
            throw new \yii\web\ForbiddenHttpException();
        }
        $document = Document::findOne($id);
        if(!isset($document)){
            throw new \yii\web\NotFoundHttpException();
        }
        if($document->user_id != $userid || $document->uploaded != 1){
            throw new \yii\web\ForbiddenHttpException();
        }
        $pathdocuments = Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'documents';
        $pathsigned = $pathdocuments. DIRECTORY_SEPARATOR . "signed";
        $path = $pathsigned . DIRECTORY_SEPARATOR . $userid . DIRECTORY_SEPARATOR . $id . '.pdf';
        $fullname = realpath($path);
        if(file_exists($fullname)){
            $file = Yii::$app->response->sendFile($fullname,$document->name); 
            return $file;
        }
        throw new \yii\web\NotFoundHttpException();
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

}
