<?php

use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AuthorizedSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mis dispositivos autorizados';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="authorized-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'device',
            'name',
            [   
                'header' => 'Desvincular',
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
            ],
        ],
    ]); ?>


</div>
