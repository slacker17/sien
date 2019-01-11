<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grupo_curso_user; // Modelo de la carga horaria
use App\Models\Grupo;
use App\Models\Curso;
use App\Models\Profesor;
use DB;
use Auth;
use Barryvdh\DomPDF\Facade as PDF;

class CargaHorariaController extends Controller
{
	public $idgrupo = null;
    public function index(){}

    public function llenaHorario($cargas, $cant_dias, $esDocente){
        $array_final = [];
        // Rellenamos matriz de horario para evitar errores
        for($i=0;$i < max($cant_dias); $i++){
            $aux = [];
            for($j=0; $j < 7; $j++)
                $aux[$j] = $j;

            array_push($array_final, $aux);
        }
        
        // Recorremos todos los dias (columnas)
        for( $i = 0 ; $i < 7 ; $i++ ){
            // Si el dia actual tiene al menos una carga horaria
            if( $cant_dias[$i] > 0 ){
                $cont = 0; // Contador de filas (indice)
                
                foreach($cargas as $carga){
                    if($carga->dia == $i+1){ // Si la carga actual pertenece a el dia actual
                        $tem =  array( // Default se muestra para perfil docente (muestra grupo)
                            $carga->idCargaHoraria,                     // 0 : id (para eliminar)
                            $carga->curso->descripcionCurso,            // 1 : Nombre del curso
                            $carga->grupo->descripcion,                 // 2 : Nombre grupo
                            $carga->hora_inicio." - ".$carga->hora_fin, // 3 : Horas
                            $carga->grupo->licenciatura,                // 4 : Licenciatura
                        );
                        
                        if(!$esDocente) // Para subdirector academico se muestra nombre de docente
                            $tem[2] = $carga->user->name." ".$carga->user->app." ".$carga->user->apm; // 2 : Nombre docente
                        
                        $array_final[$cont][$i] = $tem; // Asignacion de array al horario
                        $cont++;
                    }
                    if($cont > 0 and $carga->dia != $i+1) break; // Rompemos ciclo
                }
                // Rellenamos horas vacias
                for($k = $cont; $k < max($cant_dias); $k++)
                    $array_final[$k][$i] = '--';
            }else{ // Rellenamos las horas vacios
                for( $j = 0; $j < max($cant_dias); $j++ ) // REcoremos filas
                    $array_final[$j][$i] = '--';
            }
        }
        
        return $array_final; 
    }
    
    // Genracion de la vista (matriz de horario)
    // Recibimos el id del grupo
    public function generarHorario($id, $docente = 0, $pdf = 0){
    	$this->idgrupo = (int)$id; // capturamos id del grupo
	$docente = (int)$docente;
	$pdf = (int)$pdf;
        // Campo a comparar
        $campo = 'grupo_id';

        if($docente == 1)
            $campo = 'user_id';

        // Obtenemos las cargas horarias
        $cargas_horarias = Grupo_curso_user::where($campo, '=', $id )
            ->orderBy('dia')
            ->orderBy('hora_inicio')
            ->get();

        // Scamos total de horarios x dia y los guardamos en un array
        $cant_dias = [0,0,0,0,0,0,0];
        
        for($i=1; $i <= 7; $i++){
            $cont = 0; // Contador de horas por dia

            foreach($cargas_horarias as $carga){
                if($carga->dia == $i) // Si el horario pertenece al dia actual
                    $cont++;
            }

            $cant_dias[$i-1] = $cont; 
        }
        
        $grupo = Grupo::find( (int)$id );
        $profesor = Profesor::find( (int)$id );
        
        // Encabezado de tabla
    	$dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

        // Matriz a mostrar 
    	//$matriz = $this->llenaMatriz($cargas_horarias, $horas_select, $docente);

        $matriz = $this->llenaHorario($cargas_horarias, $cant_dias, $docente);
    	// Retornamos la vista

        $data = [];
        
        if((int)$docente == 0){ // si es grupo
            if($grupo){
                $data = [
                    'grupo'  => $grupo,
                    'dias'   => $dias,
                    //'horas'  => $horas,
                    'cursos' => $grupo->curso,
                    //'horas_select' => $horas_select,
                    'idgrupo' => $id,
                    'cargas' => $cargas_horarias,
                    'matriz' => $matriz,
                    'esdocente' => 0,
                    'max' => max($cant_dias),
                ];
            }
        }else if((int)$docente == 1){ // si es docente
            if($profesor){
                $data = [
                    'nombre'  => $profesor->name." ".$profesor->app." ".$profesor->apm,
                    'dias'   => $dias,
                    //'horas'  => $horas,
                    //'cursos' => $grupo->curso,
                    //'horas_select' => $horas_select,
                    //'idgrupo' => $idGrupo,
                    'cargas' => $cargas_horarias,
                    'matriz' => $matriz,
                    'esdocente' => 1,
                    'iddocente' => $profesor->id,
                    'max' => max($cant_dias),
                ];
            }
        }

        if($pdf == 0) // Si solo es una vista
            return view('carga_horaria', $data );
        else if($pdf == 1){ // Si es para generar PDF
            $pdf = PDF::loadView('carga_horaria_pdf', $data);
            
            $pdf->setPaper('A4', 'landscape');
            if($docente == 0) // Si es grupo
                return $pdf->download('horario_'.$grupo->descripcion.'.pdf');
            else if($docente == 1)
                return $pdf->download('horario_'.$profesor->name.'.pdf');
        }
    }
        
