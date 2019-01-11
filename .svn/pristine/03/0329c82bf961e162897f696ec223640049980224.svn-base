$(document).ready(function(){
    var grupo = 0;

    $('#curp').blur(function(){
	// Si la curp es de 18 caracteres hacemos el request
	if($(this).val().length == 18){
	
	    $.post( 'getdataalumno', {curp: $(this).val()})
		.done(function( data ) {
		    
                    var obj = jQuery.parseJSON( data );
		    
                    if (obj.result == "ok"){
			// Mostramos los datos
			$('#dataalumno').html("Nombre: "+obj.nombre+" "+obj.app+" "+obj.apm);
			resultado.classList.add("ok");
			resultado.innerText = "Curp encontrada";
		    }else if(obj.result == "error"){
			resultado.classList.remove("ok");
			resultado.innerText = "Esta curp no esta disponible";
		    }
		    
		},'json');
	}
    });

    $('#addalumno').click(function() {
	// Si el id del grupo ya tiene el valor
	if($('#grupooid').val().length > 0 && $('#curp').val().length == 18){
	
	    $.post( 'updatealumnogrupo', {grupoid: $('#grupooid').val(), curp: $('#curp').val() })
		.done(function( data ) {
		    
                    var obj = jQuery.parseJSON( data );
		    
                    if (obj.result == "ok"){
						// Mostramos los datos
						alert("Alumno agregado con exito al grupo: "+obj.grupo);
						$('#grupooid').val("");
						window.location.reload();
		    		}else if(obj.result == "error"){
						alert("Ha ocurrido un error inesperado.");
		    		}
		    
		},'json');
	}else{
	    alert("No esta signado");
	}
    });
});

// Funcion que sirve para establecer el id del grupo
function setIdGrupo($idGrupo){
		// Limpiamo modal y idgrupo oculto
		$('#grupooid').val("");

		resultado.classList.remove("ok");
		resultado.innerText = "";
		$('#dataalumno').html("");
		//alert($idGrupo);
		$('#grupooid').val($idGrupo);
		//alert("Valor de grupooid: "+$('#grupooid').val());
}