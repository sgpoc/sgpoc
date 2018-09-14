<?php 

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\widgets\Growl;
use yii\bootstrap\Modal;
use yii\helpers\Url;

$gridColumns = [
    [
        'class' => 'kartik\grid\SerialColumn',
        'contentOptions' => ['class' => 'kartik-sheet-style'],
        'width' => '36px',
        'header' => '#',
        'headerOptions' => ['class' => 'kartik-sheet-style']
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'attribute' => 'Item',
        'vAlign' => 'middle',
        'contentOptions' => ['class' => 'kartik-sheet-style']
    ],
     [
        'class' => 'kartik\grid\DataColumn',
        'attribute' => 'Abreviatura',
        'label' => 'Unidad',
        'contentOptions' => ['class' => 'kartik-sheet-style']
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => 'Incidencia',
        'vAlign' => 'middle',
        'width' => '240px',
        'template' => '{modificar}',
        'buttons' => [
                'modificar' => function($url, $model, $key){ 
                    return  Html::button($model['Incidencia'],
                            [
                                'value'=>Url::to(['/elementos-constructivos/modificar-incidencia', 'IdItem' => $model['IdItem'], 'IdElementoConstructivo' => $model['IdElementoConstructivo']]), 
                                'class'=>'btn btn-link modalButton',
                                'title'=>'Modificar Incidencia Item en Elemento'
                            ]);
                },
        ]        
    ],
    [
        'class' => '\kartik\grid\ActionColumn',
        'header' => 'Acciones',
        'vAlign' => 'middle',
        'width' => '240px',
        'template' =>  '{borrar}',
        'buttons' => [
                'borrar' => function($url, $model, $key){
                    return Html::a('<i class="fa fa-trash-o"></i>',
                            [
                                'borrar-item', 'IdItem' => $model['IdItem'], 'IdElementoConstructivo' => $model['IdElementoConstructivo']
                            ], 
                            [
                                'title' => 'Borrar Item del Elemento', 
                                'class' => 'btn btn-link',
                                'data' => [
                                    'confirm' => 'Esta seguro que desea borrar el Item del Elemento?',
                                    'method' => 'post'
                                ]
                            ]);
                }     
        ]
    ], 
];

                
?>
  
<?php if(Yii::$app->session->getFlash('alert')){
    echo Growl::widget([
    'type' => Growl::TYPE_DANGER,
    'title' => 'Cuidado!',
    'icon' => 'glyphicon glyphicon-remove-sign',
    'body' => Yii::$app->session->getFlash('alert'),
    'showSeparator' => true,
    'delay' => 1000,
    'pluginOptions' => [
        'showProgressbar' => false,
        'placement' => [
            'from' => 'top',
            'align' => 'center',
        ]
    ]
    ]);
    }
?>

<?php
    Modal::begin([
            'header'=>'<h2>Items</h2>',
            'footer'=>'',
            'id'=>'modal',
            'size'=>'modal-lg',
       ]);
    echo "<div id='modalContent'></div>";
    Modal::end();
?>

<div>
    <?= GridView::widget([
        'moduleId' => 'gridviewKrajee',
        'pjax'=>true,
        'pjaxSettings'=>[
            'neverTimeout'=>true,
        ],
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns
    ]);   
    ?>
</div>

