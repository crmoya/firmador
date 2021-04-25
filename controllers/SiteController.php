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

    public function actionUploadSignature(){
        $model = new ImagenFirmaForm();
        $mensaje = "";
        return $this->render('upload-signature',['model'=>$model,'mensaje'=>$mensaje]);
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
