<?php

namespace app\controllers;

use app\models\Authorized;
use sizeg\jwt\JwtHttpBearerAuth;
use Yii;

class AuthorizedController extends \yii\rest\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'optional' => [
                'login', 'refresh',
            ],
        ];

        return $behaviors;
    }


    public function actionAdd() {
        $request = Yii::$app->request;
        $device = $request->get('device');
        $userid = Yii::$app->user->id;
        $json = [
            'Status' => 'ERROR',
            'Message' => 'No fue posible autorizar el dispositivo.',
        ];
        $hash = sha1($device.".".$userid);
        $authorized = Authorized::find()->where(['user_id'=>$userid,'device'=>$hash])->one();
        if(isset($authorized)){
            $json = [
                'Status' => 'SUCCESS',
                'Message' => 'Dispositivo ya ha sido autorizado anteriormente.',
            ];
        }
        else{
            $authorized = new Authorized();
            $authorized->user_id = $userid;
            $authorized->device = $hash;
            if($authorized->save()){
                $json = [
                    'Status' => 'SUCCESS',
                    'Message' => 'Dispositivo autorizado con éxito.',
                ];
            }
        }
        return $this->asJson($json);
    }

    public function actionRemove() {
        $request = Yii::$app->request;
        $device = $request->get('device');
        $userid = Yii::$app->user->id;
        $json = [
            'Status' => 'ERROR',
            'Message' => 'No fue posible desvincular el dispositivo.',
        ];
        $hash = sha1($device.".".$userid);
        $authorized = Authorized::find()->where(['user_id'=>$userid,'device'=>$hash])->one();
        if(isset($authorized)){
            if(Authorized::deleteAll(['user_id'=>$userid,'device'=>$hash])>0){
                $json = [
                    'Status' => 'SUCCESS',
                    'Message' => 'Dispositivo ha sido desvinculado con éxito.',
                ];
            }
            else{
                $json = [
                    'Status' => 'ERROR',
                    'Message' => 'Dispositivo no se pudo desvincular.',
                ];
            }
        }
        else{
            $json = [
                'Status' => 'ERROR',
                'Message' => 'Dispositivo aún no ha sido autorizado.',
            ];
        }
        return $this->asJson($json);
    }
}
