function quitar(email){
    $.ajax({
        url: "index.php?r=site/quitarusuario",
        data: {
            email: email,
        },
        method: 'POST',
        success: function (result) {
            window.location = 'index.php?r=site/invitar';
        }
    });
}
$(document).ready(function (e) {
  
    var server = 'https://vast-hamlet-25601.herokuapp.com/';
    
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
    
    
//RECEPCIÓN DE MENSAJERÍA
    var connection = new RTCMultiConnection();
    connection.socketURL = server;
    connection.socketMessageEvent = 'video-conference-demo';

    connection.connectSocket(function(socket) {
        setEstado(true);
        if(tipo=="consultor"){        
            connection.socket.on(connection.socketCustomEvent, function(event) {
                if(event.accion == 'pantalla') {
                    console.log("consulta pantalla de: "+event.emisor);
                    actualizaEstadoConexion(event.emisor,true);
                    connection.socket.emit(connection.socketCustomEvent, {
                        accion: 'respuesta',
                        destinatario: event.emisor,
                        pantalla: 'index',
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
                        if(currentscreen != 'index'){
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
                actual: 'invitar',
            });
        }catch(ex){
            window.location = 'index.php?r=site/index';
        }
    }

//END MENÚ Y ENVÍO DE MENSAJES
    
    
    connection.session = {
        audio: false,
        video: false
    };

    connection.sdpConstraints.mandatory = {
        OfferToReceiveAudio: false,
        OfferToReceiveVideo: false
    };

    

//ABRIR O UNIRSE A REUNIÓN
    if(tipo == 'consultor'){
        connection.userid = consultor;
        try{
            connection.open(consultor, function () {});
        }catch(ex){
            alert('Error, por favor reintente.');
        }

    }
    if(tipo == 'participante'){
        connection.userid = participante;
        connection.join(consultor);
        connection.socket.emit(connection.socketCustomEvent, {
            accion: 'pantalla',
            emisor: participante,
            actual: 'invitar',
        });
    }
//END ABRIR O UNIRSE A REUNIÓN

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
    if(tipo == 'consultor')
    {
        connection.socket.on('disconnect',function(event){
            setEstado(false);
        });
    }
    
//END DESCONEXIÓN DEL CONSULTOR


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
if(tipo == 'consultor'){
    connection.socket.emit(connection.socketCustomEvent, {
        accion: 'index',
    });
}  
//END EMITIR LLAMADO A PARTICIPANTES PARA QUE CAMBIEN PANTALLA

//DESCONEXIÓN DE ALGÚN PARTICIPANTE
if(tipo == 'consultor'){
  connection.onUserStatusChanged = function(event) {
      console.log(event.status);
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

});
