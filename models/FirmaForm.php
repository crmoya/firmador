<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\HtmlPurifier;

/**
 * ContactForm is the model behind the contact form.
 */
class FirmaForm extends Model
{
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'pdf'],
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
        if ($this->validate()) {
            $document = new Document();
            $document->user_id = $userid;
            $document->name = HtmlPurifier::process($this->file->name);
            if($document->save()){
                if($this->file->saveAs($userpath . DIRECTORY_SEPARATOR . $document->id . ".pdf")){
                    return $document->id;
                }
            }            
        }
        return -1;
    }
}
