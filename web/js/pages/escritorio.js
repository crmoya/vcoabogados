$(document).ready(function (e) {
    
    var server = 'https://vast-hamlet-25601.herokuapp.com/';
    var conectados = 0;
    if(localStorage.getItem("email") == null){
        return;
    }
    
    var tipo = localStorage.getItem('tipo');
    var consultor = localStorage.getItem('consultor');
    var participante = localStorage.getItem('participante');
    if(tipo == null){
       $('.iniciosesion').show();
       $('.bienvenido').remove();
    }
    
    if(tipo == 'participante'){
        $('.share').remove();
    }

    var screens = 0;
    var connection = new RTCMultiConnection();

    // Using getScreenId.js to capture screen from any domain
    // You do NOT need to deploy Chrome Extension YOUR-Self!!
    connection.getScreenConstraints = function(callback) {
        getScreenConstraints(function(error, screen_constraints) {
            if (!error) {
                screen_constraints = connection.modifyScreenConstraints(screen_constraints);
                callback(error, screen_constraints);
                return;
            }
            else{
                if(error == 'not-installed'){
                  swal({
                      title: 'Plugin no instalado, por favor instálalo.',
                      type: 'warning',
                      html:
                        'Puedes descargarlo desde ' +
                        '<a href="https://chrome.google.com/webstore/detail/screen-capturing/ajhifddimkapgcifgcodmmfdlknahffk" target="_blank">Acá</a>',
                      showCloseButton: false,
                      showCancelButton: false,
                      focusConfirm: false,
                      confirmButtonText: '<i class="fa fa-thumbs-up"></i> OK',
                      confirmButtonAriaLabel: 'Thumbs up, great!',
                      cancelButtonText:
                      '<i class="fa fa-thumbs-down"></i>',
                      cancelButtonAriaLabel: 'Thumbs down',
                  });
                }
                
            }
            
        });
    };

    connection.socketURL = server;
    connection.socketMessageEvent = 'audio-video-screen-demo';

    
    connection.session = {
        audio: true, 
        video: true,
    };
    
    connection.sdpConstraints.mandatory = {
        OfferToReceiveAudio: true,
        OfferToReceiveVideo: true
    };

    connection.videosContainer = document.getElementById('videos-container');
    connection.audiosContainer = document.getElementById('audios-container');
    connection.onstream = function(event) {
        if(event.userid != consultor && tipo == 'consultor'){
          conectados++;
          console.log("se conectó: "+event.userid);
        }
        if(tipo == 'consultor'){
            screens++;
            if(screens == 2){
                //EMITIR LLAMADO A PARTICIPANTES PARA QUE CAMBIEN PANTALLA
                connection.socket.emit(connection.socketCustomEvent, {
                    accion: 'escritorio',
                });
                //EMITIR LLAMADO A PARTICIPANTES PARA QUE CAMBIEN PANTALLA
            }
        }
        var width = $(window).width()/2-10;
        var width = 200;
        var mediaElement = getMediaElement(event.mediaElement, {
            title: event.userid,
            buttons: ['full-screen'],
            width: width,
            showOnMouseEnter: false
        });
        
        
        if(tipo == 'participante' && event.userid == consultor){
          console.log(event.stream.isScreen);
          if(event.stream.isScreen) {
            connection.videosContainer.appendChild(mediaElement);
          }
          else {
            connection.audiosContainer.appendChild(mediaElement);
          }
          
          setTimeout(function() {
            mediaElement.media.play();
          }, 5000);
        }
        
        
        mediaElement.id = event.streamid;
    };

    connection.onstreamended = function(event) {
        var mediaElement = document.getElementById(event.streamid);
        if(mediaElement) {
            mediaElement.parentNode.removeChild(mediaElement);
        }
    };

//RECEPCIÓN DE MENSAJERÍA
    
    connection.connectSocket(function(socket) {
        setEstado(true);
        if(tipo=="consultor"){        
            connection.socket.on(connection.socketCustomEvent, function(event) {
                if(event.accion == 'pantalla') {
                    console.log("consulta pantalla de: "+event.emisor);
                    if(event.actual == 'escritorio'){
                      actualizaEstadoConexion(event.emisor,true);
                    }
                    connection.socket.emit(connection.socketCustomEvent, {
                        accion: 'respuesta',
                        destinatario: event.emisor,
                        pantalla: 'escritorio',
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
                        if(currentscreen != 'escritorio'){
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
                actual: 'escritorio',
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
            $('.transmitir').hide();
            $('.terminar').hide();
        }
        
    }
}
//


//DESCONEXIÓN DEL CONSULTOR
    connection.onUserStatusChanged = function(event, dontWriteLogs) {
        if (!!connection.enableLogs && !dontWriteLogs) {
            if(event.userid == consultor && event.status == 'offline' && tipo == 'participante'){
                window.location = 'index.php';
            }
        }
    };
    
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
//
//EMITIR LLAMADO A PARTICIPANTES PARA QUE CAMBIEN PANTALLA
/*if(tipo == 'consultor'){
    setTimeout(function () {
        connection.socket.emit(connection.socketCustomEvent, {
            accion: 'escritorio',
        });
    }, 10000);
} */ 
//END EMITIR LLAMADO A PARTICIPANTES PARA QUE CAMBIEN PANTALLA

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
                connection.addStream({
                  screen: true,
                  oneway: true
                });
              },3000);
            });
        }catch(ex){
            alert('Error, por favor reintente.');
        }

    }
    
    if(tipo == 'participante'){
        //connection.userid = participante;
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


});
