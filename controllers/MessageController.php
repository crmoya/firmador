<?php

namespace app\controllers;

use Yii;
use app\models\Message;
use app\models\MessageSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MessageController implements the CRUD actions for Message model.
 */
class MessageController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['modify'],
                'rules' => [
                    [
                        'actions' => ['modify'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Updates an existing Message model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionModify()
    {
        //si el usuario aún no ha subido su mensaje, se le asigna por defecto
        $model = Message::find()->where(['user_id'=>Yii::$app->user->id])->one();
        if(!isset($model)){
            $model = new Message();
            $model->user_id = Yii::$app->user->id; 
            $model->text = Yii::$app->params['MENSAJE_POR_DEFECTO'];
        }

        if ($model->load(Yii::$app->request->post())) {
            if($model->save()){
                Yii::$app->session->setFlash('success', "Mensaje para firmar cambiado con éxito.");
            }
            else{
                Yii::$app->session->setFlash('error', "Error al intentar cambiar el mensaje para firmar. Por favor reintente.");
            }
        }

        return $this->render('modify', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Message model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Message the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Message::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
