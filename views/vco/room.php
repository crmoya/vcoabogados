<?php

use yii\helpers\Html;
?>
<style>
    .media-box video {
        height: 100% !important;
    }

    .participante {
        height: 500px;
        display: inline-block;
        background: red;
    }

    .container {
        height: 100% !important;
        margin: 0 auto;
        text-align: center;
    }

    .media-box h2 {
        width: 180px;
        line-height: 1rem;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    #participantes {
        max-width: 1050px !important;
        width: 1050px !important;
        text-align: left;
        margin-top: 10px;
        border-radius: 10px;
    }

    #chat {
        height: 500px;
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
<div class="row">
    <div class="col-md-3">
        <?php 
        $mensaje = "SALIR DE LA REUNIÓN";
        if(Yii::$app->user->can("abogado")){
            $mensaje = "TERMINAR LA REUNIÓN Y SALIR";
        }
        echo Html::a($mensaje, ['/vco/close'], ['class'=>'btn btn-danger']);
        ?>
    </div>
</div>
<link rel="stylesheet" href="<?= Yii::getAlias('@web'); ?>/css/getHTMLMediaElement.css">
<script src="<?= Yii::getAlias('@web'); ?>/js/RTCMultiConnection.js"></script>
<script src="https://vast-hamlet-25601.herokuapp.com/socket.io/socket.io.js"></script>
<script src="<?= Yii::getAlias('@web'); ?>/js/getHTMLMediaElement.js"></script>
<script src="https://cdn.webrtc-experiment.com/common.js"></script>
<script src="<?= Yii::getAlias('@web'); ?>/js/adapter.js"></script>
<script src="<?= Yii::getAlias('@web'); ?>/js/md5.min.js"></script>
<script src="https://unpkg.com/sweetalert2@7.0.8/dist/sweetalert2.all.js"></script>

<?php


$abogado = $reunion->abogado->username;
$participante = $reunion->participante->username;

$script = <<< JS

var transmitiendo = true;
var miStream = null;

$(document).ready(function (e) {
  
    var conectados = 0;
    var server = 'https://vast-hamlet-25601.herokuapp.com/';
        
//VARIABLES QUE SE USARÁN

    var alto = $(window).height() - 200;
    var ancho = $("#row-participantes").width() / 2;

    if(alto > ancho){
        alto = ancho;
    }

    $('.participante').height(alto);
    
    var recibiendo = false;
    var width = alto;
    var height = width;
    var tipo = "$tipo";
    var reunion = "$abogado";
    var abogado = "$abogado";
    var participante = "$participante";
    
    window.enableAdapter = true; // enable adapter.js
    
//END VARIABLES QUE SE USARÁN
    var connection = new RTCMultiConnection();
    connection.bandwidth = {
        audio: 50
    };

    var videoConstraints = {
        mandatory: {
            minWidth: width,
            maxWidth: width,
            minHeight: height,
            maxHeight: height
        },
        optional: []
    };

    connection.socketURL = server;
    connection.socketMessageEvent = 'video-conference-demo';
    
    connection.session = {
        audio: true,
        video: true
    };

    connection.sdpConstraints.mandatory = {
        OfferToReceiveAudio: true,
        OfferToReceiveVideo: true
    };

    connection.mediaConstraints = {
        audio: true,
        video: videoConstraints,
    };

    

//ABRIR O UNIRSE A REUNIÓN
    if(tipo == 'abogado'){
        connection.userid = abogado;
        try{
            connection.checkPresence(reunion, function(isRoomExist, roomid) {
                if (isRoomExist === true) {
                    connection.closeSocket();
                } 
                connection.openOrJoin(roomid);
            });
        }catch(ex){
            alert('Error, por favor reintente.');
        }

    }
    if(tipo == 'participante'){
        connection.userid = participante;
        connection.openOrJoin(reunion);
    }
//END ABRIR O UNIRSE A REUNIÓN


//DESCONEXIÓN DEL CONSULTOR
    if(tipo == 'abogado')
    {
        connection.socket.on('disconnect',function(event){
            //setEstado(false);
        });
        
    }
  
    if(tipo == 'participante'){
        connection.onUserStatusChanged = function(event) {
            var isOffline = event.status === 'offline';
            if(event.userid == abogado && isOffline){
                window.location = 'index.php';
            }
        };
    }
    
//END DESCONEXIÓN DEL CONSULTOR


//END DESCONEXIÓN DE ALGÚN PARTICIPANTE

//STREAMING
    
    connection.onstream = function (event) {
        if(event.userid != abogado){
          conectados++;
        }
        recibiendo = true;

        event.mediaElement.removeAttribute('src');
        event.mediaElement.removeAttribute('srcObject');

        var who = event.userid;
        var video = document.createElement('video');
        video.controls = true;
        video.srcObject = event.stream;
        connection.videosContainer = document.getElementById('participantes');
        
        var buttons = ['full-screen'];
        if (event.type === 'local') {
            video.muted = true;
        }
        var mediaElement = getHTMLMediaElement(video, {
            title: who,
            buttons: buttons,
            width: width,
            height: height,
            showOnMouseEnter: false,
            isLocal: event.type,
        });

        connection.videosContainer.appendChild(mediaElement);

        setTimeout(function () {
            mediaElement.media.play();
        }, 5000);

        mediaElement.id = event.streamid;

    };

    connection.onstreamended = function (event) {
        var mediaElement = document.getElementById(event.streamid);
        if (mediaElement) {
            mediaElement.parentNode.removeChild(mediaElement);
        }
    };
//END STREAMING


});
JS;
$this->registerJs($script);