<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Backpack\NewsCRUD\app\Models\Category;
use App\User;
use App\Models\Grupo;
use App\Models\Alumno;
use App\Models\Curso;
use App\Models\Escuelanormal;
use App\CodigoPostal;
    
class ProfesorNormalController extends Controller{
    /************ ADVERTENCIA: Cuidado con el metodo paginate() ************/
    /* El parametro que recibe es la cantidad de elementos a mostrar */
    /* por default muestra 15, si se desean mostrar mas registros aumentar la cantidad */
    
    public function index(Request $request){
        if ($request){

            // Obtenemos usuarios (docentes que pertenecen a la misma escuela del usuario logueado)
            $results = Grupo::where('idescuelanormal', Auth::user()->idescuelanormal)
                ->paginate(100);
        }
        else{
            $results = Grupo::paginate(10);
        }

        return $results;
    }

    // Metodo para asignar grupos a administrativos
    // usado por el subdirector academico
    public function getAdministrativos(Request $request){
	if($request){
		$namerol = 'ADMINISTRATIVO';
		$results = User::where('idescuelanormal', '=', Auth::user()->idescuelanormal)
			->whereHas('roles',
				function($query) use ($namerol){
					$query->where('name', $namerol);
				})
				->paginate(100);
	}else{
		$results = User::paginate(10);
	}

	return $results;
    }

    /*********** Peticiones de alta de alumnos en la bd en administrtivos ***********/

    // Obtenemos los grupos que tiene el usuario loqueado (administrativo)
    // Esto para registrar a un alumno en la bd 
    public function getGruposAdministrativo(Request $request){
        if($request){
            /*********************************************/
            /***************** IMPORTANTE ****************/
            /*********************************************/
            // FALTA AGRREGAR where DEL CICLO ESCOLAR PARA SACAR LOS GRUPOS ACTIVOS DE ESTE CICLO ESCOLAR
            $grupos = Grupo::where('idadministrativo', '=', Auth::user()->id)->paginate(100);
            return $grupos;
        }else{
            $grupos = Grupo::paginate(10);
        }
        
        return $grupos; // Retornamos grupos
    }
    
    /*********** Peticiones de asignacion de grupos a docentes ***********/

    // Obtenemos los cursos que da el docente
    public function getCursos(Request $request){
        if($request){
            $user_id = $request->input('user_id'); // Get user id from request (profesor)
            // Obetenemos los cursos que tiene el id del profesor pasado en el request
            $cursos = Curso::whereHas('profesores',
                                function($query) use ($user_id){
                                    $query->where('id', $user_id);
                                })
                                ->paginate(100); // Paginacion del resultado
        }else{
            $cursos = Curso::paginate(10);
        }

        return $cursos;
    }

    public function show($id){
        return Category::find($id);
    }

    // Funcion que busca el estado municipio y colonias a traves del codigo postal
    public function getDomicilio(Request $request){
        if($request->ajax()){
        	// Obtenemos el curso elegido
        	//$cp = CodigoPostal::where( 'CodigoPostal', (int)$request->cp )
                //->first();

	    $cp = CodigoPostal::where('cp', $request->cp)->get();
	    $i = 0;
            foreach($cp as $c){$i++;}

            // Si si se encontro algun registro con el cp recibido
            if($i > 0){
                $resultado = ''; // Cadena que contendra los option select
                
                // Extraemos colonias
                //$colonias = explode(";", $cp->Colonia);
                
                // Recorremos las colonias para generar los option select
                foreach($cp as $colonia)
                    // Agregamos cada option con su respectivo resultado
                    $resultado .=
                    '<option value=\''.$colonia->d_asenta.'\'>'.$colonia->d_asenta.'</option>';

                // retornamos valores
                return "{\"result\": \"ok\", \"estado\": \"".
                    $colonia->d_estado.
                    "\", \"municipio\": \"".
                    $colonia->D_mnpio."\", \"colonias\": \"".$resultado."\" }";
            }else{
                // retornamos valores
                return "{\"result\": \"noexistecp\", \"cp\": \"".$request->cp."\" }";
            }
        }
    }

    // Funcion que obtine ,os cursos que se le asiganaran al docente
    // para mostrar cursos de primaria o preescolar
    public function getCursosByNormal(Request $request){
        if($request){
            // Obtenemos la escuela del usuario que esta logueado
            $escuela = Escuelanormal::find( (int)Auth::user()->idescuelanormal );
            
            // Si es preescolar
            if($escuela->nombre == "NORMAL PREESCOLAR")
                $cursos = Curso::where('licenciatura', '=', 'PREESCOLAR')->paginate(100);
            else // Si no es para primarias
                $cursos = Curso::where('licenciatura', '=', 'PRIMARIA')->paginate(100);
            
            return $cursos;        
        }else{
            $cursos = Curso::paginate(10);
            return $cursos;        
        }
        
    }
}

?>
