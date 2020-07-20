<style>
    .container{
        height:500px !important;
        width:1050px !important;
        margin:0 auto;
        text-align:center;
    }
    .media-container{
        height:100% !important;
    }
</style>
<div class="jumbotron">
    <div id="consultor">
    </div>
</div>
<?php
$this->registerJsFile(
    '@web/js/pages/broadcast.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
?>