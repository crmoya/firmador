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
        <table class="table table-striped table-hover">
            <tr>
                <th>Documento</th>
                <th style="text-align:center;" width="10%">Seleccionar</th>
            </tr>
        <?php foreach($unsigned as $document):?>        
            <tr>
                <td>
                    <?=Html::a('<i class="fa fa-fw fa-search"></i><i style="color:red;" class="fa fa-fw fa-file-pdf-o"></i>&nbsp;&nbsp;&nbsp;&nbsp;' . $document->name, ['document/download', 'id' => $document->id], ['class' => 'profile-link ver','target'=>'_blank'])?>
                </td>
                <td style="text-align:center;">
                    <input class="check" id="<?=$document->id;?>" type="checkbox" checked>
                </td>
            </tr>
        <?php endforeach;?>
        </table>
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
