<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\Curso;
use App\Models\Profesor;
use App\Models\Cicloescolar;
use App\Calificacion;
use App\Http\Controllers\DashboardController;
//use App\Http\Controllers\Controller;

class AltaCalificacionController extends Controller
{

	// Index
    public function index(){

    }

    // Metodo que verifica si una fecha dada (la actual por default) esta entre un rango de otras dos
    public function fechaActualEstaEnRango($f_ini, $f_fin){
        $hoy = new \DateTime("now"); // Fecha de hoy
        $fi = new \DateTime($f_ini); // Fecha inicio
        $ff = new \DateTime($f_fin); // Fecha fin
        
        if($hoy >= $fi and $hoy <= $ff) // Si estamos en el rango
            return true;   // Retornamos true
        else
            return false;  // Retornamos false
            
    }

    // Metodo que busca si un elemento esta en el array (en este caso el numero de la unidad)
    public function existeUnidadEnArreglo($buscar, $en){
        foreach ($en as $elemento ) {
            if( (int)$elemento[0] == (int)$buscar ){
                return true;
                break;
            }
        }

        return false;
    }

    // Rellenar array de calificaciones
    public function llenaArrayCalificaciones($array_calificaciones, $alumno, $curso, $fechas_evaluacion){
        $array_resultado = array();

        // Si hay al menos una calificaion asignada
        if( count($array_calificaciones) > 0 ){
            // Primero extraemos los valores de las calificaciones que ya pueden estar asignadas
            foreach ($array_calificaciones as $array) {
                $temp = array(); // Arreglo temporal
                array_push($temp, $array->unidad);          // Position 0 Unidad
                array_push($temp, $array->id_calificacion); // Position 1 Calificacion Id
                array_push($temp, $array->id_alumno);       // Position 2 Alumno Id
                array_push($temp, $array->id_curso);        // Position 3 Curso Id
    
                if($array->calificacion == null) array_push($temp, "no");     // Position 4 Calificacion Valor
                else array_push($temp, $array->calificacion);                 // Position 4 Calificacion Valor
    
                if($array->asistencia == null)  array_push($temp, "no");      // Position 5 Asistencia Valor
                else array_push($temp, $array->asistencia);                   // Position 5 Asistencia Valor

                // Recorremos fechas de evaluacion para saber si la fecha esta activa
                foreach($fechas_evaluacion as $fecha){
                    if($fecha[2] == $array->unidad){ // Si es la unidad actual
                        // Comparamos el rango de fechas
                        
                        //if($this->fechaActualEstaEnRango($fecha[0], $fecha[1])) // Si estamops en el rango
                        if($fecha[3])
                            array_push($temp, "activo"); // Position 6 activo o inactivo dependiendo del periodo de evaluacion
                        else
                            array_push($temp, "no"); // Position 6 activo o inactivo dependiendo del periodo de evaluacion
                    }
                }
                
                // Agregamos al arreglo temporal los campos necesarios
                array_push($array_resultado, $temp);
            }
        }

        // recorremos la cantidad n de numero de unidades dependiendo del curso
        for($i = 0 ; $i < $curso->numeroUnidades ; $i++){
            // Si no existe la unidad actual ($i + 1) en el arreglo final se agrega como vacia
            if( !$this->existeUnidadEnArreglo( $i+1 , $array_resultado) ){
                $temp = array();                // Arreglo temporal
                array_push($temp, $i+1);        // Position 0 Unidad
                array_push($temp, "no");        // Position 1 Calificacion Id
                array_push($temp, $alumno);     // Position 2 Alumno Id
                array_push($temp, $curso->id_curso);      // Position 3 Curso Id
                array_push($temp, "no");        // Position 4 Calificacion Valor
                array_push($temp, "no");        // Position 5 Asistencia Valor

                // Recorremos fechas de evaluacion para saber si la fecha esta activa
                foreach($fechas_evaluacion as $fecha){
                    if($fecha[2] == $i+1){ // Si es la unidad actual
                        // Comparamos el rango de fechas
                        
                        //if($this->fechaActualEstaEnRango($fecha[0], $fecha[1]))// Si estamops en el rango
                        if($fecha[3])
                            array_push($temp, "activo"); // Position 6 activo o inactivo dependiendo del periodo de evaluacion
                        else
                            array_push($temp, "no"); // Position 6 activo o inactivo dependiendo del periodo de evaluacion
                    }
                }
                
                // Agregamos al arreglo temporal los campos necesarios
                array_push($array_resultado, $temp);
            }
        }

        sort($array_resultado); // Ordenamos array 
        return $array_resultado; // Retornamos y ordenamos array_resultado menor a menor de acuerdo a las unidades
    }

