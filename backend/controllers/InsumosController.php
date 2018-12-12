<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use app\models\GestorInsumos;
use app\models\GestorFamilias;
use app\models\GestorSubFamilias;
use app\models\Insumos;
use app\models\InsumosBuscar;
use kartik\mpdf\pdf;


class InsumosController extends Controller
{   
    public function actionListar()
    {       
        $gestor = new GestorInsumos;
        $gestorf = new GestorFamilias;
        $gestorsf = new GestorSubFamilias;
        $searchModel = new InsumosBuscar;
        $unidades = $gestor->ListarUnidades();
        $listDataU = ArrayHelper::map($unidades,'IdUnidad','Abreviatura');
        $pIdGT = Yii::$app->user->identity['IdGT'];
        $familias = $gestorf->Listar($pIdGT);
        $listDataF = ArrayHelper::map($familias,'IdFamilia','Familia');
        $subfamilias = $gestorsf->Listar($pIdGT);
        $listDataSF = ArrayHelper::map($subfamilias,'IdSubFamilia','SubFamilia');
        if($searchModel->load(Yii::$app->request->get()) && $searchModel->validate())
        {
            $pInsumo = $searchModel['Insumo'];
            $pTipoInsumo = $searchModel['TipoInsumo'];
            $pIdFamilia = $searchModel['Familia'][0];
            $pIdSubFamilia = $searchModel['SubFamilia'][0];
            $pIdUnidad = $searchModel['Abreviatura'][0];
            $insumos = $gestor->Buscar($pInsumo, $pTipoInsumo, $pIdFamilia, $pIdSubFamilia, $pIdUnidad, $pIdGT);
            $dataProvider = new ArrayDataProvider([
                'allModels' => $insumos,
                'pagination' => ['pagesize' => 9,],
            ]);
            return $this->render('listar',['dataProvider' => $dataProvider, 'searchModel' => $searchModel, 'listDataU' => $listDataU, 'listDataF' => $listDataF, 'listDataSF' => $listDataSF]);
        }
        else{
            $insumos = $gestor->Listar($pIdGT);
            $dataProvider = new ArrayDataProvider([
                'allModels' => $insumos,
                'pagination' => ['pagesize' => 9,],
            ]);
            return $this->render('listar',['dataProvider' => $dataProvider, 'searchModel' => $searchModel, 'listDataU' => $listDataU, 'listDataF' => $listDataF, 'listDataSF' => $listDataSF]);
        }
    }
    
    
    public function actionAlta()
    {
        $model = new Insumos;
        $model->scenario = 'alta-insumo';
        $gestor = new GestorInsumos;
        $gestorsf = new GestorSubFamilias;
        $pIdGT = Yii::$app->user->identity['IdGT'];
        $subfamilias = $gestorsf->Listar($pIdGT);
        $listData= ArrayHelper::map($subfamilias,'IdSubFamilia','SubFamilia');
        $unidades = $gestor->ListarUnidades();
        $listDataU= ArrayHelper::map($unidades,'IdUnidad','Abreviatura');
        if($model->load(Yii::$app->request->post()) && ($model->validate()))
        {
            $pIdSubFamilia = $model->IdSubFamilia;
            $pIdUnidad = $model->IdUnidad;
            $pInsumo = $model->Insumo;
            $pTipoInsumo = $model->TipoInsumo;
            $mensaje = $gestor->Alta($pInsumo, $pIdGT, $pTipoInsumo, $pIdSubFamilia, $pIdUnidad);
            return $mensaje[0]['Mensaje'];
        }
        else{
            return $this->renderAjax('alta',['model' => $model, 'listData' => $listData, 'listDataU' => $listDataU]);
        }
    }
    
    public function actionModificar()
    {
        $model = new Insumos;
        $gestor = new GestorInsumos;
        $pIdGT = Yii::$app->user->identity['IdGT'];
        $pIdInsumo = Yii::$app->request->get('IdInsumo');
        $insumo = $gestor->Dame($pIdInsumo, $pIdGT);
        if($model->load(Yii::$app->request->post()) && ($model->validate()))
        {
            $pInsumo = $model->Insumo;
            $pTipoInsumo = $model->TipoInsumo;
            $mensaje = $gestor->Modificar($pIdInsumo, $pIdGT, $pInsumo, $pTipoInsumo);
            if(substr($mensaje[0]['Mensaje'], 0, 2) === 'OK') {
                Yii::$app->session->setFlash('alert',$mensaje[0]['Mensaje']);
                return $this->redirect('/sgpoc/backend/web/insumos/listar');
            }
            else {
                return $mensaje[0]['Mensaje'];
            }
        }
        else{
           return $this->renderAjax('modificar',['model' => $model, 'insumo' => $insumo]);
        }
    }
    
    public function actionBorrar() {
        $gestor = new GestorInsumos;
        $pIdGT = Yii::$app->user->identity['IdGT'];
        $pIdInsumo = Yii::$app->request->get('IdInsumo');
        $mensaje = $gestor->Borrar($pIdInsumo, $pIdGT);
        Yii::$app->session->setFlash('alert',$mensaje[0]['Mensaje']);
        return $this->redirect('/sgpoc/backend/web/insumos/listar');
    }

    public function actionExportar() {
        $gestor = new GestorInsumos;
        $pIdGT = Yii::$app->user->identity['IdGT'];
        $insumos = $gestor->Listar($pIdGT);
        $dataProvider = new ArrayDataProvider([
            'allModels' => $insumos,
        ]);
           $data = $this->renderPartial('exportar',['dataProvider' => $dataProvider]);
           Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
           $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE, 
            'destination' => Pdf::DEST_BROWSER,
            'content' => $data,
            'options' => [
                
            ],
            'methods' => [
                'SetTitle' => 'Familias',
                'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                'SetHeader' => ['Insumos||Generado el: ' . date("r")],
                'SetFooter' => ['|Page {PAGENO}|'],
                'SetAuthor' => 'Kartik Visweswaran',
                'SetCreator' => 'Kartik Visweswaran',
                'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);
        return $pdf->render();
     }
    
}
