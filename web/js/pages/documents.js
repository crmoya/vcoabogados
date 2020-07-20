function quitar(email) {
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

    if (localStorage.getItem("email") == null) {
        return;
    }
    
    listar();
    
    $(document.body).on('click','.delete',function(e){
        swal({
            title: '¿Seguro deseas eliminar este documento?',
            text: "No podrás revertir esta operación",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminarlo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    method: "GET",
                    url: "../ws_drive.php",
                    data: { request:'delete', id: $(this).attr('document_id') }
                }).done(function( respuesta ) {
                    swal(
                        'Eliminación Exitosa',
                        'El documento ha sido eliminado.',
                        'success'
                    );
                    listar();
                });
                
            }
        })
        return false;
    });
    
    $(document.body).on('click','.document',function(e){
        var tipo = $(this).attr('tipo');
        var document_id = $(this).attr('document_id');
        swal(
            'Por favor espera',
            'Se está intentando compartir el archivo...',
            'warning'
        );
        $.ajax({
            method: "GET",
            url: "../ws_drive.php",
            data: { request:'permissions', id: document_id }
        }).done(function( respuesta ) {
            if(respuesta == 'OK'){
                if(tipo == 'excel'){
                    window.location = 'index.php?r=site/sheets&document_id='+document_id;
                }
                else if(tipo == 'word'){
                    window.location = 'index.php?r=site/docs&document_id='+document_id;
                }
                else{
                    swal(
                        'Error',
                        'El archivo no existe.',
                        'error'
                    );
                }
            }
            else{
                swal(
                    'Error',
                    'No se pudo compartir el archivo.',
                    'error'
                );
            }
        });
    });
    
    function listar(){
        $.ajax({
            method: "GET",
            url: "../ws_drive.php",
            data: { request:'list' }
        }).done(function( respuesta ) {
            var resp = $.parseJSON(respuesta);
            $('#documentos').empty();
            for(var i=0;i<resp.length;i++){
                var file = resp[i];
                var tipo = (file.type=='application/vnd.google-apps.spreadsheet')?"excel":"word";
                var html =  "<div tipo='"+tipo+"' document_id='"+file.id+"' class='document col-md-1'>"+
                                "<div class='delete' document_id='"+file.id+"'><img src='img/trash.png'></div>"+
                                "<img src='img/"+tipo+".png'/>"+
                                "<div class='nombre'>"+file.name+"</div>"+
                            "</div>";
                $('#documentos').append(html);
            }
            $('.telon').hide();
        });
    }
    
    

    $('.createXls').click(function (e) {
        $('.formCreateXls').show();
        $('.formCreateDoc').hide();
        $('.formUploadXls').hide();
        $('.formUploadDoc').hide();
        $('.telon').show();
    });
    
    $('.cancelar').click(function(e){
        $('.formCreateXls').hide();
        $('.formCreateDoc').hide();
        $('.formUploadXls').hide();
        $('.formUploadDoc').hide();
        $('.telon').hide();
    });


    var filesDoc = '';
    var filesXls = '';
    $('#fileDoc').on('change', prepareDoc);
    $('#fileXls').on('change', prepareXls);
    
    function prepareDoc(event)
    {
        var tipo = event.target.files[0].type;
        if(tipo == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
            filesDoc = event.target.files;
        }
        else{
            swal(
                'Error',
                'Sólo se aceptan archivos con extensión .docx',
                'error'
            );
        }
    }
    function prepareXls(event)
    {
        var tipo = event.target.files[0].type;
        if(tipo == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
            filesXls = event.target.files;
        }
        else{
            swal(
                'Error',
                'Sólo se aceptan archivos con extensión .xlsx',
                'error'
            );
        }
    }
    
    $('#subirDoc').click(function(event){
        swal(
            'Por favor espera',
            'Se está intentando subir el archivo...',
            'warning'
        );
        event.stopPropagation(); // Stop stuff happening
        event.preventDefault(); // Totally stop stuff happening
        var data = new FormData();
        if(filesDoc != ''){
            $.each(filesDoc, function(key, value)
            {
                data.append(key, value);
            });

            $.ajax({
                url: '../ws_drive.php?request=uploadDoc',
                type: 'POST',
                data: data,
                cache: false,
                dataType: 'json',
                processData: false, // Don't process the files
                contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                success: function(data, textStatus, jqXHR)
                {
                    swal(
                        'Archivo DOC Subido Correctamente',
                        'Se ha subido el archivo exitosamente.',
                        'success'
                    );
                    filesDoc = '';
                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    swal(
                        'Archivo DOC Subido Correctamente',
                        'Se ha subido el archivo exitosamente.',
                        'success'
                    );
                    $('.telon').hide();
                    $('.formUploadDoc').hide();
                    listar();
                    filesDoc = '';
                }
            });
        }
        else{
            swal(
                'Archivo incorrecto',
                'Debes seleccionar un archivo con extensión .docx',
                'error'
            );
        }
    });
    
    $('#subirXls').click(function(event){
        swal(
            'Por favor espera',
            'Se está intentando subir el archivo...',
            'warning'
        );
        event.stopPropagation(); // Stop stuff happening
        event.preventDefault(); // Totally stop stuff happening
        
        if(filesXls != ''){
            var data = new FormData();
            $.each(filesXls, function(key, value)
            {
                data.append(key, value);
            });

            $.ajax({
                url: '../ws_drive.php?request=uploadXls',
                type: 'POST',
                data: data,
                cache: false,
                dataType: 'json',
                processData: false, // Don't process the files
                contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                success: function(data, textStatus, jqXHR)
                {
                    swal(
                        'Archivo XLS Subido Correctamente',
                        'Se ha subido el archivo exitosamente.',
                        'success'
                    );
                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    swal(
                        'Archivo XLS Subido Correctamente',
                        'Se ha subido el archivo exitosamente.',
                        'success'
                    );
                    $('.telon').hide();
                    $('.formUploadXls').hide();
                    listar();
                }
            });
        }
        else{
            swal(
                'Archivo incorrecto',
                'Debes seleccionar un archivo con extensión .xlsx',
                'error'
            );
        }
        
    });
    
    
    $('#crearXls').click(function(e){
        swal(
            'Por favor espera',
            'Se está intentando crear el archivo...',
            'warning'
        );
        $.ajax({
            method: "GET",
            url: "../ws_drive.php",
            data: { request:'createXls', name: $('#nameXls').val() }
        }).done(function( respuesta ) {
            if(respuesta == 'OK'){                
                swal(
                    'Archivo XLS Creado',
                    'Se ha creado el archivo '+$('#nameXls').val()+' exitosamente.',
                    'success'
                );
                $('#nameXls').val('');
                $.ajax({
                    method: "GET",
                    url: "../ws_drive.php",
                    data: { request:'list' }
                }).done(function( respuesta ) {
                    var resp = $.parseJSON(respuesta);
                    $('#documentos').empty();
                    for(var i=0;i<resp.length;i++){
                        var file = resp[i];
                        var tipo = (file.type=='application/vnd.google-apps.spreadsheet')?"excel":"word";
                        var html = "<div tipo='"+tipo+"' document_id='"+file.id+"' class='document col-md-1'>"+
                                        "<div class='delete' document_id='"+file.id+"'><img src='img/trash.png'></div>"+
                                        "<img src='img/"+tipo+".png'/>"+
                                        "<div class='nombre'>"+file.name+"</div>"+
                                    "</div>";
                        $('#documentos').append(html);
                    }
                    $('.telon').hide();
                    $('.formCreateXls').hide();
                });
            }
            else{
                swal(
                    'Error al crear XLS',
                    'No se pudo crear el archivo, reintenta.',
                    'error'
                );
            }
        });
        
    });
    
    $('.createDoc').click(function (e) {
        $('.formCreateDoc').show();
        $('.formCreateXls').hide();
        $('.formUploadXls').hide();
        $('.formUploadDoc').hide();
        $('.telon').show();
    });
    
    

    $('#crearDoc').click(function(e){
        swal(
            'Por favor espera',
            'Se está intentando crear el archivo...',
            'warning'
        );
        $.ajax({
            method: "GET",
            url: "../ws_drive.php",
            data: { request:'createDoc', name: $('#nameDoc').val() }
        }).done(function( respuesta ) {
            if(respuesta == 'OK'){
                 swal(
                    'Archivo DOC Creado',
                    'Se ha creado el archivo '+$('#nameDoc').val()+' exitosamente.',
                    'success'
                );
                $('#nameDoc').val('');
                $.ajax({
                    method: "GET",
                    url: "../ws_drive.php",
                    data: { request:'list' }
                }).done(function( respuesta ) {
                    var resp = $.parseJSON(respuesta);
                    $('#documentos').empty();
                    for(var i=0;i<resp.length;i++){
                        var file = resp[i];
                        var tipo = (file.type=='application/vnd.google-apps.spreadsheet')?"excel":"word";
                        var html = "<div tipo='"+tipo+"' document_id='"+file.id+"' class='document col-md-1'>"+
                                        "<div class='delete' document_id='"+file.id+"'><img src='img/trash.png'></div>"+
                                        "<img src='img/"+tipo+".png'/>"+
                                        "<div class='nombre'>"+file.name+"</div>"+
                                    "</div>";
                        $('#documentos').append(html);
                    }
                    $('.telon').hide();
                    $('.formCreateDoc').hide();
                });
            }
            else{
                swal(
                    'Error al crear DOC',
                    'No se pudo crear el archivo, reintenta.',
                    'error'
                );
            }
        });
        
    });
    
    $('.uploadDoc').click(function (e) {
        $('.formUploadDoc').show();
        $('.formCreateXls').hide();
        $('.formCreateDoc').hide();
        $('.formUploadXls').hide();
        $('.telon').show();
    });
    
    $('.uploadXls').click(function (e) {
        $('.formUploadDoc').hide();
        $('.formCreateXls').hide();
        $('.formCreateDoc').hide();
        $('.formUploadXls').show();
        $('.telon').show();
    });
    
    

    var ancho = $('.desktop').width() - 200;
    var alto = $('.desktop').height();
    $('.animacion').css('left', ancho / 2 + 'px');
    $('.animacion').css('top', alto / 2 + 'px');
    $('.animacion').show();
    var tipo = localStorage.getItem('tipo');
    var consultor = localStorage.getItem('consultor');
    var participante = localStorage.getItem('participante');