    // Metodo que obtine los docentes que imparten el curso recibido
    // Se recibe el id del cursi
    public function getDocentes(Request $request){
    	// Si es una peticion AJAX
        if($request->ajax()){
        	// Obtenemos el curso elegido
        	$curso = Curso::find( (int)$request->elegido );

        	$resultado = ''; // Cadena que contendra 

        	// Recorremos los docentes que imparten este curso
        	foreach ($curso->profesores as $docente) {
                // Si es de la misma escuela
                if($docente->idescuelanormal == Auth::user()->idescuelanormal){
                    // Agregamos cada option con su respectivo resultado
                    $resultado .=  '<option value=\''.$docente->id.'\'>'.$docente->name.' '.$docente->app.' '.$docente->apm.'</option>';
                }
        	}

        	// Si no hay docentes que impartan ese curso, mandamos un mensaje en el select
        	if($resultado == '')
        		$resultado = '<option disabled selected value>No hay docentes que impartan el curso</option>';

        	// retornamos valores
        	return "{\"result\": \"".$resultado."\"}";
        }
    }

    // Metodo que guarda una carga horaria
    public function saveCargaHoraria(Request $request){
    	// Si es una peticion AJAX
        if($request->ajax()){
            // Arrays auxliares para saber las validaciones de horario disponible en grupo y profesor
            $grupos = [0,0,0];
            $docentes = [0,0,0];

            // Variable de campos a buscar
            $campo_db = 'grupo_id'; // Para validacion de grupo
            $campo_recibido = $request->grupo; // El campo enviado a comparar
            
            // Hacemos validaciones y conteos
            for($cont = 0; $cont < 2; $cont++){
                $existeOpcion1 = Grupo_curso_user::where('dia', '=', $request->dia)
                    ->where($campo_db, '=', $campo_recibido)
                    ->where('hora_inicio', '<=', $request->horainicio)
                    ->where('hora_fin', '>=', $request->horafin)->get();
                
                $existeOpcion2 = Grupo_curso_user::where('dia', '=', $request->dia)
                    ->where($campo_db, '=', $campo_recibido)
                    ->where('hora_inicio', '>', $request->horainicio)
                    ->where('hora_inicio', '<', $request->horafin)->get();
                
                $existeOpcion3 = Grupo_curso_user::where('dia', '=', $request->dia)
                    ->where($campo_db, '=', $campo_recibido)
                    ->where('hora_fin', '>', $request->horainicio)
                    ->where('hora_fin', '<', $request->horafin)->get();

                $campos_ = [$existeOpcion1, $existeOpcion2, $existeOpcion3];

                // Recorremos resultados para llenar array contadores
                for($i=0; $i < 3; $i++){
                    // Iteramos cada resultado
                    foreach($campos_[$i] as $camp){
                        if($cont == 0) // Si estamos en grupos
                            $grupos[$i]++;
                        else if($cont == 1) // Si estamos en docentes
                            $docentes[$i]++;
                    }
                }

                $campo_db = 'user_id'; // Para validacion de grupo
                $campo_recibido = $request->docente; // El campo enviado a comparar
            }
            
        	// Si existe un horario similar no se puede guardar
        	if(array_sum($grupos) > 0 or array_sum($docentes) > 0){
        		return "{\"result\":\"errorhora\"}";
        	}else{// Si no registramos la carga horaria
        		$carga = new Grupo_curso_user; // Creamos objeto
        		$carga->grupo_id = $request->grupo; // id grupo
        		$carga->curso_id = $request->curso;
        		$carga->user_id  = $request->docente;
        		$carga->hora_inicio = $request->horainicio;
        		$carga->hora_fin = $request->horafin;
        		$carga->dia = $request->dia;
	
        		if( $carga->save() ) // si se guarda regresamos valor correcto
        			return "{\"result\":\"ok\"}";
        		else
        			return "{\"result\":\"error\"}";
        	}
        }
    }

