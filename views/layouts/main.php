<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    
    <link rel="icon" type="image/png" sizes="32x32" href="<?= Yii::getAlias('@web');?>/img/favicon.png">
    
    <link rel="stylesheet" href="<?= Yii::getAlias('@web');?>/css/getHTMLMediaElement.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css">
    <link href="<?= Yii::getAlias('@web');?>/css/menu.css" rel="stylesheet" type="text/css">
    
    <script src="<?= Yii::getAlias('@web');?>/js/RTCMultiConnection.js"></script>
    <script src="https://rtcmulticonnection.herokuapp.com/socket.io/socket.io.js"></script>
    <script src="<?= Yii::getAlias('@web');?>/js/getHTMLMediaElement.js"></script>
    
    <script src="https://cdn.webrtc-experiment.com/common.js"></script>
    <!--<script src="<?= Yii::getAlias('@web');?>/js/menu.js"></script>-->
    <script src="<?= Yii::getAlias('@web');?>/js/adapter.js"></script>
    <script src="<?= Yii::getAlias('@web');?>/js/md5.min.js"></script>
    <script src="https://unpkg.com/sweetalert2@7.0.8/dist/sweetalert2.all.js"></script>
    <script src="https://apis.google.com/js/api:client.js"></script>
    
    <!-- GOOGLE SIGN IN -->
    
    <script>
        
    var startApp = function() {
        gapi.load('auth2', function(){
            auth2 = gapi.auth2.init({
                client_id: '159814861874-b00cl0ea33dcjsass9no0cat4hk7v0lm.apps.googleusercontent.com',
            });
            attachSignin(document.getElementById('customBtn'));
        });
               
    };    
    var googleUser = {};
    
    function attachSignin(element) {
        
        
        auth2.attachClickHandler(element, {},
            function(googleUser) {
                
                var profile = googleUser.getBasicProfile(); 
                localStorage.setItem("email", profile.getEmail());
                localStorage.setItem("nombre", profile.getName());
                var consultores = [];
                <?php
                $i = 0;
                $consultores = app\models\Consultores::find()->all();
                foreach ($consultores as $consultor) {
                    echo "consultores[" . $i++ . "] = '" . $consultor['email'] . "';";
                }
                ?>
                var esConsultor = false;
                for(var i=0;i<consultores.length;i++){
                    if(consultores[i] == localStorage.getItem("email")){
                        esConsultor = true;
                    }
                }
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['site/cargarusuario']);?>',
                    data: {
                        email: localStorage.getItem("email"),
                        nombre: localStorage.getItem("nombre"),
                    },
                    success: function (data) {
                        if (esConsultor) {
                            localStorage.setItem('consultor',localStorage.getItem('nombre'));
                            localStorage.setItem('emailConsultor',localStorage.getItem('email'));
                            localStorage.setItem('tipo','consultor');   
                            window.location = 'index.php';
                        }
                        else{
                            //si es participante, revisar si está invitado
                            $.ajax({
                                url: '<?= \yii\helpers\Url::to(['site/estainvitado']);?>',
                                data: {
                                    email: localStorage.getItem("email"),
                                },
                                success: function (data) {
                                    if(data == 1){
                                        localStorage.setItem('tipo','participante');
                                        localStorage.setItem('participante',localStorage.getItem("nombre"));
                                        localStorage.setItem('emailParticipante',localStorage.getItem("email"));
                                        $.ajax({
                                            url: '<?= \yii\helpers\Url::to(['site/cargarconsultor']);?>',
                                            data: {},
                                            dataType: 'json',
                                            success: function (data) {
                                                localStorage.setItem('consultor',data.nombre);
                                                localStorage.setItem('emailConsultor',data.email);
                                                window.location = "index.php";
                                            },
                                            type: 'POST'
                                        });
                                    }
                                    else{
                                        cerrarSesion();
                                    }
                                },
                                type: 'POST'
                            });
                        }
                    },
                    type: 'POST'
                });
            }, function(error) {
                alert(JSON.stringify(error, undefined, 2));
            });
    }
    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }
    </script>
    
    
    <!-- GOOGLE SIGN IN -->
    
    <!-- SELECCIONAR EL MENÚ DEPENDIENDO DEL TIPO DE USUARIO -->
    <?php
    /*$this->registerJsFile(
        '@web/js/revision.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );*/
    
    $script = "
    function toggleMenu(){
        if(localStorage.getItem('tipo') == 'consultor'){
            $('.menuconsultor').show();
            $('.menuparticipante').hide();
        }
        if(localStorage.getItem('tipo') == 'participante'){
            $('.menuconsultor').hide();
            $('.menuparticipante').show();
        }
    }

    toggleMenu();
    ";
    
    $this->registerJs(
        $script,
        \yii\web\View::POS_READY
    ); 
    $this->registerJsFile(
        '@web/js/botonesmenu.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
    
    
    ?>
    <!-- END SELECCIONAR EL MENÚ DEPENDIENDO DEL TIPO DE USUARIO -->
    
    <title><?= Html::encode(Yii::$app->name) ?></title>
    <?php $this->head() ?>
    <style>
        .header{
            height:60px;
            top:-5px;
            position:fixed;
            background:white;
            width:100%;
        }
        .img-header{
            height:40px;
            margin-left:5%;
            margin-top:10px;
            margin-bottom: 5px;
        }
        #customBtn {
            display: inline-block;
            font-size: 20pt;
            border-radius: 5px;
            white-space: nowrap;
        }
        #customBtn:hover {
            cursor: pointer;
        }
        span.buttonText {
            display: inline-block;
            border-radius: 7px;
            vertical-align: middle;
            font-weight: bold;
            background:white;
            color:gray;
            border:1px silver solid;
            /* Use the Roboto font that is loaded in the <head> */
            font-family: 'Roboto', sans-serif;
        }
    </style>


    
