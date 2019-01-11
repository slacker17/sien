$(document).ready(function(){
    // headers
     $.ajaxSetup({
            headers: {'X-CSRF-Token': $('meta[name=_token]').attr('content')}
        });

    valor_antes = "";
    id_antes = "";
    $("input[type=number]").focus(function(){
	valor_antes = $(this).val();
	id_antes = $(this).attr("id");
    });
        
     $("input[type=number]").blur(function(){
        // Obtenemos valor id oara sacar alumn, unidad y curso
        var id = $(this).attr("id");    // Obtenemos cadena de Id attr
        isReadonly = $(this).attr("readonly"); // Obtenemos el atributo readonly
        valor = $(this).val();          // Get valor del campo
        valores = {};
        etiqueta = "";
        idid = "#"+id; // Sacamaos id del input para hacerlo readonly cuando se agrega o actualiza una calif o asistecnia

        arregloDeSubCadenas = id.split('-');
        unidadd =  parseInt(arregloDeSubCadenas[0]); // Numero de unidad (calificacion o asistencia)
        alumno =  parseInt(arregloDeSubCadenas[2]);  // Id Alumno (calificaion o asistencia)
        url = "insertacalificacion";                 // Url post


	 curso = getQueryVariable('curso_id'); // Obtenemos id del curso por url (get)
	 //alert("Curso id: "+curso);
         // Si es calificacion
        if( arregloDeSubCadenas[1]  == "calif"){
           valores = {
               id_alumno:    alumno,
               unidad:       unidadd,
               calificacion: valor,
	       idcurso: curso
           };
           etiqueta = "#"+unidadd+"-rescal-"+alumno;
        }
        // Sino, si es asistencia
        else if ( arregloDeSubCadenas[1] == "asistencia"){
            valores = {
                id_alumno:  alumno,
                unidad:     unidadd,
                asistencia: valor,
		idcurso: curso
            };
            etiqueta = "#"+unidadd+"-resasis-"+alumno;
        }

        // Si hay valor en el input y no es readonly (no esta bloqueado), y el valor esta en un rango de 1 y 10
        if(valor.length != 0 && isReadonly != "readonly" && (valor >= 1 && valor <= 10) ){ 
            $.post( url, valores)
                .done(function( data ) {

                    var obj = jQuery.parseJSON( data );

                    if (obj.result == "ok"){
                        colorrr = "";
                        if(valor < 7){ // Si es reprobatorio
                            colorrr = "#FC271C"; // Asignamos color
                            $(etiqueta).html('<span aria-hidden="true">Capturado<br>No Aprobado</span>');
                        }else{// Si es aprobatorio
                            colorrr = "#047C31"; // Asignamos color
                            $(etiqueta).html('<span class="glyphicon glyphicon-ok" aria-hidden="true">Capturado</span>');
                        }
                       
                        //$(idid).attr( "readonly", "readonly" ); // Cambiamos a solo lectura el campo del input
                        $(etiqueta).css({                       // Pintamos eltilos
                            "background-color": colorrr,
                            "color": "white"
                        });
                    }else if(obj.result == "error"){
                        $(etiqueta).html('<span class="glyphicon glyphicon-remove" aria-hidden="true">No Capturado</span>');
                    }

            },'json');
        }else if( (valor < 1 || valor > 10) && isReadonly != "readonly"){
	    $(etiqueta).html('<span aria-hidden="true">Insertar numeros en <br>un rango de 1 y 10<br>El valor no ha sido cambiado</span>');
	    $(etiqueta).css({                       // Pintamos eltilos
                "background-color": "#ea8717", //"#FFC300",
                "color": "white"
            });

	    $("#"+id_antes).val(valor_antes);
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
