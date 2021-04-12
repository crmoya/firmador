<?php

namespace app\models;

use Yii;
use yii\base\Model;

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
        $path = Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'documents'. DIRECTORY_SEPARATOR . "not_signed";
        if ($this->validate()) {
            $user_id = Yii::$app->user->id;
            $user_path = $path . DIRECTORY_SEPARATOR . $user_id;
            if(!is_dir($user_path)){
                mkdir($user_path);
            }
            $document = new Document();
            $document->user_id = $user_id;
            if($document->save()){
                if($this->file->saveAs($user_path . DIRECTORY_SEPARATOR . $document->id . ".pdf")){
                    return $document->id;
                }
            }            
        }
        return -1;
    }
}
