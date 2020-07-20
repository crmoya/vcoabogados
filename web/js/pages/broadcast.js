$(document).ready(function (e) {
    var conectados = 0;
    var server = 'https://vast-hamlet-25601.herokuapp.com/';
    if(localStorage.getItem("email") == null){
        return;
    }
    
    
//VARIABLES QUE SE USARÁN

    var recibiendo = false;
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
    connection.socketURL = server;
    connection.socketMessageEvent = 'video-conference-demo';
    
    connection.session = {
        audio: true,
        video: true,
        oneway: true
    };

    connection.sdpConstraints.mandatory = {
        OfferToReceiveAudio: false,
        OfferToReceiveVideo: false
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
      connection.sdpConstraints.mandatory = {
          OfferToReceiveAudio: true,
          OfferToReceiveVideo: true
      };
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
                    if(event.actual == 'broadcast'){
                      actualizaEstadoConexion(event.emisor,true);
                    }
                    connection.socket.emit(connection.socketCustomEvent, {
                        accion: 'respuesta',
                        destinatario: event.emisor,
                        pantalla: 'broadcast',
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
                        if(currentscreen != 'broadcast'){
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
                actual: 'broadcast',
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
            $('.consultores').show();
            $('.vco').show();
            $('.broadcast').show();
            $('.escritorio').show();
            $('.terminar').show();
            $('.doc').show();
            $('.sheets').show();
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
                //alert('Consultor se desconectó, reintentando la conexión...');
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
    connection.videosContainer = document.getElementById('consultor');

    connection.onstream = function (event) {
        event.mediaElement.removeAttribute('src');
        event.mediaElement.removeAttribute('srcObject');
        
        
        if(tipo == 'consultor' && event.userid == consultor){
            //EMITIR LLAMADO A PARTICIPANTES PARA QUE CAMBIEN PANTALLA
            connection.socket.emit(connection.socketCustomEvent, {
                accion: 'broadcast',
            });
            //EMITIR LLAMADO A PARTICIPANTES PARA QUE CAMBIEN PANTALLA
        }
        recibiendo = true;
        
        if(consultor != 'null'){
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
        var video = document.createElement('video');
        video.controls = true;
        if(event.type === 'local') {
            video.muted = true;
        }
        video.srcObject = event.stream;

        var width = parseInt(connection.videosContainer.clientWidth / 2) - 20;
        var mediaElement = getHTMLMediaElement(video, {
            title: event.userid,
            buttons: ['full-screen'],
            width: width,
            showOnMouseEnter: false
        });

        connection.videosContainer.appendChild(mediaElement);

        setTimeout(function() {
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

//ERROR AL HACER STREAMING
    connection.onMediaError = function(event){
        //window.location = 'index.php?content=welcome';
        //alert("ERROR en la conexión, por favor presione el botón 'RECONECTAR'");
        if(tipo == 'consultor'){
            connection.open(consultor);
        }
        else{
            connection.join(consultor);
        }
    }
//


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
                window.location = 'index.php?r=site/broadcast';
            }
        }, 7000);
    }   
//END SI DESPUÉS DE 7 SEGUNDOS NO RECIBE ENTONCES REFRESCAR

/*
//EMITIR LLAMADO A PARTICIPANTES PARA QUE CAMBIEN PANTALLA
if(tipo == 'consultor'){
    setTimeout(function () {
        connection.socket.emit(connection.socketCustomEvent, {
            accion: 'broadcast',
        });
    }, 10000);
}  
*/
//END EMITIR LLAMADO A PARTICIPANTES PARA QUE CAMBIEN PANTALLA






});