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
        $authorized = Authorized::find(['user_id'=>$userid,'device'=>$hash])->one();
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
}
