<style>
    .media-box video{
        height:100% !important;
    }
    .filatop{
        width:90%;
        padding:0px;
        margin-left:-15px;
    }
    #yo{
        text-align: left;
        padding:0px;
        margin:0px;
    }
    #consultor{
        text-align:right;
        padding:0px;
    }
    .participante{
        width:200px;
        height:200px;
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
        max-width: 1050px !important;
        width:1050px !important;
        text-align: left;
        border:1px silver solid;
        margin-top:10px;
        border-radius: 10px;
    }
    .col-md-2{
        margin-left: 10px !important;
        margin-bottom: 10px !important;
    }
    .col-md-3{
        margin-left: -15px;
    }
</style>
<br/><br/><br/>
<div class="row filatop">
    <div class="col-md-3"></div>
    <div class="col-md-3">
        <div id="yo" class="col-md-6">
        </div>
    </div>
    <div class="col-md-6">
        <div id="consultor" class="col-md-6">
        </div>    
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div id="participantes">
        </div>
    </div>
</div>
<?php
$this->registerJsFile(
    '@web/js/pages/vco.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
?>