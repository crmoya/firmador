<?php

namespace app\controllers;

use app\models\PasswordForm;
use Yii;
use app\models\User;
use app\models\UserSearch;
use Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create','index','view','delete','password'],
                'rules' => [
                    [
                        'actions' => ['create','index','view','delete'],
                        'allow' => true,
                        'roles' => ['administrador'],
                    ],
                    [
                        'actions' => ['password'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionPassword()
    {

        if(Yii::$app->user->isGuest) {
            throw new Exception("No ha iniciado sesión.");
        }
        $model = new PasswordForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::findOne(Yii::$app->user->id);
            if(isset($user)){
                if(Yii::$app->security->validatePassword($model->claveAntigua, $user->password)){
                    $user->password = Yii::$app->security->generatePasswordHash($model->claveNueva);
                    $user->password_repeat = $user->password;
                    if($user->save()){
                        Yii::$app->session->setFlash('success', "Clave cambiada correctamente.");
                        $model = new PasswordForm();
                    }
                    else{
                        Yii::$app->session->setFlash('error', "ERROR: no se pudo cambiar su clave, reintente.");
                    }
                }
                else{
                    Yii::$app->session->setFlash('error', "ERROR: Clave actual incorrecta.");
                }
            }
        }
        return $this->render('password', [
            'model' => $model,
        ]);
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post())) {
            if($model->validate()){
                $model->password = Yii::$app->security->generatePasswordHash($model->password);
                $model->password_repeat = $model->password;
                if($model->save()){
                    $auth = Yii::$app->authManager;
                    $rol = $auth->getRole($model->rol);
                    $auth->assign($rol, $model->id);
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $auth = Yii::$app->authManager;
        $rol = $auth->getRole($model->rol);
        $auth->revoke($rol, $model->id);
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