//RECEPCIÓN DE MENSAJERÍA
    var connection = new RTCMultiConnection();
    connection.socketURL = server;
    connection.socketMessageEvent = 'video-conference-demo';

    connection.connectSocket(function (socket) {
        setEstado(true);
        if (tipo == "consultor") {
            connection.socket.on(connection.socketCustomEvent, function (event) {
                if (event.accion == 'pantalla') {
                    console.log("consulta pantalla de: " + event.emisor);
                    if(event.actual == 'index'){
                      actualizaEstadoConexion(event.emisor,true);
                    }
                    connection.socket.emit(connection.socketCustomEvent, {
                        accion: 'respuesta',
                        destinatario: event.emisor,
                        pantalla: 'index',
                    });
                }
            });
        }
        if (tipo == 'participante') {
            connection.socket.on(connection.socketCustomEvent, function (event) {
                if (event.accion == 'vco') {
                    window.location = 'index.php?r=site/vco';
                } else if (event.accion == 'invitar') {
                    window.location = 'index.php?r=site/index';
                } else if (event.accion == 'escritorio') {
                    window.location = 'index.php?r=site/escritorio';
                } else if (event.accion == 'consultores') {
                    window.location = 'index.php?r=site/index';
                } else if (event.accion == 'broadcast') {
                    window.location = 'index.php?r=site/broadcast';
                } else if (event.accion == 'docs') {
                    window.location = 'index.php?r=site/docs&document_id='+event.document_id;
                } else if (event.accion == 'sheets') {
                    window.location = 'index.php?r=site/sheets&document_id='+event.document_id;
                } else if (event.accion == 'respuesta') {
                    if (event.destinatario == participante) {
                        var currentscreen = event.pantalla;
                        console.log("respuesta pantalla: " + currentscreen);
                        if (currentscreen == 'invitar' || currentscreen == 'consultores') {
                            currentscreen = 'index';
                        }
                        if (currentscreen != 'index') {
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

    function reconectar() {
        try {
            console.log('reconectando...');
            connection.socket.emit(connection.socketCustomEvent, {
                accion: 'pantalla',
                emisor: participante,
                actual: 'documents',
            });
        } catch (ex) {
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
    if (tipo == 'consultor') {
        connection.userid = consultor;
        try {
            $.ajax({
                url: 'index.php?r=site/limpiarconectados',
                type: 'GET'
            }).done(function(){
              connection.open(consultor, function () {});
            });
        } catch (ex) {
            alert('Error, por favor reintente.');
        }

    }
    if (tipo == 'participante') {
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

//ESTADO DE CONEXIÓN
    function setEstado(conectado) {
        if (conectado) {
            $('.estado').removeClass('alert-danger');
            $('.estado').addClass('alert-success');
            $('.estado').parent().addClass('col-md-4');
            $('.estado').parent().removeClass('col-md-5');
            $('.estado').html("En Línea");
            $('.reconectar').hide();

            if (tipo == 'consultor') {
                $('.invitar').show();
                $('.consultores').show();
                $('.vco').show();
                $('.broadcast').show();
                $('.escritorio').show();
                $('.terminar').show();
                $('.doc').show();
                $('.sheets').show();
            }
            if (tipo == 'participante') {
                $('.terminar').show();
            }
        } else {
            $('.estado').removeClass('alert-success');
            $('.estado').addClass('alert-danger');
            $('.estado').parent().addClass('col-md-5');
            $('.estado').parent().removeClass('col-md-4');
            $('.estado').html("Desconectado");
            $('.reconectar').html('Reconectar <i class="fa fa-plug"></i>');
            $('.reconectar').show();

            if (tipo == 'consultor') {
                $('.invitar').hide();
                $('.consultores').hide();
                $('.vco').hide();
                $('.broadcast').hide();
                $('.escritorio').hide();
                $('.terminar').hide();
                $('.doc').hide();
                $('.sheets').hide();
            }
            if (tipo == 'participante') {
                $('.transmitir').hide();
                $('.terminar').hide();
            }

        }
    }
//


//DESCONEXIÓN DEL CONSULTOR
    if (tipo == 'consultor')
    {
        connection.socket.on('disconnect', function (event) {
            setEstado(false);
        });
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

//PARTICIPANTE CONSULTA SI ESTÁ EN LA PANTALLA CORRECTA CUANDO CARGA
    if (tipo == 'participante') {
        esperaYReconecta();
        function esperaYReconecta() {
            setTimeout(function () {
                reconectar();
            }, 3000);
        }
    }
//END PARTICIPANTE CONSULTA SI ESTÁ EN LA PANTALLA CORRECTA CUANDO CARGA
//
//EMITIR LLAMADO A PARTICIPANTES PARA QUE CAMBIEN PANTALLA
    if (tipo == 'consultor') {
        console.log('cambiar pantalla');
        connection.socket.emit(connection.socketCustomEvent, {
            accion: 'index',
        });
    }
//END EMITIR LLAMADO A PARTICIPANTES PARA QUE CAMBIEN PANTALLA


});
