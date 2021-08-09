<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use app\assets\RutAsset;
RutAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form col-md-3">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'class' => 'form-control rut','readonly' => !$model->isNewRecord],) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true,'class' => 'form-control']) ?>

    <?php if($model->isNewRecord): ?>
    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true,'class' => 'form-control']) ?>

    <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => true, 'class' => 'form-control']) ?>
    
    <?=  $form->field($model, 'rol')->dropDownList(['administrador'=>'Administrador','notario'=>'Notario'],['prompt'=>'Seleccione un Rol']);?>
    <?php endif; ?>
    
    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