    // Function get requests
    // Recibimos: 
    // idGrupo seleccionado
    // idUsuario (docente) logueado
    public function listado($idGrupo){
        // Obtenemos id del curso pasado por get
        $curso_id_recibido = \Request::input('curso_id');

        // Obtenemos datos del curso
        $cursoo = Curso::find((int) $curso_id_recibido);
        
        $ciclo_actual = Cicloescolar::where('vigente', '=', 1)->first();        
        // Obtenemos las fechas de evalucion de cada unidad del curso seleccionado
        $fechas = []; // Periodos de evaliacion
                
        // Calculamos fecha de evaluacion para SER MOSTRADAS
        // rango de division de total de dias entre la cantidad de unidades
        $rango = (int)(DashboardController::diferenciaDias() / $cursoo->numeroUnidades);
        
        $cont = $rango; // Contador para ir incrementadno los dias a sumar (de cada rango de unidad)
        
        // Recorremos n veces el numero de uniades del curso actual
        for($m=0; $m < $cursoo->numeroUnidades; $m++){
            // Sumamos a la fecha inicial la cantidad de dias para ir sacando los periodos (dependiendo del numero de unidades) 
            $next_date = strtotime($ciclo_actual->fecha_inicio.'+ '.$cont.' days');
            // Convertimos a formato date 
            $next = date("Y-m-d",$next_date);
            // Calculamos limite inferior(fecha inicio) y superior(fecha fin)
            $li = strtotime($next.'- 3 days');
            $ls = strtotime($next.'+ 3 days');
            // Agregamos al arreglo de las fechas, las fechas de evaluacion
            array_push($fechas, [
                date("Y-m-d",$li), // Position 0: Limite inferior
                date("Y-m-d",$ls), // Position 1: limite superior
                $m+1, // Contador de unidad // Position 2: contador de unidad
                // True o false si esta en el rango de fechas
                $this->fechaActualEstaEnRango(date("Y-m-d",$li), date("Y-m-d",$ls)) // Position 3: boolean rango de fechas
            ]);
            
            // Incrementamos contador sumandole el rango
            $cont += $rango;
        }
        
        
    	// Obtenemos el listado de los alumnos que pertenecen al grupo seleccionado
    	$alumnos = Alumno::where('idgrupo', (int)$idGrupo)
                                ->orderBy('app')->orderBy('apm')->orderBy('nombre')
                                ->get();
        $grupo = Grupo::find($idGrupo); // Get grupo

        $calificaciones = array();// Array final que contendra las calificaciones

        // Obtenemos calificaciones de cada alumno (recorriendo el array de alumnos)
        // Para eso necesitamos el id_curso y sus unidades (parciales)
        foreach ($alumnos as $alumno) {
            // Calificaciones del alumno actual (id_alumno, id_curso)
            $current_alumno_califs = Calificacion::where('id_alumno', (int) $alumno->id_Alumno)
                                               ->where('id_curso', (int) $curso_id_recibido)
                                               ->orderBy('unidad')->get();
            /*
            $current_alumno_califs = Calificacion::where('id_alumno', (int) $alumno->id_Alumno)
                                               ->where('id_curso', (int) Auth::user()->cursos->id_curso)
                                               ->orderBy('unidad')->get();*/

            // Rellenamos campos para hacer uso en la vista
            $array_campos = $this->llenaArrayCalificaciones(
                $current_alumno_califs, 
                (int) $alumno->id_Alumno,
                $cursoo,
                $fechas
                //(int) $curso_id_recibido
            );
            
            // Agregamos las calificaciones del alumno actual a la global
            array_push($calificaciones, $array_campos); 
        }

        // Fecha actual
        $now = new \DateTime();
        $hoy =  $now->format('Y-m-d');
        
        // Retornamos valores y vista
    	return view('listado_calificaciones', 
                [
                 'calificaciones' => $calificaciones,
                 'alumnos' => $alumnos,
                 'nombreCurso' => $cursoo->descripcionCurso, //Auth::user()->cursos->descripcionCurso,
                 'numeroUnidades' => $cursoo->numeroUnidades, //Auth::user()->cursos->numeroUnidades,
                 'grupo' => $grupo->descripcion,
                 'idgrupo' => $grupo->id_Grupos,
                 'idcurso' => $cursoo->id_curso,
                 'fechas' => $fechas, // Los periodos de evaluacion de cada unidad
                 'fechahoy' => $hoy,
                ]);
    }

    // Funcion que genera ids para los input de cada calificaion
    // Tambien utilizada por ajax para generar los eventos
    public function generaIds(Request $request = null){
        // Si es para llenar los eventos en jquery
        if($request->ajax()){

        }else{ // si no solo se generan para la vista utilizada en el metodo listado

        }
    }

