//REVISAR SI ESTÁ INVITADO O ES CONSULTOR
var email = localStorage.getItem('email');
if(email == null){
    email = "";
}
$.ajax({
    url: 'index.php?r=site/estainvitadooconsultor',
    data: {
        email: email,
    },
    success: function (data) {
        if(data == 0){
            //cerrarSesion();
        }
    },
    type: 'GET'
});
//END REVISAR SI ESTÁ INVITADO O ES CONSULTOR
