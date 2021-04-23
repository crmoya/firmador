<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/styles.css',
    ];
    public $js = [
        '//cdnjs.cloudflare.com/ajax/libs/pdf.js/2.8.335/pdf.worker.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/pdf.js/2.8.335/pdf.min.js',
        'js/script.js',
        '//cdn.jsdelivr.net/npm/sweetalert2@10',
        '//kit.fontawesome.com/66ac3e2c70.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
