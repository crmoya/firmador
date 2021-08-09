<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ImagenFirmaForm extends Model
{
    public $imageFile;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'imageFile'=>'Imagen',
        ];
    }
    
    public function upload()
    {
        $pathsignature = Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'signature';
        if(!is_dir($pathsignature)){
            mkdir($pathsignature);
        } 
        $userid = Yii::$app->user->id;
        $userpath = $pathsignature . DIRECTORY_SEPARATOR . $userid;
        if(!is_dir($userpath)){
            mkdir($userpath);
        }

        //primero limpiar el directorio de alguna firma anterior
        $files=\yii\helpers\FileHelper::findFiles($userpath);
        if (isset($files[0])) {
            foreach ($files as $file) {
                unlink($file);
            }
        }
        if ($this->validate()) {
            $this->imageFile->saveAs($userpath . DIRECTORY_SEPARATOR . "firma.jpg");
            return true;
        } else {
            return false;
        }
    }
}