</head>
<body>
    <?php $this->beginBody() ?>
    
    <?php 
    $session = Yii::$app->session;
    if(!$session->isActive){
        $session->open();
    }
    if($session->get('email') == '' || $session->get('email') == null){
        echo "<script>localStorage.clear();</script>";
    }
    ?>    
<!-- SIGN IN MENU -->
<br/><br/><br/>
    <div class="jumbotron iniciosesion" style="display:none;">
        <div id="gSignInWrapper">
            <div id="customBtn" class="customGPlusSignIn">
                <span class="buttonText">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Click acá para iniciar sesión con Google <img class="img-rounded" src="img/google.png"/></span>
            </div>
        </div>
    </div>
<!-- SIGN IN MENU -->  

    <?= $content ?>

<!-- MENÚ CONSULTOR -->    
    <div class="menu menuconsultor escondido sticky-top" style="display:none;">
        <div class="row">
            <div class='col-md-1'><i class="flecha fa fa-chevron-right" style="font-size:30px;" onclick="javascript:esconder();"></i></div>
            <div class="col-md-1"></div>
            <div class="col-md-9 header-menu">
                ¿Qué deseas hacer?
            </div>
        </div>
        <!-- PING 
        <div class='row row-boton'>
            <div class='col-md-1'></div>
            <div class='col-md-10'>
                <div class='btn btn-default boton ping'>
                    PING <i class="fa fa-user" aria-hidden="true"></i>
                </div>
            </div>
        </div>
         END PING -->
        <div class='row row-boton'>
            <div class='col-md-1'></div>
            <div class='col-md-10'>
                <a class='btn btn-default boton invitar' style="display:none;" href="index.php?r=site/invitar">
                    Invitar participantes <i class="fa fa-user" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        <div class='row row-boton'>
            <div class='col-md-1'></div>
            <div class='col-md-10'>
                <a class='btn btn-default boton consultores' style="display:none;" href="index.php?r=site/consultores">
                    Administrar Consultores <i class="fa fa-user" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        <div class='row row-boton'>
            <div class='col-md-1'></div>
            <div class='col-md-10'>
                <a class='btn btn-default boton vco' style="display:none;" href="index.php?r=site/vco">
                    Iniciar Video Conferencia <i class="fa fa-camera" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        <div class='row row-boton'>
            <div class='col-md-1'></div>
            <div class='col-md-10'>
                <a class='btn btn-default boton broadcast' style="display:none;" href="index.php?r=site/broadcast">
                    Iniciar Transmisión en Vivo <i class="fa fa-video-camera" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        <div class='row row-boton'>
            <div class='col-md-1'></div>
            <div class='col-md-10'>
                <a class='btn btn-default boton escritorio' style="display:none;" href="index.php?r=site/escritorio">
                    Compartir mi escritorio <i class="fa fa-desktop" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        <div class='row row-boton'>
            <div class='col-md-1'></div>
            <div class='col-md-10'>
                <a class='btn btn-default boton sheets' style="display:none;" href="index.php?r=site/documents">
                    Mis Documentos <i class="fa fa-folder-open" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        <div class='row row-boton'>
            <div class='col-md-1'></div>
            <div class='col-md-10'>
                <div class='btn btn-default boton terminar' onclick="javascript:cerrarSesion();">
                    Terminar la reunión <i class="fa fa-sign-out" aria-hidden="true"></i>
                </div>
            </div>
        </div>
        <div class='row row-boton'>
            <div class='col-md-1'></div>
            <div class='col-md-10'>
                <a class='btn btn-primary boton reconectar' href="index.php?r=site/index">
                    Reconectar <i class='fa fa-plug'></i>
                </a>
            </div>
        </div>
        <div class='row row-boton'>
            <div class='col-md-1'></div>
            <div class='col-md-4'>Estado:</div>
            <div class='col-md-5'>
                <div class='alert-danger estado' style='text-align:center;'>
                    &nbsp;Desconectado
                </div>
            </div>
        </div>
    </div>
