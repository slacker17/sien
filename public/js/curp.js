$(document).ready(function(){
    $('#curp').after("<pre id='resultado'> </pre>");
    $('#cp').after("<pre id='resultadocp'> </pre>");

    
    // Comprobamos si hay errores para limpiar elementos de estado, municipio y localidad
    if ( $(".callout")[0] ) {
	// hacer algo aquí si el elemento existe
	// Limpiamos campos
	$('#estado').empty();
	$('#municipio').empty();
	$('#localidad').empty();
    }
    
    // Parte del nombre de usuario en el formualrio de docentes y administrativos
    
    $('#apm, #app, #name').blur(function(){
    	nombre = $('#name').val();
	app = $('#app').val();
	apm = $('#apm').val();

	nombreusuario = '';

	if(nombre.length > 0 && app.length > 0 && apm.length > 0){
	    //quitamos espacios en blanco
	    nombre = nombre.replace(" ", "");
	    app = app.replace(" ", "");
	    apm = apm.replace(" ", "");

	    // Extraemos primera letra del nombre
	    nombre = nombre.substring(0, 1);
	    // Extraemos primera letra del apellido materno
	    apm = apm.substring(0, 1);
	    // Establecemos nombre de usuarios con su id
	    $('#username').val(nombre.toLowerCase()+app.toLowerCase()+apm.toLowerCase());
    	}else{
	    $('#username').empty();
	}

    });

    //Parte del codigo postal (muestra estado, municipio y colonias con select)
    $('#cp').blur(function(){
	$('#estado').val(null);
        $('#municipio').val(null);
        $('#localidad').val(null);

	//alert($('#cp').val());
	if($('#cp').val().length > 0){
	    // Mandamos datos x post para traer al estado, municipio y localidades
            $.post( 'getdomicilio',  { cp: $('#cp').val() } )
		.done(function( data ) {
		    
                    var obj = jQuery.parseJSON( data );
		    resultadocp = document.getElementById("resultadocp");
		    if(obj.result == "ok"){ // si se encotraron registros con el cp proporcionado
			resultadocp.classList.add("ok");
			resultadocp.innerText = "Correcto";
			$('#estado').val(obj.estado);
			$('#municipio').val(obj.municipio);
			$('#localidad').html(obj.colonias);
                    }else if(obj.result == "noexistecp"){
			resultadocp.classList.remove("ok");
			resultadocp.innerText = "No válido: Por favor verifique el código postal.";
			//alert(obj.cp);
			$('#estado').empty();
			$('#municipio').empty();
			$('#localidad').empty();
                    }
		},'json');
	}
    });
});

function mayusculas(field) {
    field.value = field.value.toUpperCase();
}

function validarInput(input) {

    var curp = input.value.toUpperCase(),
        resultado = document.getElementById("resultado"),
        valido = "No válido";
        
    resultado.innerText = input;

    if (curpValida(curp)) {
        valido = "Válido";
        resultado.classList.add("ok");
    } else {
        resultado.classList.remove("ok");
    }
    //alert("CURP: " + curp + "\nFormato: " + valido);    
    resultado.innerText = "Formato: " + valido;
   //$('a').innerText = "CURP: " + curp + "\nFormato: " + valido;
}

function curpValida(curp) {
	var re = /^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0\d|1[0-2])(?:[0-2]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/,
    	validado = curp.match(re);
	
    if (!validado)  //Coincide con el formato general?
    	return false;
    
    //Validar que coincida el dígito verificador
    function digitoVerificador(curp17) {
        //Fuente https://consultas.curp.gob.mx/CurpSP/
        var diccionario  = "0123456789ABCDEFGHIJKLMNÑOPQRSTUVWXYZ",
            lngSuma      = 0.0,
            lngDigito    = 0.0;
        for(var i=0; i<17; i++)
            lngSuma= lngSuma + diccionario.indexOf(curp17.charAt(i)) * (18 - i);
        lngDigito = 10 - lngSuma % 10;
        if(lngDigito == 10)
            return 0;
        return lngDigito;
    }
    if (validado[2] != digitoVerificador(validado[1])) 
    	return false;
        
	return true; //Validado
}
