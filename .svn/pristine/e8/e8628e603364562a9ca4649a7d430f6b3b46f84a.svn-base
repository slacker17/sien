$(document).ready(function(){
    // Cuando se le da submit al formulario de cambio de estatus en el modal
    $('#migrargrp').click(function(){
	oldg = getQueryVariable('grupo_id'); // Obtenemos id del curso por url (get)
	//alert(oldgrupo);
	//alert($('#newgrupo').val());
        var r = confirm("Es correcto el grupo de destino?");
        if (r == true) {
            $.post( 'updategrupoalumno' , { oldgrupo: oldg, newgrupo: $('#newgrupo').val() } )
                .done(function( data ) {
                    var obj = jQuery.parseJSON( data );
                    if(obj.result == "ok"){ // si se registro correctamente
                        alert("Operacion hecha con exito!");
			window.location.href = "grupo";
			//window.location.reload();
                    }else if(obj.result == "error"){
                        alert("Error en la operacion.");
                    }
                },'json');
        }
    });
    
});


// Funcion para obtener valores del get (url)
function getQueryVariable(variable) {
    // Estoy asumiendo que query es window.location.search.substring(1);
    var query = window.location.search.substring(1); //"product_id=32&cat_id=1&sessionid=123";
    var vars = query.split("&");
    //alert(vars);
    for (var i=0; i < vars.length; i++) {
        var pair = vars[i].split("="); 
        if (pair[0] == variable) {
            return pair[1];
        }
    }
    return false;
}
