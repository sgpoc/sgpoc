<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\widgets\Growl;
use yii\bootstrap\Modal;
use yii\helpers\Url;


$this->title = 'SGPOC | Grupos Trabajo';

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
        'attribute' => 'GrupoTrabajo',
        'label' => 'Nombre',
        'vAlign' => 'middle',
        'contentOptions' => ['class' => 'kartik-sheet-style'],
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'attribute' => 'Mail',
        'label' => 'Email',
        'vAlign' => 'middle',
        'contentOptions' => ['class' => 'kartik-sheet-style']
    ],
    [
        'class' => 'kartik\grid\BooleanColumn',
        'attribute' => 'Estado',
        'vAlign' => 'middle',
        'hAlign' => 'center'
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'attribute' => 'FechaCreacion',
        'vAlign' => 'middle',
        'hAlign' => 'center'
    ],
    [
        'class' => '\kartik\grid\ActionColumn',
        'header' => 'Acciones',
        'vAlign' => 'middle',
        'width' => '270px',
        'template' => '{modificar} {borrar} {baja} {activar} {listarusuarios} {copiar}',
        'buttons' => [
                'modificar' => function($url, $model, $key){
                    return Html::button('<i class="fa fa-pencil-alt"></i>',
                            [
                                'value'=>Url::to(['/grupos-trabajo/modificar', 'IdGT' => $model['IdGT']]), 
                                'class'=>'btn btn-link modalButton',
                                'title'=>'Modificar Grupo de Trabajo'
                            ]);
                },
                'borrar' => function($url, $model, $key){
                    return Html::a('<i class="fa fa-trash"></i>',
                            [
                                'borrar','IdGT' => $model['IdGT']
                            ], 
                            [ 
                                'class'=>'btn btn-link',
                                'title'=>'Borrar Grupo Trabajo',
                                'data'=>[
                                    'confirm'=>'Esta seguro que desea borrar el Grupo de Trabajo?',
                                    'method'=>'post'
                                ]
                            ]);
                },
                'listarusuarios' => function($url, $model, $key){
                    return Html::button('<i class="fa fa-user"></i>',
                            [
                                'value'=>Url::to(['/grupos-trabajo/listar-usuarios', 'IdGT' => $model['IdGT']]), 
                                'class'=>'btn btn-link modalButton',
                                'title'=>'Listar Usuarios'
                            ]);
                },
                'baja' => function($url, $model, $key){
                    if($model['Estado'] === '1'){
                        return Html::a('<i class="fa fa-toggle-on"></i>',
                                [
                                    'baja','IdGT' => $model['IdGT']
                                ], 
                                [
                                    'title' => 'Dar de baja Grupo Trabajo', 
                                    'class' => 'btn btn-link'
                                ]);
                    }
                },
                'activar' => function($url, $model, $key){
                    if($model['Estado'] === '0'){
                        return Html::a('<i class="fa fa-toggle-off"></i>',
                                [
                                    'activar','IdGT' => $model['IdGT']
                                ], 
                                [
                                    'title' => 'Activar Grupo Trabajo',
                                    'class' => 'btn btn-link'
                                ]);
                    }
                },
                'copiar' => function($url, $model, $key){
                    return Html::a('<i class="fa fa-copy"></i>',
                                [
                                    'copiar','IdGT' => $model['IdGT']
                                ], 
                                [
                                    'title' => 'Copiar Datos Plantilla Base al Grupo Trabajo',
                                    'class' => 'btn btn-link'
                                ]);
                }
        ]
    ], 
] 

?>
  
<?php if(Yii::$app->session->getFlash('alert')){
            if(substr(Yii::$app->session->getFlash('alert'), 0, 2) != 'OK') {
                echo Growl::widget([
                'type' => Growl::TYPE_DANGER,
                'icon' => 'glyphicon glyphicon-remove-sign',
                'body' => Yii::$app->session->getFlash('alert'),
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
            else {
                echo Growl::widget([
                'type' => Growl::TYPE_SUCCESS,
                'icon' => 'glyphicon glyphicon-ok-sign',
                'body' => Yii::$app->session->getFlash('alert'),
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
        }
?>

<?php
    Modal::begin([
            'header'=>'<h2>Grupos de Trabajo</h2>',
            'footer'=>'',
            'id'=>'modal',
            'size'=>'modal-lg',
       ]);
    echo "<div id='modalContent'></div>";
    Modal::end();
?>


<div>
    <?= GridView::widget([
        'id' => 'gridview',
        'moduleId' => 'gridviewKrajee',
        'pjax'=>true,
        'pjaxSettings'=>[
            'neverTimeout'=>true,
            'options' => [
                'id' => 'gridview'
            ]
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
        'exportConfig' => [
                GridView::EXCEL => ['label' => 'EXCEL'],
                GridView::PDF => ['label' => 'PDF'],
         ],
        'toolbar' => [
            [
                'content' => 
                    Html::button('<i class="fa fa-plus"></i>',
                            [
                                'value'=>Url::to('/sgpoc/backend/web/grupos-trabajo/alta'),
                                'class'=>'btn btn-success modalButton',
                                'title'=>'Crear Grupo de Trabajo'
                            ]).' '.
                    Html::a('<i class="fa fa-redo"></i>', 
                            ['grupos-trabajo/listar'], 
                            [
                                'data-pjax' => 0, 
                                'class' => 'btn btn-default', 
                                'title' => 'Actualizar'
                            ]).' '.
                            Html::a('<i class="fas fa-external-link-alt"></i>', 
                            ['grupos-trabajo/exportar'],
                            [
                                'data-pjax' => 0, 
                                'class' => 'btn btn-default', 
                                'title' => 'Exportar',
                                'target' => '_blank'
                            ]),
            ],
        ],
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="fa fa-graduation-cap"></i> Grupos de Trabajo</h3>',
            'type' => GridView::TYPE_DEFAULT,
        ],
    ]);   
    ?>
    
</div>


