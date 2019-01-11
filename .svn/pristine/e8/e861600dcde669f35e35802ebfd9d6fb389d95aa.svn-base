$(document).ready(function(){
    
    //$('#cont_semestre').hide();
    $.ajaxSetup({
	headers: {
	    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
    });
    
    // Acciones de select de licenciaturas (que es la raiz)
    $("#licenciaturas").change(function(){
	var op = $("#licenciaturas option:selected").val();

	// Limpiamos select de semestres
	$('#semestres').empty();
	
	$.post( 'getplansemestre',  {
	    operacion: "planes",
	    licenciatura: $("#licenciaturas option:selected").val()
	} )
	    .done(function( data ) {
		var obj = jQuery.parseJSON( data );
		// Llenamos slect de planes
		$('#planes').html(obj.result);
	    },'json');	
    });
    
    // Acciones de select de planes
    $("#planes").change(function(){
	var op = $("#planes option:selected").val();
	
	$.post( 'getplansemestre',  {
	    operacion: "semestres",
	    licenciatura: $("#licenciaturas option:selected").val(),
	    plan: $("#planes option:selected").val()
	} )
	    .done(function( data ) {
		var obj = jQuery.parseJSON( data );
		// Llenamos slect de semestres
		$('#semestres').html(obj.result);
	    },'json');	
    });
    
    // Acciones para el boton enviar (mandar al formulario de fechas de evaluacion)
    $("#planes").click(function(){
	lic = $("#licenciaturas option:selected").val();
	plan = $("#planes option:selected").val();
	semestre = $("#semestres option:selected").val();

	// Redireccionamos al formulario
	
    });


    /**** Parte de guardar las fechas ****/
    
    
});
