<?php

/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;

$this->title = 'Firmador';
?>
<div class="site-index">
    <div class="principal">
        <h4>Seleccione una imagen con su firma manuscrita</h4>
        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert-success alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <h4><i class="icon fa fa-check"></i>Imagen guardada con éxito!</h4>
                <?= Yii::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>

        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <h4><i class="icon fa fa-circle"></i>Error!</h4>
                <?= Yii::$app->session->getFlash('error') ?>
            </div>
        <?php endif; ?>
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
            <?= $form->field($model, 'imageFile')->fileInput(['accept' => 'image/*', 'class'=>'form-control input-file']) ?>
            <button class="btn btn-success">Cargar imagen</button>
        <?php ActiveForm::end() ?>
    </div>
</div>