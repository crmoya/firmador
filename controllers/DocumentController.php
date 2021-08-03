<?php

namespace app\controllers;

use Yii;
use app\models\Document;
use app\models\DocumentSearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;

/**
 * DocumentController implements the CRUD actions for Document model.
 */
class DocumentController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['uploaded','download','view','delete'],
                'rules' => [
                    [
                        'actions' => ['uploaded','download','view','delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Document models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DocumentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
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
    
    public function actionUploaded()
    {
        $userid = Yii::$app->user->id;
        $searchModel = new DocumentSearch();
        Document::deleteAll(['user_id'=>$userid,'uploaded'=>0]);
        $dataProvider = new ActiveDataProvider([
            'query' => Document::find()->where(['user_id'=>$userid,'uploaded'=>1])->orderBy(['id'=>SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $this->render('index',['dataProvider'=>$dataProvider,'searchModel'=>$searchModel]);
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
}
