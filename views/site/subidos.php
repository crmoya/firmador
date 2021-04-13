<?php

/* @var $this yii\web\View */

use yii\helpers\HtmlPurifier;
use yii\helpers\Url;

$this->title = 'Firmador';
?>
<br/><br/><br/>
<div class="site-index">
    <h3>Mis documentos firmados:</h3>
    <?php foreach($subidos as $subido):?>
    <div class="row">
        <div class="col-md-12">
            <a href="<?=Url::to(['site/view','id'=>$subido->id]);?>"><?=HtmlPurifier::process($subido->name)?></a>
        </div>
    </div>
    <?php endforeach;?>
</div>