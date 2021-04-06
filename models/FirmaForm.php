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
        $path = Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'documents';
        if ($this->validate()) {
            $count = DocIndexer::getNext();
            $this->file->saveAs($path . DIRECTORY_SEPARATOR . "not_signed" . DIRECTORY_SEPARATOR . $count . ".pdf");
            return $count;
        } else {
            return -1;
        }
    }
}
