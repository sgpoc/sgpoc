<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;
use app\models\GestorObras;
use app\models\GestorComputosMetricos;
use app\models\GestorPresupuestos;
use app\models\PresupuestosBuscar;
use app\models\Presupuestos;
use app\models\LineaPresupuestos;
use app\models\GestorListaPrecios;
use app\models\GestorProveedores;

class PresupuestosController extends Controller
{   
    public function actionListar() {
        $gestor = new GestorPresupuestos;
        $gestoro = new GestorObras;
        $searchModel = new PresupuestosBuscar;
        $pIdGT = Yii::$app->user->identity['IdGT'];
        $obras = $gestoro->Listar($pIdGT);
        $listDataO = ArrayHelper::map($obras,'IdObra','Obra');
        if($searchModel->load(Yii::$app->request->get()) && $searchModel->validate())
        {
            $pIdObra= $searchModel['Obra'][0];
            $pFechaDePresupuesto = $searchModel['FechaDePresupuesto'];
            $presupuestos = $gestor->Buscar($pIdObra, $pFechaDePresupuesto, $pIdGT);
            $dataProvider = new ArrayDataProvider([
                'allModels' => $presupuestos,
                'pagination' => ['pagesize' => 5,],
            ]);
            return $this->render('listar',['dataProvider' => $dataProvider, 'searchModel' => $searchModel, 'listDataO' => $listDataO]);
        }
        else{
            $presupuestos = $gestor->Listar($pIdGT);
            $dataProvider = new ArrayDataProvider([
                'allModels' => $presupuestos,
                'pagination' => ['pagesize' => 5,],
            ]);
            return $this->render('listar',['dataProvider' => $dataProvider, 'searchModel' => $searchModel, 'listDataO' => $listDataO]);
        }
    }
           
    public function actionAlta() {
        $model = new Presupuestos;
        $modellinea = new LineaPresupuestos;
        $model->scenario = 'alta-presupuesto';
        $gestor = new GestorPresupuestos;
        $gestoro = new GestorObras;
        $gestorp = new GestorProveedores;
        $gestorlp = new GestorListaPrecios;
        $pIdGT = Yii::$app->user->identity['IdGT'];
        $obras = $gestoro->Listar($pIdGT);//sacar bajasy finalizadas
        $listDataO = ArrayHelper::map($obras,'IdObra','Obra');
        $proveedores = $gestorp->Listar($pIdGT);//sacar bajas
        $listDataP = ArrayHelper::map($proveedores,'IdProveedor','Proveedor');
        $localidades = $gestorlp->ListarLocalidades();
        $listDataL = ArrayHelper::map($localidades,'IdLocalidad','Localidad');
        if($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $lineapresupuesto = Yii::$app->request->post('LineaPresupuestos');
            $pIdLocalidad = $lineapresupuesto['IdLocalidad'];
            $pIdObra= $model->IdObra;
            $pIdComputoMetrico = $model->IdComputoMetrico;
            $pFechaDePresupuesto = $model->FechaDePresupuesto;
            $mensaje = $gestor->Alta($pIdComputoMetrico, $pIdObra, $pIdLocalidad, $pFechaDePresupuesto);
            return $mensaje[0]['Mensaje'];
        }
        else{
            return $this->renderAjax('alta',['model' => $model, 'modellinea' => $modellinea, 'listDataO' => $listDataO, 'listDataP' => $listDataP, 'listDataL' => $listDataL]);
        }
    }
    
    public function actionModificar() {
        $model = new Presupuestos;
        $gestor = new GestorPresupuestos;
        $pIdPresupuesto = Yii::$app->request->get('IdPresupuesto');
        $presupuesto = $gestor->Dame($pIdPresupuesto);
        if($model->load(Yii::$app->request->post()) && ($model->validate()))
        {
            $pFechaDePresupuesto = $model->FechaDePresupuesto;
            $mensaje = $gestor->Modificar($pIdPresupuesto, $pFechaDePresupuesto);
            if(substr($mensaje[0]['Mensaje'], 0, 2) === 'OK')
            {
                Yii::$app->session->setFlash('alert',$mensaje[0]['Mensaje']);
                return $this->redirect('/sgpoc/backend/web/presupuestos/listar');
            }
            else{
                return $mensaje[0]['Mensaje'];
            }
        }
        else{
           return $this->renderAjax('modificar',['model' => $model, 'presupuesto' => $presupuesto]);
        }
    }
    
