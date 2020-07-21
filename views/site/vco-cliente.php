<style>
    .media-box video{
        height:100% !important;
    }
    .participante{
        height:500px;
        display:inline-block;
        background:red;
    }
    .container{
        height:100% !important;
        margin:0 auto;
        text-align:center;
    }
    .media-box h2{
        width:180px;
        line-height: 1rem;
        overflow:hidden;
        white-space:nowrap;
        text-overflow: ellipsis;
    }
    #participantes{
        text-align: left;
        margin-top:10px;
        border-radius: 10px;
    }
    #chat{
        height:500px;
        background: yellow;
    }
</style>
<div class="row">
    <div id="row-participantes" class="col-md-9">
        <div id="participantes">
        </div>
    </div>
    <div id="chat" class="col-md-3">

    </div>
</div>
<?php
$this->registerJsFile(
    '@web/js/pages/vco-cliente.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);
?>