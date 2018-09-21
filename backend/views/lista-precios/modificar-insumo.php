<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\widgets\Growl;
use kartik\daterange\DateRangePicker;

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

<?php $form = ActiveForm::begin(); ?>
<div class="modal-content">
    <div class="modal-header">
        <h1 class="modal-title">Modificar Insumo</h1>   
    </div>
    <div class="modal-body">
        <div class="form-group">
            <?= $form->field($model, 'PrecioLista', ['addon' => ['prepend' => ['content'=>'$']]])->textInput(['value'=>$Insumo[0]['PrecioLista']]) ?>
            <?= 
                $form->field($model, 'FechaUltimaActualizacion', [
                    'addon'=>['prepend'=>['content'=>'<i class="glyphicon glyphicon-calendar"></i>']],
                    'options'=>['class'=>'drp-container form-group']
                ])->widget(DateRangePicker::classname(), [
                     'model' => $model,
                     'attribute' => 'FechaUltimaActualizacion',
                     'useWithAddon'=>true,
                     'pluginOptions'=>[
                        'singleDatePicker'=>true,
                        'showDropdowns'=>true
                    ]
                ]); 
            ?>
        </div>
        <div class="modal-footer">
            <?= html::submitButton('Modificar',['class'=>'btn btn-success pull-right']); ?>
        </div>
    </div>
</div>
<?php ActiveForm::end() ?>
