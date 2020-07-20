<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Consultores */

$this->title = 'Create Consultores';
$this->params['breadcrumbs'][] = ['label' => 'Consultores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<br/><br/><br/>
<div class="consultores-create">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-7">
                <h3>Nuevo consultor</h3>

                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>

    </div>
</div>
<?php
$this->registerJsFile(
    '@web/js/pages/index.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
?>