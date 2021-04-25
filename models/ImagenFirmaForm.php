<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\HtmlPurifier;

/**
 * ContactForm is the model behind the contact form.
 */
class ImagenFirmaForm extends Model
{
    public $imageFile;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
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
        $pathdocuments = Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'documents';
        if(!is_dir($pathdocuments)){
            mkdir($pathdocuments);
        } 
        $pathunsigned = $pathdocuments. DIRECTORY_SEPARATOR . "unsigned";
        if(!is_dir($pathunsigned)){
            mkdir($pathunsigned);
        } 
        $userid = Yii::$app->user->id;
        $userpath = $pathunsigned . DIRECTORY_SEPARATOR . $userid;
        if(!is_dir($userpath)){
            mkdir($userpath);
        }

        //primero limpiar el directorio de los documentos no subidos
        Document::deleteAll(['user_id'=>$userid,'uploaded'=>0]);
        $files=\yii\helpers\FileHelper::findFiles($userpath);
        if (isset($files[0])) {
            foreach ($files as $file) {
                unlink($file);
            }
        }
        $documentos = 0;
        if ($this->validate()) {
            foreach ($this->files as $file) {
                $document = new Document();
                $document->user_id = $userid;
                $document->name = HtmlPurifier::process($file->name);
                if($document->save()){
                    if($file->saveAs($userpath . DIRECTORY_SEPARATOR . $document->id . ".pdf")){
                        $documentos++;
                    }
                }     
            }     
        }
        return $documentos;
    }
}
