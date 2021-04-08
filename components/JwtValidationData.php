<?php

namespace app\components;

use Yii;

class JwtValidationData extends \sizeg\jwt\JwtValidationData
{
 
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->validationData->setIssuer(Yii::$app->params['server']);
        $this->validationData->setAudience(Yii::$app->params['server']);
        $this->validationData->setId(Yii::$app->params['jwtId']);

        parent::init();
    }
}    