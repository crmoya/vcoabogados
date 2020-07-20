<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<?php $form = ActiveForm::begin(['id' => 'invitar-form']); ?>
<br/><br/><br/>

<div class="container-fluid">
    <div class="row alert-danger">
<?= Yii::$app->session->getFlash("error") ?>
    </div>
    <div class="row alert-success">
<?= Yii::$app->session->getFlash("exito") ?>
    </div>
    <div class="row">
        <div class="col-md-6">
            <h4>Mis Documentos Word y Excel disponibles para compartir</h4>
        </div>
        <div class="col-md-6">
            <div class='row comandos'>
                <div class="btn btn-default createXls">
                    Nuevo Excel <i class="fa fa-file-excel-o"></i>
                </div>
                <div class="btn btn-default createDoc">
                    Nuevo Word <i class="fa fa-file-word-o"></i>
                </div>
                <div class="btn btn-default uploadXls">
                    Subir Excel <i class="fa fa-file-excel-o"></i>
                </div>
                <div class="btn btn-default uploadDoc">
                    Subir Word <i class="fa fa-file-word-o"></i>
                </div>
            </div>
        </div>
    </div>
    <img style="display:none;" src="img/animacion.gif" class="animacion"/>
    <div class="row desktop">
        <div class='col-md-12'>
            <div class='row' id="documentos">
                <img style="left:400px;" src="img/Spinner.svg"/>
            </div>
        </div>

    </div>
</div>
<div class="jumbotron telon" style="display:none;">
    <div class="formCreateXls" style='display: none;'>
        <p>Nombre del archivo XLS que crearás:</p>
        <input id="nameXls" type="text" class="form-control"/><br/>
        <div class="btn btn-success" id="crearXls">Crear</div>
        <div class="btn btn-danger cancelar">Cancelar</div>
    </div>
    <div class="formCreateDoc" style='display: none;'>
        <p>Nombre del archivo DOC que crearás:</p>
        <input id="nameDoc" type="text" class="form-control"/><br/>
        <div class="btn btn-success" id="crearDoc">Crear</div>
        <div class="btn btn-danger cancelar">Cancelar</div>
    </div>
    <div class="formUploadDoc" style='display: none;'>
        <p>Archivo DOC que subirás:</p>
        <form enctype="multipart/form-data" method="post" id='formDoc'>
            <input class='form-control' type="file" id="fileDoc" required/><br/><br/>
            <div class="btn btn-success" id="subirDoc">Subir Documento</div>
            <div class="btn btn-danger cancelar">Cancelar</div>
        </form>
        <div></div>
    </div>
    <div class="formUploadXls" style='display: none;'>
        <p>Archivo XLS que subirás:</p>
        <form enctype="multipart/form-data" method="post" id='formXls'>
            <input class='form-control' type="file" id="fileXls" required/><br/><br/>
            <div class="btn btn-success" id="subirXls">Subir Documento</div>
            <div class="btn btn-danger cancelar">Cancelar</div>
        </form>
    </div>
</div>
<?php ActiveForm::end(); ?>
<style>
    .comandos{
        margin: 10px;
    }
    .delete{
        color:red;
        position:relative;
        margin-left: 70px;
    }
    .delete:hover{
        cursor:pointer;
    }
    .document{
        border-radius: 5px;
        margin-top:5px;
        margin-bottom:5px;
        text-align:center;
    }
    .document:hover{
        cursor:pointer;
    }
    .animacion{
        position:absolute;
    }
    .desktop{
        background:#f5f5f5;
        height:500px;
        width:100%;
        border-radius: 10px;
        margin-bottom: 15px;
        padding:10px;
        overflow: auto;
    }
    .container-fluid{
        background: #F2F2F2;
        width:90%;
        margin:0 auto;
        height:100%;
        border-radius:10px;
    }
    .quitar:hover{
        cursor:pointer;
    }
    .email{
        margin-top: 5px;
        margin-left: -20px;
    }
    .jumbotron{
        position:absolute;
        top:200px;
        width:100%;
        background:black;
        opacity: 0.9;
        color:white;
        font-size: 10pt;
    }
    .formCreateXls{
        width:500px;
        margin:0 auto;
    }
    .formUploadXls{
        width:350px;
        margin:0 auto;
    }
    .formUploadDoc{
        width:350px;
        margin:0 auto;
    }
    .formCreateDoc{
        width:500px;
        margin:0 auto;
    }
</style>
<?php
$this->registerJsFile(
        '@web/js/pages/documents.js', ['depends' => [\yii\web\JqueryAsset::className()]]
);
?>
