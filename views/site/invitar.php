<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<?php $form = ActiveForm::begin(['id' => 'invitar-form']); ?>
                <br/><br/><br/>
<div class="container-fluid">
    <div class="row alert-danger">
        <?=Yii::$app->session->getFlash("error")?>
    </div>
    <div class="row alert-success">
        <?=Yii::$app->session->getFlash("exito")?>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h3>Invitar participantes a esta reunión</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'email')->textInput(['autofocus' => true,'placeholder'=>"Correo Electrónico del Invitado"])->label(false) ?>
        </div>
        <div class="col-md-4 email">
            @gmail.com
        </div>
    </div>
    <br/>
    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'nombre')->textInput(['autofocus' => true,'placeholder'=>"Nombre del Invitado"])->label(false) ?>
        </div>
        <div class="col-md-2">
            <?= Html::submitButton('Invitar<i class="fa fa-user-plus" style="font-size: 17px; color:green;"></i>', ['class' => 'btn btn-default', 'name' => 'contact-button']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-11">
            <table class='table table-hover table-striped table-responsive'>
                <thead>
                    <tr>
                        <th>E-mail</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $usuarios = \app\models\Invitados::find()->all();
                    foreach ($usuarios as $usuario):
                        ?>
                        <tr>
                            <td>
                                <?= $usuario->email ?>
                            </td>
                            <td>
                                <?= $usuario->nombre ?>
                            </td>
                            <?php if($usuario->conectado==0):?>
                                <td style="color:red;">Desconectado <i class="fa fa-chain-broken"></i></td>
                            <?php else:?>
                                <td style="color:green;">En línea <i class="fa fa-chain"></i></td>
                            <?php endif;?>
                            <td class="quitar" onclick="javascript:quitar('<?= $usuario->email ?>');">
                                <i class='fa-minus-circle fa' style='font-size:17px;color:red;'></i>
                            </td>
                        </tr>
                        <?php
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<style>
    .container-fluid{
        background: #F2F2F2;
        width:90%;
        margin:0 auto;
        height:100%;
        border-radius: 10px;
    }
    .quitar:hover{
        cursor:pointer;
    }
    .email{
        margin-top: 5px;
        margin-left: -20px;
    }
</style>
<?php
$this->registerJsFile(
    '@web/js/pages/invitar.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
?>
