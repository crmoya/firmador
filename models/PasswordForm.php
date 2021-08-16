<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class PasswordForm extends Model
{
    public $claveAntigua;
    public $claveNueva;
    public $repitaClave;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['claveAntigua', 'claveNueva', 'repitaClave'], 'required'],
            ['claveNueva', 'compare', 'compareAttribute' => 'repitaClave'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'claveAntigua' => 'Clave actual',
            'claveNueva' => 'Clave nueva',
            'repitaClave' => 'Repita clave nueva',
        ];
    }

}
