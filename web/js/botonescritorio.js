if(localStorage.getItem('tipo') == 'participante'){
    $('.menuf').show();
}

function toggleConsultor(){
    if ($('.menuf').hasClass("escondido")) {
        $('.media-container').css("transform", "translate(0px, -215px)");
        $('.flex').removeClass('fa-arrow-up');
        $('.flex').addClass('fa-arrow-down');
        $('.txtflex').html('Mostrar Presentador');
        $('.menuf').removeClass('escondido');
    } else {
        $('.media-container').css("transform", "");
        $('.flex').removeClass('fa-arrow-down');
        $('.flex').addClass('fa-arrow-up');
        $('.txtflex').html('Ocultar Presentador');
        $('.menuf').addClass('escondido');
    }
}
