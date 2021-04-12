<?php

/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;

$this->title = 'Firmador';
?>
<div class="site-index">
    <div class="telon">
        <span style="font-size:15pt;" id="gear-telon">Por favor espere... <img src="images/gear.gif" height="33" style="position:relative;top:-5px;" /></span>
    </div>
    <div class="principal">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
            <?= $form->field($model, 'file')->fileInput(['class'=>'form-control input-file']) ?>
            <button class="btn btn-success">Aceptar y Firmar documento</button>
        <?php ActiveForm::end() ?>
        <div class="col-lg-12" id="espereDocumento" style="display:none;"><span style="font-size:15pt;" id="gear">Por favor espere... <img src="images/gear.gif" height="33" style="position:relative;top:-5px;" /></span></div>
        <div id="previewer"></div>
    </div>
</div>


<?php

$script = <<< JS
    $('.input-file').change(function(e){
        readURL(this);
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();            
            reader.onload = function(e) {
                renderPDF(e.target.result);
            }            
            reader.readAsDataURL(input.files[0]); 
        }
    }
JS;
$this->registerJs($script);

if(isset($result)){
    $script = <<< JS


    $(document).ready(function(e){
        if($result > -1){
            Swal.fire({
                icon: "info",
                title: "Iniciando el proceso de firma...",
                text: "El documento está siendo cargado en su equipo... por favor, espere mientras se inicia el proceso de firma.",
            });
            callWS($result);
        }
        else{
            Swal.fire({
                icon: "error",
                title: "Archivo no seleccionado",
                text: "Por favor, seleccione un documento PDF.",
            });
        }
    });
    
    JS;
    $this->registerJs($script);
}

