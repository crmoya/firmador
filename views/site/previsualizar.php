<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Firmador';
?>
<div class="site-index">
    <div class="principal">
        <h4>
            Seleccione los documentos que desea firmar. <br/>
            <b>Puede previsualizarlos haciendo click en la lupa.</b>
        </h4>
        <div class="row">
        <?php foreach($unsigned as $document):?>        
            <div class="col-md-3" style="text-align:center;">
                <input class="check" id="<?=$document->id;?>" type="checkbox" checked>
                <?=Html::img('@web/images/pdf.png', ['alt' => $document->name,'height'=>40])?>
                <br/>
                <?=Html::a('<i class="fa fa-fw fa-search"></i>', ['document/download', 'id' => $document->id], ['class' => 'profile-link ver','target'=>'_blank'])?>  
                <br/>
                <?=$document->name?>              
            </div>
        <?php endforeach;?>
        </div>
        <div class="row">
            <div class="col-md-12" style="text-align:center">
                <div id="sign" class="btn btn-success">Firmar documentos seleccionados</div>
            </div>
        </div>
    </div>
</div>


<?php
$url = Url::to(['document/delete']);
$script = <<< JS

    $(document).ready(function(e){ 
        $('#sign').click(function(e){
            Swal.fire({                
                icon: "info",          
                title: "Iniciando el proceso de firma...", 
                text: "El documento está siendo cargado en su equipo... por favor, espere mientras se inicia el proceso de firma.", 
            }); 

            var checked = [];
            var not_checked = [];
            $('.check').each(function(e){
                if($(this).prop('checked')){
                    checked.push($(this).attr('id'));
                }
                else{
                    not_checked.push($(this).attr('id'));
                }
            });
            $.get(
                '$url',
                { itemsJson: JSON.stringify(not_checked) }
            ).done(function(resp){
                callWS(checked);
            });

            
        });   
    }); 
    
JS;
$this->registerJs($script);
