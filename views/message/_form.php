<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Message */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="message-form">

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            <h4><i class="icon fa fa-check"></i> <?= Yii::$app->session->getFlash('success') ?></h4>
        </div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            <h4><i class="icon fa fa-close"></i> <?= Yii::$app->session->getFlash('error') ?></h4>
        </div>
    <?php endif; ?>
    <?php $form = ActiveForm::begin(); ?>
    <p class="notice">
        *Tiene disponibles los siguientes parámetros para escribir su mensaje: <br/>
        <table class="table table-striped">
            <tr>
                <th>Atributo</th>
                <th>Significado</th>
            </tr>
            <tr>
                <td><?=Yii::$app->params['NOMBRE_DOCUMENTO']?></td>
                <td>Se reemplazará por el nombre del documento en el texto</td>
            </tr>
            <tr>
                <td><?=Yii::$app->params['FECHA_PALABRAS']?></td>
                <td>Se reemplazará por la fecha actual con formato de palabras en el texto</td>
            </tr>
            <tr>
                <td><?=Yii::$app->params['FECHA']?></td>
                <td>Se reemplazará por la fecha actual con formato DD-MM-AAAA en el texto</td>
            </tr>
        </table>    
    </p>
    <?= $form->field($model, 'text')->textarea(['rows' => 4]) ?>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
