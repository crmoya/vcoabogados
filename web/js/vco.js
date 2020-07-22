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
    var tipo = "abogado";
    var abogado = "Abogado";
    var reunion = abogado;
    var participante = "Cliente";
    
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
            connection.openOrJoin(reunion, function () {});
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
        if(abogado != 'null'){
            if(tipo == 'participante'){
                $('.transmitir').show();
            }
        }
        else{
           //cerrarSesion();
        }

        event.mediaElement.removeAttribute('src');
        event.mediaElement.removeAttribute('srcObject');

        var who = "";
        if(tipo == 'abogado'){
            var video = document.createElement('video');
            video.controls = false;
            if (event.type === 'local') {
                video.muted = true;
            }
            video.srcObject = event.stream;
            connection.videosContainer = document.getElementById('participantes');
            who = event.userid;
            
            var mediaElement = getHTMLMediaElement(video, {
                title: who,
                buttons: ['full-screen'],
                width: width,
                height: height,
                showOnMouseEnter: false
            });

            connection.videosContainer.appendChild(mediaElement);

            setTimeout(function () {
                mediaElement.media.play();
            }, 5000);

            mediaElement.id = event.streamid;
        }
        if(tipo == 'participante'){            
            var video = document.createElement('video');
            video.controls = false;
            if (event.type === 'local') {
                video.muted = true;
            }
            video.srcObject = event.stream;
            if(event.userid == abogado){
                who = abogado;
            }
            else{
                who = event.userid;
            }
            connection.videosContainer = document.getElementById('participantes');
            var mediaElement = getHTMLMediaElement(video, {
                title: who,
                buttons: ['full-screen'],
                width: width,
                height: height,
                showOnMouseEnter: false
            });

            connection.videosContainer.appendChild(mediaElement);

            setTimeout(function () {
                mediaElement.media.play();
            }, 5000);

            
            mediaElement.id = event.streamid;

                    
        }   

    };

    connection.onstreamended = function (event) {
        var mediaElement = document.getElementById(event.streamid);
        if (mediaElement) {
            mediaElement.parentNode.removeChild(mediaElement);
        }
    };
//END STREAMING



});