<?php

namespace app\controllers;

use app\models\Authorized;
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
                ->expiresAt($time + 600)// Configures the expiration time of the token (exp claim)
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
                ->expiresAt($time + 600)
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
        $device = $request->get('device');

        if(strlen($device)<=0){
            throw new \yii\web\ForbiddenHttpException("Unauthorized device");
        }
        $userid = Yii::$app->user->id;
        if($userid <= 0){
            throw new \yii\web\ForbiddenHttpException("User not found");
        }
        $hash = sha1($device.".".$userid);
        $document = Document::findOne($id);
        if(!isset($document)){
            throw new \yii\web\NotFoundHttpException("Document not found");
        }
        if($document->user_id != $userid){
            throw new \yii\web\ForbiddenHttpException("User is not document's owner");
        }
        $authorized = Authorized::find()->where(['user_id'=>$userid, 'device'=>$hash])->one();
        if(!isset($authorized)){
            throw new \yii\web\ForbiddenHttpException("Unauthorized device");
        }
        $pathdocuments = Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'documents';
        if(!is_dir($pathdocuments)){
            mkdir($pathdocuments);
        } 
        $pathunsigned = $pathdocuments. DIRECTORY_SEPARATOR . "unsigned";
        if(!is_dir($pathunsigned)){
            mkdir($pathunsigned);
        } 
        $path = $pathunsigned . DIRECTORY_SEPARATOR . $userid . DIRECTORY_SEPARATOR . $id . '.pdf';
        $fullname = realpath($path);
        if(file_exists($fullname)){
            $file = Yii::$app->response->sendFile($fullname);  
            unlink($path);  
            return $file;
        }
        throw new \yii\web\ForbiddenHttpException("Other");
    }

    public function actionUpload(){
        $json = [
            'Status' => 'ERROR',
            'Message' => 'Inicio',
        ];
        $request = Yii::$app->request;
        $id = $request->get('id');
        $device = $request->get('device');
        if(strlen($device)<=0){
            throw new \yii\web\ForbiddenHttpException("Unauthorized device");
        }
        $userid = Yii::$app->user->id;
        if($userid <= 0){
            throw new \yii\web\ForbiddenHttpException("User not found");
        }
        $hash = sha1($device.".".$userid);
        $document = Document::findOne($id);
        if(!isset($document)){
            throw new \yii\web\NotFoundHttpException("Document not found");
        }
        if($document->user_id != $userid){
            throw new \yii\web\ForbiddenHttpException("User is not document's owner");
        }
        $authorized = Authorized::find()->where(['user_id'=>$userid, 'device'=>$hash])->one();
        if(!isset($authorized)){
            throw new \yii\web\ForbiddenHttpException("Unauthorized device");
        }
        $allowed = array("pdf" => "application/octet-stream");
        $filetype = $_FILES["file"]["type"];
        
        $pathdocuments = Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'documents';
        if(!is_dir($pathdocuments)){
            mkdir($pathdocuments);
        } 
        $pathsigned = $pathdocuments. DIRECTORY_SEPARATOR . "signed";
        if(!is_dir($pathsigned)){
            mkdir($pathsigned);
        } 
        $userpath = $pathsigned . DIRECTORY_SEPARATOR . $userid;
        if(!is_dir($userpath)){
            mkdir($userpath);
        }    
        
        $path = $userpath . DIRECTORY_SEPARATOR . $id . '.pdf';
        if (in_array($filetype, $allowed)) {
            if(move_uploaded_file($_FILES["file"]["tmp_name"], $path)){
                $document->uploaded = 1;
                $document->signed_at = date("Y-m-d H:i:s");
                if($document->save()){
                    $json = [
                        'Status' => 'SUCCESS',
                        'Message' => 'Documento subido con éxito',
                    ];
                }
                else{
                    unlink($path);
                    $json = [
                        'Status' => 'ERROR',
                        'Message' => 'No se pudo grabar el documento subido',
                    ];
                }
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
