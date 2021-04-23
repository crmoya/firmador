<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Firmador';
?>
<div class="site-index">
    <div class="telon">
        <span style="font-size:15pt;" id="gear-telon">Por favor espere... <img src="<?=Yii::getAlias("@web")?>/images/gear.gif" height="33" style="position:relative;top:-5px;" /></span>
    </div>
    <div class="principal">
        <p>
            Seleccione los documentos que desea firmar. 
            <b>Puede previsualizarlos haciendo click en la lupa.</b>
        </p>
        <div class="row">
        <?php foreach($unsigned as $document):?>        
            <div class="col-md-3" style="text-align:center;">
                <?=Html::img('@web/images/pdf.png', ['alt' => $document->name,'height'=>40])?>
                <br/>
                <input class="check" id="<?=$document->id;?>" type="checkbox" checked>
                <?=Html::a('<i class="fa fa-fw fa-search"></i>', ['download', 'id' => $document->id], ['class' => 'profile-link ver','target'=>'_blank'])?>  
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
                'delete',
                { itemsJson: JSON.stringify(not_checked) }
            ).done(function(resp){
                callWS(checked);
            });

            
        });   
    }); 
    
JS;
$this->registerJs($script);
