<?php

namespace app\controllers;

use app\models\Document;
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
                'login', 'refresh',
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
                ->expiresAt($time + 60)// Configures the expiration time of the token (exp claim)
                ->withClaim('uid', $user->id)// Configures a new claim, called "uid"
                ->getToken($signer, $key); // Retrieves the generated token

            $refreshtoken = Yii::$app->security->generateRandomString(255);
            $user->refresh_token = $refreshtoken;
            $user->save();

            return $this->asJson([
                'status' => 'SUCCESS',
                'token' => (string)$token,
                'refreshToken' => $refreshtoken,
            ]);
        }

        return $this->asJson([
            'status' => 'ERROR',
        ]);
        
    }

    public function actionRefresh(){
        $oldtoken = Yii::$app->request->get('token');
        $refreshtoken = Yii::$app->request->get('refresh_token');
        $user = User::findIdentityByStringToken($oldtoken);
        if(isset($user) && $user->refresh_token == $refreshtoken){
            $jwt = Yii::$app->jwt;
            $signer = $jwt->getSigner('HS256');
            $key = $jwt->getKey();
            $time = time();

            $token = $jwt->getBuilder()
                ->issuedBy(Yii::$app->params['server'])
                ->permittedFor(Yii::$app->params['server'])
                ->identifiedBy(Yii::$app->params['jwtId'], true)
                ->issuedAt($time)
                ->expiresAt($time + 60)
                ->withClaim('uid', $user->id)
                ->getToken($signer, $key); 

            $refreshtoken = Yii::$app->security->generateRandomString(255);
            $user->refresh_token = $refreshtoken;
            $user->save();

            return $this->asJson([
                'status' => 'SUCCESS',
                'token' => (string)$token,
                'refreshToken' => $refreshtoken,
            ]);
        }

        return $this->asJson([
            'status' => 'ERROR',
        ]);
    }
    
    public function actionDownload() {
        $request = Yii::$app->request;
        $id = $request->get('id');

        $document = Document::findOne($id);
        if($document->user_id != Yii::$app->user->id){
            throw new \yii\web\NotFoundHttpException();
        }
        $path = Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . 'not_signed' . DIRECTORY_SEPARATOR . Yii::$app->user->id . DIRECTORY_SEPARATOR . $id . '.pdf';
        $fullname = realpath($path);
        if(file_exists($fullname)){
            $file = Yii::$app->response->sendFile($fullname);  
            unlink($path);  
            return $file;
        }
        throw new \yii\web\NotFoundHttpException();
    }

    public function actionUpload(){
        $json = [
            'Status' => 'ERROR',
            'Message' => 'Inicio',
        ];
        $request = Yii::$app->request;
        $id = $request->get('id');
        $allowed = array("pdf" => "application/octet-stream");
        $filetype = $_FILES["file"]["type"];

        $userpath = Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . 'signed' . DIRECTORY_SEPARATOR . Yii::$app->user->id;
        if(!is_dir($userpath)){
            mkdir($userpath);
        }    
        $path = $userpath . DIRECTORY_SEPARATOR . $id . '.pdf';
        if (in_array($filetype, $allowed)) {
            if(move_uploaded_file($_FILES["file"]["tmp_name"], $path)){
                $json = [
                    'Status' => 'SUCCESS',
                    'Message' => 'Documento subido con éxito',
                ];
            }   
            else{
                $json = [
                    'Status' => 'ERROR',
                    'Message' => 'No se pudo subir el documento',
                ];
            }
        } else {
            $json = [
                'Status' => 'ERROR',
                'Message' => 'Solo se admiten documentos PDF',
            ];
        }
        return $this->asJson($json);
    }

}
