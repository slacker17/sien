$(document).ready(function(){
    // Cuando se selecciona el select de cursos
    $("#curso").change(function () {
        $("#curso option:selected").each(function () {
            elegido=$(this).val(); // Obtenemos id del curso seleccionado
	    
            // Mandamos datos x post para traer a los docentes que imparten el curso
            $.post( 'getdocentes',  { elegido: elegido } )
                .done(function( data ) {
		    
                    var obj = jQuery.parseJSON( data );
		    
                    $("#docente").html(obj.result);
		    
            },'json');
            
        });
    });		
    
    
    // Limpiamos los elementos del formulario modal cada vez que se agrega una carga horaria
    $( "#agregar" ).click(function() {
  	// Limpiamos
  	$("#docente").empty(); 
  	$("#msgcurso").empty();
  	$("#msgdocente").empty();
  	$("#msgdia").empty();
  	$("#msghorainicio").empty();
  	$("#msghorafin").empty();
    });
    
    //Validaciones a la hora de dar click en guardar
    $("#guardar").click(function() {
	errores = 0;
	$("#msgcurso").empty();
	$("#msgdocente").empty();
	$("#msgdia").empty();
	$("#msghorainicio").empty();
	$("#msghorafin").empty();

	// Validaciones de campos vacios
	
	// curso
  	if(!$('select[id=curso]').val()){
  	    $('#msgcurso').html("<p style='color:#FF0000';>Este campo es obligatorio</p>"); 
		    errores = errores + 1;
  	}
	
  	// docente
  	if(!$('select[id=docente]').val()){
  			$('#msgdocente').html("<p style='color:#FF0000';>Este campo es obligatorio</p>"); 
	    errores = errores + 1;
  	}
	
  	// dia
  	if(!$('select[id=dia]').val()){
  	    $('#msgdia').html("<p style='color:#FF0000';>Este campo es obligatorio</p>"); 
	    errores = errores + 1;
  	}
	
  	// hora inicio
  	if(!$('#horainicio').val()){
  	    $('#msghorainicio').html("<p style='color:#FF0000';>Este campo es obligatorio</p>"); 
	    errores = errores + 1;
  	}
	
  	// hora fin 
  	if(!$('#horafin').val()){
  	    $('#msghorafin').html("<p style='color:#FF0000';>Este campo es obligatorio</p>"); 
	    errores = errores + 1;
  	}

	// Validacion que los rangos sean al menos de 50 minutos
	if( $('#horainicio').val() < $('#horafin').val()  ){
	    // Si la cantidad de la hora fin menos la cantidad de hora inicio es menor a 50
	    // Mostramos mensaje de que el rango de horas debe ser mayor a 50 min 
	    if( (devolverMinutos($('#horafin').val()) - devolverMinutos($('#horainicio').val()))  < 50 ){
		alert("El rango de horas debe ser mínimo de 50 minutos. Favor de revisar.");
		errores = errores + 1;
	    }
	}
		
	// Validacion rango de horas
	if( $('#horafin').val() < '06:59:00' || $('#horafin').val() > '21:01:00' ){
	    errores = errores + 1;
	    $('#msghorafin').html("<p style='color:#FF0000';>Por favor verifique que la hora final sea mayor a 7:00 AM y menor a 9:00 PM</p>"); 
	}
	
	if( $('#horainicio').val() < '06:59:00' || $('#horainicio').val() > '21:01:00' ){
	    errores = errores + 1;
	    $('#msghorainicio').html("<p style='color:#FF0000';>Por favor verifique que la hora inicial sea mayor a 7:00 AM y menor a 9:00 PM</p>"); 
	}
	
	// Comparacion de horas ilogicas (por ejemplo hora_inicio=8:00 - hora_fin=10:00 o hora_inicio=8:00 - hora_fin=8:00)
	if($('#horafin').val() <= $('#horainicio').val()){
	    errores = errores + 1;
	    $("#msghorainicio").empty();
	    $("#msghorafin").empty();
	    $('#msghorainicio').html("<p style='color:#FF0000';>Por favor verifique que la hora de inicio sea menor a la hora final</p>"); 
	    $('#msghorafin').html("<p style='color:#FF0000';>Por favor verifique que la hora final sea mayor a la hora de inicio</p>"); 
	}
	
  	// Si no hay errores enviamos para registro de carga horaria
  	if(errores == 0){
  	    // Mandamos datos x post para registrar carga horaria
	    $.post( 'savecargahoraria',  {  curso: $('select[id=curso]').val(), 
					    docente: $('select[id=docente]').val(),
					    horainicio: $('#horainicio').val(),
						horafin: $('#horafin').val(),
					    dia: $('select[id=dia]').val(),
					    grupo: $('#grupoid').val()
                                         } )
                .done(function( data ) {
		    
			var obj = jQuery.parseJSON( data );
			
			if(obj.result == "ok"){ // si se registro correctamente
                    	    alert("Agregado exitosamente!");
                    	    window.location.reload();
			}else if(obj.result == "errorhora"){ // si ya hay un registro con ese dia y hora
                    	    alert("La hora del grupo o docente seleccionado, ya esta ocupada en ese dia, por favor seleccione otra.");
			}else if(obj.result == "error"){ // si ya hay un registro con ese dia y hora
                    	    alert("Ha ocurrido un error inesperado, disculpe las molestias.");
                    	    window.location.reload();
			}
			
		    },'json');
  	    }
	});
});

// Aliminar una carga horaria a traves del id
function eliminarCarga(id){
    var r = confirm("Desea eliminar la carga horaria seleccionada?");
    if (r == true) {
          $.post( 'deletecargahoraria' , { idcarga: id } )
                .done(function( data ) {
                    var obj = jQuery.parseJSON( data );
                    if(obj.result == "ok"){ // si se registro correctamente
                      alert("Eliminado exitosamente!");
                      window.location.reload();
                    }else if(obj.result == "error"){
                      alert("Error en la operacion.");
                    }
                },'json');
    }
}

// Funcion para obtener los minutos totales... 10:30=630 minutos
function devolverMinutos(horaMinutos)
{
    return (parseInt(horaMinutos.split(":")[0])*60)+parseInt(horaMinutos.split(":")[1]);
}
