<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class RutAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/jquery.rut.min.js',
        'js/rut.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
