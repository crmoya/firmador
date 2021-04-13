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
                'only' => ['logout','index','uploaded','view'],
                'rules' => [
                    [
                        'actions' => ['logout','index','uploaded','view'],
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
        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            $result = $model->upload();
            return $this->render('firmar',['model'=>$model,'result'=>$result]);
        }
        return $this->render('firmar',['model'=>$model]);
    }

    public function actionUploaded()
    {
        $subidos = Document::find()->where(['user_id'=>Yii::$app->user->id,'uploaded'=>1])->orderBy(['id'=>'DESC'])->all();
        return $this->render('subidos',['subidos'=>$subidos]);
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