    public function actionBorrar() {
        $gestor = new GestorPresupuestos;
        $pIdPresupuesto = Yii::$app->request->get('IdPresupuesto');
        $mensaje = $gestor->Borrar($pIdPresupuesto);
        Yii::$app->session->setFlash('alert',$mensaje[0]['Mensaje']);
        return $this->redirect('/sgpoc/backend/web/presupuestos/listar');
    }
    
    public function actionListarInsumos() {
        $gestor = new GestorPresupuestos;
        $gestoro = new GestorObras;
        $searchModel = new PresupuestosBuscar;
        $pIdGT = Yii::$app->user->identity['IdGT'];
        $obras = $gestoro->Listar($pIdGT);
        $listDataO = ArrayHelper::map($obras,'IdObra','Obra');
        if($searchModel->load(Yii::$app->request->get()) && $searchModel->validate())
        {
            $pIdObra= $searchModel['Obra'][0];
            $pFechaDePresupuesto = $searchModel['FechaDePresupuesto'];
            $presupuestos = $gestor->Buscar($pIdObra, $pFechaDePresupuesto, $pIdGT);
            $dataProvider = new ArrayDataProvider([
                'allModels' => $presupuestos,
                'pagination' => ['pagesize' => 5,],
            ]);
            return $this->render('listar-insumos',['dataProvider' => $dataProvider, 'searchModel' => $searchModel, 'listDataO' => $listDataO]);
        }
        else{
            $presupuestos = $gestor->Listar($pIdGT);
            $dataProvider = new ArrayDataProvider([
                'allModels' => $presupuestos,
                'pagination' => ['pagesize' => 5,],
            ]);
            return $this->render('listar-insumos',['dataProvider' => $dataProvider, 'searchModel' => $searchModel, 'listDataO' => $listDataO]);
        }
    }
    
    public function actionModificarPorcentajes() {
        $model = new LineaPresupuestos;
        $model->scenario = 'modificar-porcentajes';
        $gestor = new GestorPresupuestos;
        $pIdPresupuesto = Yii::$app->request->get('IdPresupuesto');
        $pIdInsumo = Yii::$app->request->get('IdInsumo');
        $lineapresupuesto = $gestor->DameLinea($pIdPresupuesto, $pIdInsumo);
        if($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $pBeneficios = $model->Beneficios;
            $pGastosGenerales = $model->GastosGenerales;
            $pCargasSociales = $model->CargasSociales;
            $pIVA = $model->IVA;
            $mensaje = $gestor->ModificarPorcentajes($pIdPresupuesto, $pIdInsumo, $pBeneficios, $pGastosGenerales, $pCargasSociales, $pIVA);
            if(substr($mensaje[0]['Mensaje'], 0, 2) === 'OK')
            {
                Yii::$app->session->setFlash('alert',$mensaje[0]['Mensaje']);
                return $this->redirect('/sgpoc/backend/web/presupuestos/listar-insumos');
            }
            else{
                return $mensaje[0]['Mensaje'];
            }
        }
        else{
            return $this->renderAjax('modificar-porcentajes',['model' => $model, 'lineapresupuesto' => $lineapresupuesto]);
        }
    }
    
    public function actionEleccionPrecio() {
        $model = new LineaPresupuestos;
        $model->scenario = 'eleccion-precio';
        $gestor = new GestorPresupuestos;
        $gestorp = new GestorProveedores;
        $gestorlp = new GestorListaPrecios;
        $pIdGT = Yii::$app->user->identity['IdGT'];
        $pIdPresupuesto = Yii::$app->request->get('IdPresupuesto');
        $pIdInsumo = Yii::$app->request->get('IdInsumo');
        $proveedores = $gestorp->Listar($pIdGT);
        $listDataP = ArrayHelper::map($proveedores,'IdProveedor','Proveedor');
        $localidades = $gestorlp->ListarLocalidades();
        $listDataL = ArrayHelper::map($localidades,'IdLocalidad','Localidad');
        if($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $pIdProveedor = $model->IdProveedor;
            $pIdLocalidad = $model->IdLocalidad;
            $mensaje = $gestor->EleccionPrecio($pIdPresupuesto, $pIdInsumo, $pIdProveedor, $pIdLocalidad);
            if(substr($mensaje[0]['Mensaje'], 0, 2) === 'OK')
            {
                Yii::$app->session->setFlash('alert',$mensaje[0]['Mensaje']);
                return $this->redirect('/sgpoc/backend/web/presupuestos/listar-insumos');
            }
            else{
                return $mensaje[0]['Mensaje'];
            }
        }
        else{
            return $this->renderAjax('eleccion-precio',['modellinea' => $model, 'listDataP' => $listDataP, 'listDataL' => $listDataL]);
        }
    }
    
}