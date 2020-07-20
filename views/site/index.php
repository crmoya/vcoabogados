<div class="site-index">
    <div class="jumbotron bienvenido" style="display: none;">
        <img id="fondo" src="img/fondoOscuro.png" style="max-width: 1000px;" class="img-rounded responsive"/>
    </div>
    <div class="jumbotron bienvenido" style="display: none;">
        <img src="img/animacion.gif" class="animacion"/>
    </div>
</div>
<style>
    .animacion{
        margin-top: -700px;
    }
</style>
<?php
$this->registerJsFile(
    '@web/js/pages/index.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
?>