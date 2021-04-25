<?php

/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;

$this->title = 'Firmador';
?>
<div class="site-index">
    <div class="principal">
        <h4>Seleccione una imagen con su firma manuscrita</h4>
        <?php if($mensaje!=""):?>
        <div class="alert alert-warning">
            <?=$mensaje?>
        </div>
        <?php endif;?>
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
            <?= $form->field($model, 'files[]')->fileInput(['accept' => '.pdf', 'class'=>'form-control input-file']) ?>
            <button class="btn btn-success">Cargar y previsualizar documentos</button>
        <?php ActiveForm::end() ?>
    </div>
</div>