    // Metodo para eliminar carga horaria seleccionada
    public function eliminarCargaHoraria(Request $request){
    	// Si es una peticion AJAX
        if( $request->ajax() ){
        	// Seleccionamos registro a eliminar
        	$registro = Grupo_curso_user::find( (int)$request->idcarga );

        	// Si se elimina con exito
        	if($registro->delete())
        		return "{\"result\":\"ok\"}";
        	else
        		return "{\"result\":\"error\"}";
        }
    }
   
}

 /*
    // Consulta para saber las horas ocupadas 
   select * from grupos_cursos_users where dia=1 and ('13:00:00' >= hora_inicio and '16:00:00' <= hora_fin)
   or ('13:00:00' < hora_inicio and '16:00:00' > hora_inicio)
   or ('13:00:00' < hora_fin and '16:00:00' > hora_fin);
*/


/*

// Metodo de respaldo que solo muestra el horario ordenado (sin botones)

public function llenaMatriz($cargas, $horas_select){
    	$array_final = array();

    	for($i=0; $i < 13; $i++){ // filas
    		$array_temporal = array();
    		array_push($array_temporal, $horas_select[$i].'-'.$horas_select[$i+1]); // Agregamos primera posicion de cada fila (hora)
    		for($j=1; $j < 8; $j++){ // columnas empezamos de 1 ya que la posicion 0 es de la hora ya asignada arriba
    			//array_push($array_temporal, '--');
    			$existio = false;
    			foreach($cargas as $carga){
    				if( $carga->dia == $j 
    					and 
    						// comprobamos rango de horas
    						// Si $horas_select[i] esta entre $carga->hora_inicio y $carga->hora_fin
    						($horas_select[$i] >= $carga->hora_inicio and $horas_select[$i+1] <= $carga->hora_fin)){
    						//$carga->hora_inicio == $horas_select[$i] ) { // Si es del mismo dia y la misma hora
    						//array_push($array_temporal, $carga->curso->descripcionCurso." - ".$carga->idCargaHoraria.
    						//	" ".$carga->user->name." ".$carga->user->app." ".$carga->user->apm);
    						array_push($array_temporal, $carga->curso->descripcionCurso." - ".
    							" ".$carga->user->name." ".$carga->user->app." ".$carga->user->apm);
    						$existio = true;
    						break;
    				}
    			}

    			if(!$existio)
    				array_push($array_temporal, '--');
    		}

    		array_push($array_final, $array_temporal);
    	}


    	return $array_final;
    }

*/


/*

// Metodo de respaldo que muestra el horario ordenado (con botones en cada celda en el caso de dos horas seguidas)

 public function llenaMatriz($cargas, $horas_select){
    	$array_final = array();

    	for($i=0; $i < 13; $i++){ // filas
    		$array_temporal = array();
    		array_push($array_temporal, array($horas_select[$i].'-'.$horas_select[$i+1], '-') ); // Agregamos primera posicion de cada fila (hora)
    		for($j=1; $j < 8; $j++){ // columnas empezamos de 1 ya que la posicion 0 es de la hora ya asignada arriba
    			//array_push($array_temporal, '--');
    			$existio = false;
    			foreach($cargas as $carga){
    				if( $carga->dia == $j 
    					and 
    						// comprobamos rango de horas
    						// Si $horas_select[i] esta entre $carga->hora_inicio y $carga->hora_fin
    						($horas_select[$i] >= $carga->hora_inicio and $horas_select[$i+1] <= $carga->hora_fin)){
    						//$carga->hora_inicio == $horas_select[$i] ) { // Si es del mismo dia y la misma hora
    						//array_push($array_temporal, $carga->curso->descripcionCurso." - ".$carga->idCargaHoraria.
    						//	" ".$carga->user->name." ".$carga->user->app." ".$carga->user->apm);
    						array_push($array_temporal, array($carga->curso->descripcionCurso." - ".
    							" ".$carga->user->name." ".$carga->user->app." ".$carga->user->apm, $carga->idCargaHoraria) );
    						$existio = true;
    						break;
    				}
    			}

    			if(!$existio)
    				array_push($array_temporal, array('--', '-'));
    		}

    		array_push($array_final, $array_temporal);
    	}


    	return $array_final;
    }
*/
