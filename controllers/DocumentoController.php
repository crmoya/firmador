<?php

namespace app\controllers;

use app\models\User;
use sizeg\jwt\JwtHttpBearerAuth;
use Yii;

/**
 * EstadoSeguimientoController implements the CRUD actions for EstadoSeguimiento model.
 */
class DocumentoController extends \yii\rest\Controller
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
                'login',
            ],
        ];

        return $behaviors;
    }


    /**
     * @return \yii\web\Response
     */
    public function actionLogin()
    {
        $username = Yii::$app->request->get('username');
        $password = Yii::$app->request->get('password');
        $user = User::findByUsername($username);
        if(isset($user) && $user->validatePassword($password)){
            /** @var Jwt $jwt */
            $jwt = Yii::$app->jwt;
            $signer = $jwt->getSigner('HS256');
            $key = $jwt->getKey();
            $time = time();

            // Adoption for lcobucci/jwt ^4.0 version
            $token = $jwt->getBuilder()
                ->issuedBy(Yii::$app->params['server'])// Configures the issuer (iss claim)
                ->permittedFor(Yii::$app->params['server'])// Configures the audience (aud claim)
                ->identifiedBy(Yii::$app->params['jwtId'], true)// Configures the id (jti claim), replicating as a header item
                ->issuedAt($time)// Configures the time that the token was issue (iat claim)
                ->expiresAt($time + 3600)// Configures the expiration time of the token (exp claim)
                ->withClaim('uid', $user->id)// Configures a new claim, called "uid"
                ->getToken($signer, $key); // Retrieves the generated token

            $user->access_token = (string)$token;
            $user->save();

            return $this->asJson([
                'status' => 'SUCCESS',
                'token' => (string)$token,
            ]);
        }

        return $this->asJson([
            'status' => 'ERROR',
        ]);
        
    }
    
    
    public function actionDownload() {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $userid = Yii::$app->user->id;

        
        $fullname = realpath(Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . 'not_signed' . DIRECTORY_SEPARATOR . $id . '.pdf');
        if(file_exists($fullname)){
            return Yii::$app->response->sendFile($fullname);    
        }
        throw new \yii\web\NotFoundHttpException();
    }

    public function actionUpload(){
        
    }

}
