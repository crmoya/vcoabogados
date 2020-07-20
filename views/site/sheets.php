<iframe id="if" frameborder="0" src="" width="100%" height="90%"></iframe>
<?php
$this->registerJsFile(
    '@web/js/pages/sheets.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
?>
<div class="tapa"></div>
<style>
    .tapa{
        background:#fafaf9;
        width:60px;
        height:30px;
        position:fixed;
        right:0px;
        top:62px;
    }
    #if{
        margin-top: -60px;
    }
</style>
