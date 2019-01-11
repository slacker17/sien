<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Cicloescolar;
use App\Models\Curso;
use App\Models\Grupo;
use App\Models\Planestudio;
use DB;

Class DashboardController extends Controller
{
    // Index
    public function index(){
        // Obtenemos Ciclo escolar actual
        // Ojo tener cuidado con no tener mas de un ciclo escolar vigente porque puede tronar dependiendo del orden
        $ciclo_actual = Cicloescolar::where('vigente', '=', 1)->first();
        
        // Array que tiene los dias de la semana en spanish
        $array_dias['Sunday'] = "Domingo";
        $array_dias['Monday'] = "Lunes";
        $array_dias['Tuesday'] = "Martes";
        $array_dias['Wednesday'] = "Miércoles";
        $array_dias['Thursday'] = "Jueves";
        $array_dias['Friday'] = "Viernes";
        $array_dias['Saturday'] = "Sábado";

        // Array final de datos (calendarios)
        $array_final = [];
        
        // Dia (en cadena)
        //$dia = $array_dias[date('l', strtotime($ciclo_actual->fecha_fin))];
        
        // Diferencia de dias
        $cant_dias = $this->diferenciaDias();

        // Obtenenos todos los semestres
        $semestres = DB::table('grupos')
            ->join('ciclos_escolares', 'grupos.idcicloescolar', '=', 'ciclos_escolares.id')
            ->select(
                'grupos.semestre'
            )
            ->distinct()
            ->where('ciclos_escolares.vigente', '=', 1) // Que sea del civlo escolar vigente (el actual)
            // Los grupos que pertencen a la escuela del usuario logueado
            ->where('grupos.idescuelanormal', '=', Auth::user()->idescuelanormal) 
            ->orderBy('grupos.semestre')
            ->get();
        
        // Recorremos semestres que se cursan actualmente
        foreach($semestres as $semestre){
            //$cursos_semestre_actual = []; // Cursos del semestre actual
            
            // Obtenemos los cursos que pertenecen al semestre actual
            $cursos = Curso::where('semestre', $semestre->semestre)->get();
            foreach($cursos as $curso){
                $fechas = []; // Periodos de evaliacion
                
                // Calculamos fecha de evaluacion para SER MOSTRADAS
                // rango de division de total de dias entre la cantidad de unidades
                $rango = (int)($cant_dias / $curso->numeroUnidades);

                $cont = $rango; // Contador para ir incrementadno los dias a sumar (de cada rango de unidad)
                
                // Recorremos n veces el numero de uniades del curso actual
                for($m=0; $m < $curso->numeroUnidades; $m++){
                    // Sumamos a la fecha inicial la cantidad de dias para ir sacando los periodos (dependiendo del numero de unidades) 
                    $next_date = strtotime($ciclo_actual->fecha_inicio.'+ '.$cont.' days');
                    // Convertimos a formato date 
                    $next = date("Y-m-d",$next_date);
                    // Calculamos limite inferior(fecha inicio) y superior(fecha fin)
                    $li = strtotime($next.'- 3 days');
                    $ls = strtotime($next.'+ 3 days');
                    // Agregamos al arreglo de las fechas, las fechas de evaluacion
                    array_push($fechas, [
                        date("Y-m-d",$li),
                        date("Y-m-d",$ls)
                    ]);
                    
                    // Incrementamos contador sumandole el rango
                    $cont += $rango;
                }
                
                array_push($array_final, [
                    $curso->descripcionCurso, // Nombre del curso
                    $curso->numeroUnidades, // Numero de unidades del curso
                    $semestre->semestre, // Numero de semestre (para ordenar las tables por semestres)
                    $fechas // Array con fechas de periodo de cada unidad
                ]);
            }

        }
        
        // Rol director
        if(Auth::user()->hasRole('DIRECTOR')){
            
        }
        
        // Rol administrativo
        if(Auth::user()->hasRole('ADMINISTRATIVO')){
            
        }
        
        // Rol docente 
        if(Auth::user()->hasRole('DOCENTE')){
            
        }
        
        // Rol subdirector academico
        if(Auth::user()->hasRole('SUBDIRECTOR ACADÉMICO')){
            
        }
        
        $data['semestres'] = $semestres;
        $data['cursos'] = $array_final;
        
        return view('dashboard_custom', $data);
    }

    // Metodo que cuenta cuantos dias hay de diferencia entre dos fechas
    static function diferenciaDias(){
        // Obtenemos ciclo actual vigente
        // Ojo tener cuidado con no tener mas de un ciclo escolar vigente porque puede tronar dependiendo del orden
        $ciclo_actual = Cicloescolar::where('vigente', '=', 1)->first();        
        
        $inicio = strtotime($ciclo_actual->fecha_inicio);
   		$fin = strtotime($ciclo_actual->fecha_fin);
   		$dif = $fin - $inicio;
   		$diasFalt = (( ( $dif / 60 ) / 60 ) / 24);
   		return ceil($diasFalt);
	}

    /************************************ IMPORTANTE ****************************************************/
    /** Metodo usado para una peticion AJAX en el controlador Fechas_evaluacion_unidadesCrudController **/
    /** es usado para la asignacion de fechas de evaluacion en la vista donde se selecciona la licenciatura, el plan y el semestre **/    
    // Metodo que llenara el plan y el semestre
    public function getPlanSemestreFechas(Request $request){
        // Si es una peticion AJAX
        if($request->ajax()){
            
            // Clico escolar actual
            $ciclo_actual = Cicloescolar::where('vigente', 1)->first();
            
            // Si es para obtener los planes de estudio mdisponibles
            if($request->operacion == "planes"){
                $licenciatura = $request->licenciatura;
                // Obtenemos ids de los planes de estudio que se cursan actual,mente con licenciatura dada
                $grupos = Grupo::where('idcicloescolar', '=', $ciclo_actual->id)
                    
                    ->whereHas('escuelasnormales',
                    function($query) use ($licenciatura){
                        $query->where('licenciatura', $licenciatura);
                    })
                    ->distinct('plan_id') // Sacamos los planes de estudios disponibles
                    ->get();
                
                // Obtenemos los planes
                $planes = Planestudio::find($grupos);
                
                // Si existen planes activos 
                if(count($planes) > 0){
                    $resultado = '<option selected disable> [ -- Seleccione -- ] </option>';
                    
                    foreach($planes as $plan)
                        $resultado .= '<option value=\''.$plan->id.'\'>'.$plan->name.'</option>';
                    
                    return "{\"result\": \"".$resultado."\"}";
                }else{
                    $resultado = '<option selected disable> No hay planes activos </option>';
                    
                    return "{\"result\": \"".$resultado."\"}";
                }
            }
            
            // Si es para obtener los semestres
            else if($request->operacion == "semestres"){
                $licenciatura = $request->licenciatura;
                
                // Sacamos semestres
                $semestres = Grupo::where('idcicloescolar', '=', $ciclo_actual->id)
                    ->distinct('semestre')
                    ->where('plan_id', '=', $request->plan) // El plan id seleccionado
                    ->whereHas('escuelasnormales',
                    function($query) use ($licenciatura){
                        $query->where('licenciatura', $licenciatura);
                    })
                    ->get();
                // Si existen planes activos 
                if(count($semestres) > 0){
                    $resultado = '<option selected disable> [ -- Seleccione -- ] </option>';
                    
                    foreach($semestres as $semestre)
                        $resultado .= '<option value=\''.$semestre->semestre.'\'>'.$semestre->semestre.'</option>';
                    
                    return "{\"result\": \"".$resultado."\"}";
                }else{
                    $resultado = '<option selected disable> No hay planes activos </option>';
                    
                    return "{\"result\": \"".$resultado."\"}";
                }
            }
        }
    }
}
