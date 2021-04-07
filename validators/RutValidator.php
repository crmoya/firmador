<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\validators;

use yii\validators\Validator;

/**
 * Validates that a rut has a correct format and is a valid rut 
 *
 * @author fvasquez
 */
class RutValidator extends Validator {

    public function init() {
        parent::init();
        $this->message = 'Rut inválido';
    }

    public function validateAttribute($model, $attribute) {
        $value = $model->$attribute;
        if (!$this->validarRut($value)) {
            $model->addError($attribute, $this->message);
        }
    }

//    public function clientValidateAttribute($model, $attribute, $view) {
//        $value = $model->$attribute;
//        $statuses = json_encode($this->validarRut($value));
//        $message = json_encode($this->message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
//        return <<<JS
//if (!$statuses) {
//    messages.push($message);
//}
//JS;
//    }

    /**
     * Check if rut is a valid one.
     * Example: 22.452.225-8 22452225-8 both returns true
     * @param  String $rut The rut to validate it can be in any format 
     * @return boolean      True when rut is valid and false otherwise
     */
    protected function validarRut($rut) {
        try {
            $rut = strtoupper(preg_replace('/\.|,|-/', '', $rut));
            $sub_rut = substr($rut, 0, strlen($rut) - 1);
            $sub_dv = substr($rut, -1);
            $x = 2;
            $s = 0;
            for ($i = strlen($sub_rut) - 1; $i >= 0; $i--) {
                if ($x > 7) {
                    $x = 2;
                }
                $s += $sub_rut[$i] * $x;
                $x++;
            }
            $dv = 11 - ($s % 11);
            if ($dv == 10) {
                $dv = 'K';
            }
            if ($dv == 11) {
                $dv = '0';
            }
            if ($dv == $sub_dv) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $ex) {
            return false;
        }
    }

}
