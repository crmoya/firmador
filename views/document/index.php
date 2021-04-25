<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DocumentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mis documentos firmados';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name:ntext',
            [
                'attribute' => 'signed_at',
                'value' => function($model){
                    return date('d/m/Y H:i:s', strtotime($model->signed_at));
                },
            ],
            [
                'header' => 'Descargar',
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
    ]); ?>


</div>
