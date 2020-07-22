<?php

use yii\helpers\Html;
use yii\helpers\Url;

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
        background-color: #E2E2E2;
        border: solid 1px silver;
        border-radius: 3px;
    }
    .titulo-chat{
        background-color: silver;
        border-bottom:1px solid gray;
    }
    .fila-envio, #salir{
        margin-top:10px;
    }
    #texto{
        font-size: 8pt;
        height: 35px;
        width: 100%;
    }
    .linea-mensaje{
        text-align: left;
        margin-left: 5px !important;
    }
    #mensajes{
        overflow-y: auto;
        overflow-x: hidden;
    }
</style>






<div class="row">
    <div id="row-participantes" class="col-md-9">
        <div id="participantes">
        </div>
    </div>
    <div id="chat" class="col-md-3">
        <div class="row">
            <p class="titulo-chat">Apuntes de la reunión</p>
        </div>
        <div class="row" id="mensajes">

        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <?php 
        $mensaje = "SALIR DE LA REUNIÓN";
        if(Yii::$app->user->can("abogado")){
            $mensaje = "TERMINAR LA REUNIÓN Y SALIR";
        }
        echo Html::a($mensaje, ['/vco/close'], ['class'=>'btn btn-danger','id'=>"salir"]);
        ?>
    </div>
    <div class="col-md-6"></div>
    <div class="col-md-3">
        <div class="row">
            <div class="fila-envio col-sm-8">
                <input type="text" id="texto" placeholder="Escribir acá..."/>
            </div>
            <div class="col-sm-4">
            <div id="enviar" class="fila-envio btn btn-success">Enviar</div>
            </div>
        </div>
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
$url = Url::to(["vco/disconnected"]);
$urlMensaje = Url::to(["vco/message"]);

$script = <<< JS

var transmitiendo = true;
var miStream = null;

$(document).ready(function (e) {
    
    var conectados = 0;
    var server = 'https://vast-hamlet-25601.herokuapp.com/';
        
//VARIABLES QUE SE USARÁN

    var alto = $(window).height() - 200;
    $('#mensajes').height(alto-80);
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
    var reunion_id = '$reunion->id';
    
    window.enableAdapter = true; // enable adapter.js
    
//END VARIABLES QUE SE USARÁN
    var connection = new RTCMultiConnection();
    connection.bandwidth = {
        audio: 50
    };

    var bitrates = 512;
    var resolutions = 'Ultra-HD';
    var videoConstraints = {};

    if (resolutions == 'HD') {
        videoConstraints = {
            width: {
                ideal: 1280
            },
            height: {
                ideal: 720
            },
            frameRate: 30
        };
    }

    if (resolutions == 'Ultra-HD') {
        videoConstraints = {
            width: {
                ideal: 1920
            },
            height: {
                ideal: 1080
            },
            frameRate: 30
        };
    }

    connection.mediaConstraints = {
        video: videoConstraints,
        audio: true
    };

    var CodecsHandler = connection.CodecsHandler;

    connection.processSdp = function(sdp) {
        var codecs = 'vp8';

        if (resolutions == 'HD') {
            sdp = CodecsHandler.setApplicationSpecificBandwidth(sdp, {
                audio: 128,
                video: bitrates,
                screen: bitrates
            });

            sdp = CodecsHandler.setVideoBitrates(sdp, {
                min: bitrates * 8 * 1024,
                max: bitrates * 8 * 1024,
            });
        }

        if (resolutions == 'Ultra-HD') {
            sdp = CodecsHandler.setApplicationSpecificBandwidth(sdp, {
                audio: 128,
                video: bitrates,
                screen: bitrates
            });

            sdp = CodecsHandler.setVideoBitrates(sdp, {
                min: bitrates * 8 * 1024,
                max: bitrates * 8 * 1024,
            });
        }

        return sdp;
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
                connection.open(roomid);
            });
        }catch(ex){
            alert('Error, por favor reintente.');
        }

    }
    if(tipo == 'participante'){
        connection.userid = participante;
        connection.join(reunion);
    }
//END ABRIR O UNIRSE A REUNIÓN


//DESCONEXIÓN
    connection.onUserStatusChanged = function(event) {
        var isOffline = event.status === 'offline';
        if(isOffline){
            window.location = '$url';
        }
    };
    
//END DESCONEXIÓN


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


//ENVÍO DE MENSAJES AL CHAT
connection.connectSocket(function(socket) {
    $('#enviar').click(function(e){
        var mensaje = { 
            remitente: connection.userid, 
            texto: $('#texto').val(),
            reunion: reunion_id,
        };
        imprimirMensaje(mensaje);
        $('#texto').val('');
        connection.socket.emit(connection.socketCustomEvent, mensaje);
        mensajeBD(mensaje);
    });
    connection.socket.on(connection.socketCustomEvent, function(event) {
        imprimirMensaje({remitente: event.remitente, texto: event.texto, reunion: reunion_id});
    });
});
    
//END ENVÍO DE MENSAJES AL CHAT

//GUARDAR MENSAJE EN BD
function mensajeBD(mensaje){
    $.ajax({
        url: '$urlMensaje',
        data: {
            texto: mensaje.texto,
            remitente: mensaje.remitente,
            reunion_id: mensaje.reunion,
        },
        type: "post",
        success: function(respuesta) {
            console.log("mensaje añadido a la BD");
        },
        error: function() {
            console.log("mensaje no se pudo añadir a la BD");
        }
    });
}
//GUARDAR MENSAJE EN BD

//IMPRIMIR EL MENSAJE EN EL CHAT
function imprimirMensaje(mensaje){
    var fila = 
        "<div class='row linea-mensaje'>" +
            "<b>" + mensaje.remitente + "</b>: " + mensaje.texto + 
        "</div>";

    $('#mensajes').append(fila);
}
//END IMPRIMIR EL MENSAJE EN EL CHAT


});
JS;
$this->registerJs($script);