    // Save calificacion
    public function saveCalificacion(Request $request){
        // Obtenemos id del curso pasado por get
        //$curso_id_recibido = \Request::input('curso_id');

        // Obtenemos datos del curso
        //$cursoo = Curso::find((int) $curso_id_recibido);
        // Si es una peticion AJAX
        if($request->ajax()){
            // Comprobamos si hay un registro igual (idAlumno, idCurso, unidad)
            // Si existe es xq ya registraron asistencia o es actualizacion
            $existe = Calificacion::where('id_alumno', (int) $request->id_alumno)
                ->where('id_curso', $request->idcurso)//(int) Auth::user()->cursos->id_curso )
                ->where('unidad', $request->unidad)->first();
            // Comprobamos si existe
            if ( $existe ){ // Si si actualizamos
                // Checamos si es actualizacion de calificacion o de asistencia
                // si es calificacion
                if($request->calificacion)
                    $existe->calificacion = $request->calificacion;
                else if($request->asistencia)// si no es de asistencia
                    $existe->asistencia   = $request->asistencia;

                if($existe->save()) // Si se guarda (actualiza) con exito
                    return "{\"result\":\"ok\"}";
                else
                    return "{\"result\":\"error\"}";
            }else{// si no insertamos uno nuevo
                 // Creamos el objeto calificacion a partir del modelo Calificacion para guardarlo
                $calificacion = new Calificacion;
                // Asignacion del alumno que contiene la calificacion
                $calificacion->id_alumno = $request->id_alumno;
                // Asignacion del curso que contiene la calificacion
                $calificacion->id_curso = $request->idcurso;//Auth::user()->cursos->id_curso;//$request->id_curso;
                
                // Checamos si es para calificacion o asistencia
                // Si es calificacion
                if($request->calificacion)    
                    // Asignacion de la calificacion (digito)
                    $calificacion->calificacion = $request->calificacion;
                else if($request->asistencia)
                    // Asignacion de la asistencia
                    $calificacion->asistencia   = $request->asistencia;
                
                // Asignacion de la unidad de la calificacion 
                $calificacion->unidad = $request->unidad;

                if($calificacion->save()) // Si se guarda con exito
                    return "{\"result\":\"ok\"}";
                else
                    return "{\"result\":\"error\"}";
            }
        }
    }

}

/*

     @for($i = 0; $i < $numeroUnidades; $i++)
                    <td><input type="number" class="form-control" name="cal" id="{{$i+1}}unidad{{$alumno->id_Alumno}}" min="1" max="10">
                      <pre id='rescal'><FONT SIZE=1>No Capturado</font></pre>
                    </td>
                    <td><input type="number" class="form-control" name="cal" id="{{$i+1}}asistencia{{$alumno->id_Alumno}}" min="1" max="10">
                      <pre id='resasis'><FONT SIZE=1>No Capturado</font></pre>
                    </td>
                  @endfor

*/

/*

 @foreach ($calificaciones as $calificacion)
                    
                    @if($calificacion[0][1] == $alumno->id_Alumno)
                       
                        @foreach($calificacion as $celda)

                            @if($celda[3] == "no")
                               <td><input type="number" class="form-control" name="cal" id="{{$celda[4]}}-calif-{{$alumno->id_Alumno}}" min="1" max="10">
                                <pre id='{{$celda[4]}}-rescal-{{$alumno->id_Alumno}}'><FONT SIZE=1>No Capturado</font></pre></td>
                            @else
                                <td><input type="number" value="{{$celda[3]}}" readonly="readonly" class="form-control" name="cal" id="{{$celda[4]}}-calif-{{$alumno->id_Alumno}}" min="1" max="10">
                      <pre id='{{$celda[4]}}-rescal-{{$alumno->id_Alumno}}'><FONT SIZE=1><span class="glyphicon glyphicon-ok" aria-hidden="true">Capturado</span></font></pre></td>
                            @endif


                            @if($celda[5] == "no")
                               <td><input type="number" class="form-control" name="cal" id="{{$celda[4]}}-asistencia-{{$alumno->id_Alumno}}" min="1" max="10">
                                <pre id='{{$celda[4]}}-resasis-{{$alumno->id_Alumno}}'><FONT SIZE=1>No Capturado</font></pre></td>
                            @else
                                <td><input type="number" value="{{$celda[5]}}" readonly="readonly" class="form-control" name="cal" id="{{$celda[4]}}-asistencia-{{$alumno->id_Alumno}}" min="1" max="10">
                      <pre id='{{$celda[4]}}-resasis-{{$alumno->id_Alumno}}'><FONT SIZE=1><span class="glyphicon glyphicon-ok" aria-hidden="true">Capturado</span></font></pre></td>
                            @endif


                        @endforeach

                    @endif
                  
                  @endforeach
                    </tr>
               @endforeach

*/
