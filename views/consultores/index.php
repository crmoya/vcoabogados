<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ConsultoresSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Consultores';
$this->params['breadcrumbs'][] = $this->title;
?>
<br/><br/><br/>
<div class="consultores-index">
    <br/>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">

            <p>
                <?= Html::a('Crear Consultor', ['create'], ['class' => 'btn btn-success']) ?>
            </p>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'email:email',

                    ['class' => 'yii\grid\ActionColumn', 'template' => '{delete}'],
                ],
            ]); ?>
        </div>
    </div>
</div>
<?php
$this->registerJsFile(
    '@web/js/pages/index.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
?>