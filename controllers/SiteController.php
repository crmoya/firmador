<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use app\models\LoginForm;
use app\models\Document;
use app\models\FirmaForm;
use app\models\ImagenFirmaForm;
use app\models\MessageForm;
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
                'only' => ['logout','index','upload-signature'],
                'rules' => [
                    [
                        'actions' => ['logout','index','upload-signature'],
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
            $model->files = UploadedFile::getInstances($model, 'files');
            $result = $model->upload();
            $userid = Yii::$app->user->id;
            $unsigned = Document::find()->where(['user_id'=>$userid,'uploaded'=>0])->all();
            if($result == count($unsigned)){
                return $this->render('previsualizar',['unsigned'=>$unsigned]);
            }
            else{
                Yii::$app->session->setFlash('error', "No se pudieron cargar los documentos, por favor reintente.");
            }
        }
        return $this->render('firmar',['model'=>$model]);
    }

    public function actionUploadSignature(){
        $model = new ImagenFirmaForm();
        if (Yii::$app->request->isPost) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->upload()) {
                Yii::$app->session->setFlash('success', "Imagen cargada con éxito.");
            }
            else{
                Yii::$app->session->setFlash('error', "No se pudo cargar la imagen, por favor reintente.");
            }
        }
        return $this->render('upload-signature',['model'=>$model]);
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
