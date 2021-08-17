<?php

/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

$this->title = 'Imagen de mi firma';
?>
<div class="site-index">
    <div class="principal">
        <?php 
        $path =  Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'signature' . DIRECTORY_SEPARATOR . Yii::$app->user->id . DIRECTORY_SEPARATOR . "firma.jpg";
        if(file_exists($path)):
            $url =Yii::$app->assetManager->publish($path)[1];
        ?>
            <h4>Imagen actual de su firma manuscrita:</h4>
            <?= Html::img($url, ['alt'=>'Mi firma manuscrita', 'class'=>'firma']);?>

            <h4>Puede modificarla seleccionando otra imagen:</h4>
        <?php else:?>
        <h4>Seleccione una imagen con su firma manuscrita</h4>
        <?php endif;?>
        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert-success alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <h4><i class="icon fa fa-check"></i> Operación exitosa!</h4>
                <?= Yii::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>

        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <h4><i class="icon fa fa-close"></i> Error!</h4>
                <?= Yii::$app->session->getFlash('error') ?>
            </div>
        <?php endif; ?>
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
            <?= $form->field($model, 'imageFile')->fileInput(['accept' => 'image/*', 'class'=>'form-control input-file']) ?>
            <button class="btn btn-success">Cargar imagen</button>
        <?php ActiveForm::end() ?>
       
    </div>
</div>