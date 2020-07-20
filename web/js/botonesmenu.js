
var slowLoad = window.setTimeout( function() {
    swal(
        'Conexión Lenta',
        'Lamentablemente tu conexión a Internet es lenta o inestable. Es posible que algunas funcionalidades no estén disponibles en su totalidad.',
        'error'
    );
    /*
    //localStorage.clear();
    setTimeout( function() {
        //window.location = 'index.php';
    }, 3000 );*/
}, 10000 );

window.addEventListener( 'load', function() {
    window.clearTimeout( slowLoad );
}, false );


function cerrarSesion(){
    $('.menuconsultor').hide();
    $('.menuparticipante').hide();
    if(localStorage.getItem('tipo')=='consultor'){
        localStorage.clear();
        $.ajax({
            url: 'index.php?r=site/limpiarinvitados',
            type: 'POST'
        }).done(function(e){
            $.ajax({
                url: 'index.php?r=site/cerrarsesion',
                type: 'POST'
            }).done(function(e){
                try{
                    var auth2 = gapi.auth2.getAuthInstance();
                    auth2.signOut().then(function () {
                        window.location = 'index.php';
                    });
                }
                catch(ex){
                    console.log(ex);
                }
            });
        });  
    }
    if(localStorage.getItem('tipo')=='participante'){
        localStorage.clear();
        $.ajax({
            url: 'index.php?r=site/desconectar',
            type: 'POST'
        }).done(function(e){
            $.ajax({
                url: 'index.php?r=site/cerrarsesion',
                type: 'POST'
            }).done(function(e){
                try{
                    var auth2 = gapi.auth2.getAuthInstance();
                    auth2.signOut().then(function () {
                        window.location = 'index.php';
                    });
                }
                catch(ex){
                    console.log(ex);
                }
            });
        });  
    }
    
    
}

function esconder(){
    if ($('.menu').hasClass("escondido")) {
        $('.menu').css("transform", "translate(265px, 0px)");
        $('.flecha').removeClass('fa fa-chevron-right');
        $('.flecha').addClass('fa fa-chevron-left flecha');
        $('.menu').removeClass('escondido');
    } else {
        $('.menu').css("transform", "");
        $('.flecha').removeClass('fa fa-chevron-left');
        $('.flecha').addClass('fa fa-chevron-right flecha');
        $('.menu').addClass('escondido');
    }
}
var alto = $('.menuconectados').height()-18;
function esconderConectados(){
    if ($('.menuconectados').hasClass("escondido")) {
        $('.menuconectados').css("transform", "translate(0px, -"+(alto-2)+"px)");
        $('.flechaabajo').removeClass('fa fa-chevron-up');
        $('.flechaabajo').addClass('fa fa-chevron-down flechaabajo');
        $('.menuconectados').removeClass('escondido');
    } else {
        $('.menuconectados').css("transform", "");
        $('.flechaabajo').removeClass('fa fa-chevron-down');
        $('.flechaabajo').addClass('fa fa-chevron-up flechaabajo');
        $('.menuconectados').addClass('escondido');
    }
}
if(localStorage.getItem('tipo')=='consultor'){
  $('.menuconectados').show();
}

function actualizaEstadoConexion(userid,status){
  console.log(userid+" "+status);
  $('.conexion').each(function(e){
    var participante = $(this).attr('participante');
    if(participante == userid){
      $(this).removeClass('alert-success alert-danger');
      if(status){
        $(this).addClass('alert-success');
      }
      else{
        $(this).addClass('alert-danger');
      }
    }
  });
  
}