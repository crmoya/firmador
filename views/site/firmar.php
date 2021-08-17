<?php

/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;

$this->title = 'Firmar';
?>
<div class="site-index">
    <div class="principal">
        <h4>Seleccione uno o varios documentos .PDF para firmar</h4>
        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert-success alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <h4><i class="icon fa fa-check"></i> Documentos subidos con éxito!</h4>
                <?= Yii::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>

        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <h4><i class="icon fa fa-close"></i> Error al cargar los documentos!</h4>
                <?= Yii::$app->session->getFlash('error') ?>
            </div>
        <?php endif; ?>
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
            <?= $form->field($model, 'files[]')->fileInput(['multiple' => true, 'accept' => '.pdf', 'class'=>'form-control input-file']) ?>
            <button class="btn btn-success">Cargar y previsualizar documentos</button>
        <?php ActiveForm::end() ?>
    </div>
</div>