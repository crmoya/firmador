<?php

namespace app\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * EstadoSeguimientoController implements the CRUD actions for EstadoSeguimiento model.
 */
class DocumentoController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    
    public function actionDownload() {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $fullname = realpath(Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . 'not_signed' . DIRECTORY_SEPARATOR . $id . '.pdf');
        if(file_exists($fullname)){
            return Yii::$app->response->sendFile($fullname);    
        }
        throw new \yii\web\NotFoundHttpException();
    }

}
