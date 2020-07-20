<style>
    video {
        object-fit: fill;
        width: 100%;
    }
    #videos-container{
        margin:0 auto;
        text-align: center;
    }
    .flechita:hover{
        cursor:pointer;
    }
</style>
<br/><br/><br/>
<div id="videos-container"></div>
<div id="audios-container"></div>
<div class="share">
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
<script src="https://cdn.webrtc-experiment.com:443/getScreenId.js"></script>
<script src="js/getMediaElement.js"></script>
<?php
$this->registerJsFile(
    '@web/js/pages/escritorio.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
    '@web/js/botonescritorio.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
?>