<!-- END MENÚ CONSULTOR -->

<!-- MENÚ PARTICIPANTE -->
    <div class="menu menuparticipante escondido" style="display:none;">
        <div class="row">
            <div class='col-md-1'><i class="flecha fa fa-chevron-right" style="font-size:30px;" onclick="javascript:esconder();"></i></div>
            <div class="col-md-1"></div>
            <div class="col-md-9 header-menu">
                ¿Qué deseas hacer?
            </div>
        </div>
        <!--
        <div class='row row-boton'>
            <div class='col-md-1'></div>
            <div class='col-md-10'>
                <div class="btn btn-info boton menuf escondido flechita" onclick="javascript:toggleConsultor();" style="display:none;">
                    <i class="flex fa fa-arrow-up"></i> <span class="txtflex">Ocultar presentador</span>
                </div>
            </div>
        </div>-->
        <div class='row row-boton'>
            <div class='col-md-1'></div>
            <div class='col-md-10'>
                <div class='btn btn-danger boton transmitir' style="display:none;" onclick="javascript:transmitir();">
                    Dejar de Transmitir <i class='fa fa-video-camera'></i>
                </div>
            </div>
        </div>
        <div class='row row-boton'>
            <div class='col-md-1'></div>
            <div class='col-md-10'>
                <a class='btn btn-primary boton refrescar' style="" href="index.php?r=site/index">
                    Refrescar <i class='fa fa-refresh'></i>
                </a>
            </div>
        </div>
        <div class='row row-boton'>
            <div class='col-md-1'></div>
            <div class='col-md-10'>
                <div class='btn btn-default boton terminar' onclick="javascript:cerrarSesion();">
                    Salir de la reunión <i class="fa fa-sign-out" aria-hidden="true"></i>
                </div>
            </div>
        </div>
        <div class='row row-boton'>
            <div class='col-md-1'></div>
            <div class='col-md-4'>Estado:</div>
            <div class='col-md-5'>
                <div class='alert-danger estado' style='text-align:center;'>
                    &nbsp;Desconectado
                </div>
            </div>
        </div>
    </div>
<!-- END MENÚ CONSULTOR -->
<div class="header">
    <img class="img-header" src="<?= Yii::getAlias('@web');?>/img/logo_guiresse_e.svg"/>
</div>
<div class="menuconectados escondido" style="display: none;">
  <?php
  $invitados = app\models\Invitados::find()->all();
  foreach($invitados as $invitado):?>   
    <div participante="<?=$invitado->nombre_gmail;?>" class="row conexion alert-danger" style="padding:5px;">
      <div><?="<b>".$invitado->nombre."</b>";?> <?="(".$invitado->email.")"?></div>
    </div>
  <?php endforeach;?>
  <div class="row">
    Estado de conexión de Participantes <i class="flechaabajo fa fa-chevron-up" onclick="javascript:esconderConectados();"></i>
  </div>
</div>
    
    
    <?php $this->endBody() ?>
    <script>
        window.onload = function() {
            startApp();
            if(localStorage.getItem('email') == null){
                $('.iniciosesion').show();
            }
            else{
                $('.bienvenido').show();
            }
        };
    </script>
</body>
</html>
<?php $this->endPage() ?>
