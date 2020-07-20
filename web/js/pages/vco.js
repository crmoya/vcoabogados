var transmitiendo = true;
var miStream = null;
function transmitir(){
    if(transmitiendo){
        miStream.mute();
        $('.transmitir').html("Transmitir <i class='fa fa-video-camera'></i>");
        $('.transmitir').removeClass('btn-danger');
        $('.transmitir').addClass('btn-success');
    }
    else{
        miStream.unmute();
        $('.transmitir').html("Dejar de Transmitir <i class='fa fa-video-camera'></i>");
        $('.transmitir').removeClass('btn-success');
        $('.transmitir').addClass('btn-danger');
    }
    transmitiendo = !transmitiendo;
}

$(document).ready(function (e) {
  
    var conectados = 0;
  
    var server = 'https://vast-hamlet-25601.herokuapp.com/';
    
    if(localStorage.getItem("email") == null){
        return;
    }
    
//VARIABLES QUE SE USARÁN

    
    var recibiendo = false;
    var width = 500;
    var height = 500;
    var tipo = localStorage.getItem('tipo');
    var consultor = localStorage.getItem('consultor');
    var participante = localStorage.getItem('participante');
    
    if(tipo == null){
       $('.iniciosesion').show();
       $('.bienvenido').remove();
    }
    
    window.enableAdapter = true; // enable adapter.js
    
//END VARIABLES QUE SE USARÁN
    var connection = new RTCMultiConnection();
    //connection.socketURL = 'https://rtcmulticonnection.herokuapp.com:443/';
    
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
    if(tipo == 'consultor'){
        connection.userid = consultor;
        try{
            $.ajax({
                url: 'index.php?r=site/limpiarconectados',
                type: 'GET'
            }).done(function(){
              setTimeout(function () {
                connection.open(consultor, function () {});
              },3000);
              
            });
        }catch(ex){
            alert('Error, por favor reintente.');
        }

    }
    if(tipo == 'participante'){
      
        connection.userid = participante;
        var email = localStorage.getItem('email');
        $.ajax({
            url: 'index.php?r=site/conectarme',
            data: { email: email, nombre: participante },
            type: 'GET'
        }).done(function(resp){
          console.log(resp);
          connection.join(consultor);
        });
    }
//END ABRIR O UNIRSE A REUNIÓN

    
//RECEPCIÓN DE MENSAJERÍA
    
    connection.connectSocket(function(socket) {
        setEstado(true);
        if(tipo=="consultor"){        
            connection.socket.on(connection.socketCustomEvent, function(event) {
                if(event.accion == 'pantalla') {
                    console.log("consulta pantalla de: "+event.emisor);
                    if(event.actual == 'vco'){
                      actualizaEstadoConexion(event.emisor,true);
                    }
                    connection.socket.emit(connection.socketCustomEvent, {
                        accion: 'respuesta',
                        destinatario: event.emisor,
                        pantalla: 'vco',
                    });
                }
            });
        }
        if(tipo == 'participante'){
            connection.socket.on(connection.socketCustomEvent, function(event) {
                if(event.accion == 'vco') {
                    window.location = 'index.php?r=site/vco';
                }
                else if(event.accion == 'invitar') {
                    window.location = 'index.php?r=site/index';
                }
                else if(event.accion == 'escritorio') {
                    window.location = 'index.php?r=site/escritorio';
                }
                else if(event.accion == 'consultores') {
                    window.location = 'index.php?r=site/index';
                }
                else if(event.accion == 'broadcast') {
                    window.location = 'index.php?r=site/broadcast';
                }
                else if(event.accion == 'docs') {
                    window.location = 'index.php?r=site/docs&document_id='+event.document_id;
                }
                else if(event.accion == 'sheets') {
                    window.location = 'index.php?r=site/sheets&document_id='+event.document_id;
                }
                else if(event.accion == 'respuesta') {
                    if(event.destinatario == participante){
                        var currentscreen = event.pantalla;
                        console.log("respuesta pantalla: "+currentscreen);
                        if(currentscreen == 'invitar' || currentscreen == 'consultores'){
                            currentscreen = 'index';
                        }
                        if(currentscreen != 'vco'){
                            if(currentscreen == 'sheets' || currentscreen == 'docs'){
                                window.location = 'index.php?r=site/'+currentscreen+'&document_id='+event.document_id;
                            }
                            else{
                                window.location = 'index.php?r=site/'+currentscreen;
                            }
                        }
                    }
                }
            });
        }
    });
//END RECEPCIÓN DE MENSAJERÍA

//MENÚ Y ENVÍO DE MENSAJES


    
    function reconectar(){
        try{
            connection.socket.emit(connection.socketCustomEvent, {
                accion: 'pantalla',
                emisor: participante,
                actual: 'vco',
            });
        }catch(ex){
            window.location = 'index.php?r=site/index';
        }
    }
//END MENÚ Y ENVÍO DE MENSAJES
    
    

//ESTADO DE CONEXIÓN
function setEstado(conectado){
    if(conectado){
        $('.estado').removeClass('alert-danger');
        $('.estado').addClass('alert-success');
        $('.estado').parent().addClass('col-md-4');
        $('.estado').parent().removeClass('col-md-5');
        $('.estado').html("En Línea");   
        $('.reconectar').hide();
        
        if(tipo == 'consultor'){
            $('.invitar').show();
            $('.doc').show();
            $('.sheets').show();
            $('.consultores').show();
            $('.vco').show();
            $('.broadcast').show();
            $('.escritorio').show();
            $('.terminar').show();
        }
        if(tipo == 'participante'){
            $('.terminar').show();
        }
    }
    else{
        $('.estado').removeClass('alert-success');
        $('.estado').addClass('alert-danger');
        $('.estado').parent().addClass('col-md-5');
        $('.estado').parent().removeClass('col-md-4');
        $('.estado').html("Desconectado");
        $('.reconectar').html('Reconectar <i class="fa fa-plug"></i>');
        $('.reconectar').show();  
        
        if(tipo == 'consultor'){
            $('.invitar').hide();
            $('.consultores').hide();
            $('.vco').hide();
            $('.broadcast').hide();
            $('.escritorio').hide();
            $('.terminar').hide();
            $('.doc').hide();
            $('.sheets').hide();
        }
        if(tipo == 'participante'){
            $('.transmitir').hide();
            $('.terminar').hide();
        }
        
    }
}
//


//DESCONEXIÓN DEL CONSULTOR
    if(tipo == 'consultor')
    {
        connection.socket.on('disconnect',function(event){
            setEstado(false);
        });
        
    }
  
    if(tipo == 'participante'){
        connection.onUserStatusChanged = function(event) {
            var isOffline = event.status === 'offline';
            if(event.userid == consultor && isOffline){
                window.location = 'index.php';
            }
        };
    }
    
//END DESCONEXIÓN DEL CONSULTOR


//DESCONEXIÓN DE ALGÚN PARTICIPANTE
if(tipo == 'consultor'){
  connection.onUserStatusChanged = function(event) {
      var isOffline = event.status === 'offline';
      if(event.userid != consultor && isOffline){
        console.log("desconectar a "+event.userid);
        actualizaEstadoConexion(event.userid,false);
        $.ajax({
            url: 'index.php?r=site/desconectarinvitado',
            data: { nombre: event.userid },
            type: 'GET'
        }).done(function(resp){
        });
      }
  };
}
//END DESCONEXIÓN DE ALGÚN PARTICIPANTE

//STREAMING
    
    connection.onstream = function (event) {
        if(event.userid != consultor){
          conectados++;
        }
        recibiendo = true;
        if(consultor != 'null'){
            if(tipo == 'participante'){
                $('.transmitir').show();
            }
            $('.estado').addClass('alert-success');
            $('.estado').removeClass('alert-danger');
            $('.estado').parent().removeClass('col-md-5');
            $('.estado').parent().addClass('col-md-4');
            $('.estado').html("En línea");
            $('.reconectar').html('Refrescar <i class="fa fa-refresh"></i>');
        }
        else{
            cerrarSesion();
        }

        event.mediaElement.removeAttribute('src');
        event.mediaElement.removeAttribute('srcObject');

        var who = "";
        if(tipo == 'consultor'){
            var video = document.createElement('video');
            video.controls = false;
            if (event.type === 'local') {
                video.muted = true;
            }
            video.srcObject = event.stream;
            if(event.userid == consultor){
                connection.videosContainer = document.getElementById('yo');
                who = "YO";
                
                //EMITIR LLAMADO A PARTICIPANTES PARA QUE CAMBIEN PANTALLA
                connection.socket.emit(connection.socketCustomEvent, {
                    accion: 'vco',
                });
                //END EMITIR LLAMADO A PARTICIPANTES PARA QUE CAMBIEN PANTALLA
                
            }
            else{
                connection.videosContainer = document.getElementById('participantes');
                who = event.userid;
            }
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
            if(event.userid == consultor){
                connection.videosContainer = document.getElementById('consultor');
                who = consultor;
            }
            else if(event.userid == participante){
                connection.videosContainer = document.getElementById('yo');
                miStream = event.stream;
                who = "YO";
            }
            else{
                connection.videosContainer = document.getElementById('participantes');
                who = event.userid;
            }
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

//PARTICIPANTE CONSULTA SI ESTÁ EN LA PANTALLA CORRECTA CUANDO CARGA
    if(tipo == 'participante'){
        esperaYReconecta();
        function esperaYReconecta(){
            setTimeout(function () {
                reconectar();
            }, 3000);
        }
    }    
//END PARTICIPANTE CONSULTA SI ESTÁ EN LA PANTALLA CORRECTA CUANDO CARGA

//SI DESPUÉS DE 7 SEGUNDOS NO RECIBE ENTONCES REFRESCAR
    if(tipo == 'participante'){
        setTimeout(function () {
            if(!recibiendo){
                window.location = 'index.php?r=site/vco';
            }
        }, 7000);
    }   
//END SI DESPUÉS DE 7 SEGUNDOS NO RECIBE ENTONCES REFRESCAR
   

//A LOS 30 SEGUNDOS REVISAR QUE ESTÉN TODOS LOS PARTICIPANTES CONECTADOS
setTimeout(function () {
    revisar();
}, 30000);

function revisar(){
  $.ajax({
      url: 'index.php?r=site/cuentaparticipantes',
      type: 'POST'
  }).done(function(resp){
      var invitados = parseInt(resp);
      if(invitados > conectados){ 
        if(tipo == 'participante'){
          swal(
              'Aún hay participantes que no se han unido a la conferencia',
              'Por favor, presione "REFRESCAR" para integrarlos a la conferencia.',
              'warning'
          );
          /*swal(
              'Aún hay participantes que no se han unido a la conferencia',
              'Se refrescará la videoconferencia',
              'warning'
          );
          setTimeout(function(){
            location.reload();
          },3000);*/
        }    
        if(tipo == 'consultor'){
          swal(
              'Aún hay participantes que no se han unido a la conferencia',
              'Por favor verifique la correcta conexión de todos los participantes.',
              'warning'
          );
        }
      }
  });
}
//A LOS 30 SEGUNDOS REVISAR QUE ESTÉN TODOS LOS PARTICIPANTES CONECTADOS




});