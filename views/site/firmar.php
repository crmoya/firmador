<?php

/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;

$this->title = 'Firmador';
?>
<div class="site-index">
    <div class="telon">
        <span style="font-size:15pt;" id="gear-telon">Por favor espere... <img src="<?=Yii::getAlias("@web")?>/images/gear.gif" height="33" style="position:relative;top:-5px;" /></span>
    </div>
    <div class="principal">
        <?php if($mensaje!=""):?>
        <div class="alert alert-warning">
            <?=$mensaje?>
        </div>
        <?php endif;?>
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
            <?= $form->field($model, 'files[]')->fileInput(['multiple' => true, 'accept' => '.pdf', 'class'=>'form-control input-file']) ?>
            <button class="btn btn-success">Cargar y previsualizar documentos</button>
        <?php ActiveForm::end() ?>
    </div>
</div>