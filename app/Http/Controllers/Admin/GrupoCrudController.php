<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Auth;
use App\User;
use App\Models\Alumno;
use App\Models\Curso;
use App\Models\Cicloescolar;
use App\Models\Grupo;
use App\Models\Planestudio;
use App\Models\Escuelanormal;
use App\Models\Grupo_curso_user; // Para sacar los grupos del docente
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\GrupoRequest as StoreRequest;
use App\Http\Requests\GrupoRequest as UpdateRequest;
use Illuminate\Http\Request;
use DB;

class GrupoCrudController extends CrudController
{
    public function setup()
    {

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Grupo');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/grupo');
        $this->crud->setEntityNameStrings('grupo', 'grupos');

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */

        // $this->crud->setFromDb();
        $this->crud->setColumns([
            [
                'name'  => 'descripcion',
                'label' => 'Grupo',
                'type'  => 'text',
            ],

            // Plan de estudios al que pertenece
            [
                // 1-n relationship
                'label' => "Plan de estudios", // Table column heading
                'type' => "select",
                'name' => 'plan_id', // the column that contains the ID of that connected entity;
                'entity' => 'plan', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Planestudio", // foreign key model
            ],
    	]);
        
         // Fields
        $this->crud->addFields([
            // Semestre
            [
                'name'  => 'semestre',
                'label' => 'Semestre',
                'type' => 'select2_from_array',
                'options' => [
                    null => '[ -- Seleccione -- ]',
                    '1' => '1', '2' => '2',
                    '3' => '3', '4' => '4', '5' => '5',
                    '6' => '6', '7' => '7',
                    '8' => '8'
                ],
                'allows_null' => false,

                'default' => false,
                
            ],
            // Grupo
            [
                'name'  => 'grupo',
                'label' => 'Grupo',
                'type' => 'select2_from_array',
                'options' => [
                    null => '[ -- Seleccione -- ]',
                    'A' => 'A', 'B' => 'B',
                    'C' => 'C', 'D' => 'D',
                    'E' => 'E', 'F' => 'F',
                    'G' => 'G', 'H' => 'H',
                ],
                'allows_null' => false,
                'default' => false,
                
            ],

            [   // Hidden
                'name' => 'idescuelanormal',
                'type' => 'hidden',
                'value' => Auth::user()->idescuelanormal,
            ],

            /** Pendiente **/
            [   // Hidden cicloescolar (Por default tomamos el actual)
                'name'  => 'idcicloescolar',
                'type'  => 'hidden',
                'value' => 0, // Cambiar el 0
            ],


            [   // Hidden cursado
                'name'  => 'cursado',
                'type'  => 'hidden',
                'value' => 0, // Cambiar el 0
            ],

        ]);

        // Agregamos el field de plan de estudios
        // Sacamos todos los planes de estudios para meterlos en el select y hacerlo dinamico
        $planes = Planestudio::all();
        $array_planes = [
            null => '[ -- Seleccione -- ]'
        ];
        
        foreach($planes as $plan){  $array_planes[$plan->id] = $plan->name;  }
        
        // Plan de estudios
        $this->crud->addField([
            'name' => 'plan_id',
            'label' => 'Plan de estudios',
            'type' => 'select2_from_array',
            'options' => $array_planes,
            'allows_null' => false,
            'default' => false,
        ]);
        
        // Si el rol es de subdirector academico, mostramos la columna del administrativo asignado
        if(Auth::user()->hasRole('SUBDIRECTOR ACADÉMICO')){
            $this->crud->addColumn([
                'name' => 'idadministrativo',
                'label' => 'Administrativo Asignado',
                'type' => 'select',
                'entity' => 'administrativo',
                'attribute' => 'full_name',
                'Model' => "App\User",	
            ]);

        }

	// Si es el cam mostramos el field de licenciatura                                                                                                              
        if( Auth::user()->idescuelanormal == 1 && Auth::user()->hasRole('SUBDIRECTOR ACADÉMICO') ){
            $this->crud->addField([
                'name'  => 'licenciatura',
                'label' => 'Licenciatura',
                'type' => 'select2_from_array',
                'options' => [
                    null => '[ -- Seleccione -- ]',
                    'PRIMARIA' => 'PRIMARIA',
                    'SECUNDARIA CON ESPECIALIDAD EN ESPAÑOL' => 'SECUNDARIA CON ESPECIALIDAD EN ESPAÑOL',
	            'SECUNDARIA CON ESPECIALIDAD EN MATEMÁTICAS' => 'SECUNDARIA CON ESPECIALIDAD EN MATEMÁTICAS',                ],
                'allows_null' => false,
                'default' => false,
            ]);

            $this->crud->addColumn([
                'name'  => 'licenciatura',
                'type'  => 'text',
                'label' => 'Licenciatura',
            ]);

            $this->crud->removeColumn('plan_id');
            $this->crud->removeField('plan_id');
        }
        // ------ CRUD FIELDS
        // $this->crud->addField($options, 'update/create/both');
        // $this->crud->addFields($array_of_arrays, 'update/create/both');
        // $this->crud->removeField('name', 'update/create/both');
        // $this->crud->removeFields($array_of_names, 'update/create/both');

        // ------ CRUD COLUMNS
        // $this->crud->addColumn(); // add a single column, at the end of the stack
        // $this->crud->addColumns(); // add multiple columns, at the end of the stack
        // $this->crud->removeColumn('column_name'); // remove a column from the stack
        // $this->crud->removeColumns(['column_name_1', 'column_name_2']); // remove an array of columns from the stack
        // $this->crud->setColumnDetails('column_name', ['attribute' => 'value']); // adjusts the properties of the passed in column (by name)
        // $this->crud->setColumnsDetails(['column_1', 'column_2'], ['attribute' => 'value']);

        // ------ CRUD BUTTONS
        // $this->crud->addButtonFromModelFunction('line', 'open_cursos', 'asignarCursos', 'beginning');
        // possible positions: 'beginning' and 'end'; defaults to 'beginning' for the 'line' stack, 'end' for the others;
        // $this->crud->addButton($stack, $name, $type, $content, $position); // add a button; possible types are: view, model_function
        // $this->crud->addButtonFromModelFunction($stack, $name, $model_function_name, $position); // add a button whose HTML is returned by a method in the CRUD model
        // $this->crud->addButtonFromView($stack, $name, $view, $position); // add a button whose HTML is in a view placed at resources\views\vendor\backpack\crud\buttons
        // $this->crud->removeButton($name);
        // $this->crud->removeButtonFromStack($name, $stack);
        // $this->crud->removeAllButtons();
        // $this->crud->removeAllButtonsFromStack('line');

        // ------ CRUD ACCESS
        // $this->crud->allowAccess(['list', 'create', 'update', 'reorder', 'delete']);

        // Si el rol del usuario es docente, se mostraran solo los grupos que tiene asignados sin operacion de editar ni eliminar ni agregar
        if(Auth::user()->hasRole('DOCENTE')){
            // Permisos de ver y hacer cosas
            $this->crud->denyAccess(['create', 'update', 'reorder', 'delete']);
            
            // Recibiremos el id del curso ***************
            $curso_id_recibido = \Request::input('curso_id');
            
            //
            // Sacamos relacion de docente con curso seleccionado
            $somesCargasHorarias = Grupo_curso_user::where('curso_id', '=', $curso_id_recibido)
                ->where('user_id', '=', Auth::user()->id)
                ->get(); 
            
            $idsGrupos = $this->getIdGrupos($somesCargasHorarias); // Extraemos solo los Id de los grupos
            // Querys
            $this->crud->addClause('whereIn', 'id_Grupos', $idsGrupos);            
            //$this->crud->addClause('whereHas', 'curso', function($query){
            //    $query->where('id_curso', Auth::user()->id_curso); // Recibir id curso por post y idprofesor
            //});
            
            // Actions Buttons
            $this->crud->addButtonFromModelFunction('line', 'calificaciones_asistencia', 'calificacionYAsistencia', 'beginning');

            // Pasamos por paremetro el id curso mara mostrar la lista de calificaciones
            $this->crud->curso = $curso_id_recibido;

            //$this->crud->addButtonFromView('line', 'alumnos', 'ver_alumnos', 'end');
            //$this->crud->addButton('line', 'calAsis', 'href', 'href="www.google.com"', 'beginning');
        }

        if(Auth::user()->hasRole('SUBDIRECTOR ACADÉMICO')){
            // Actions Buttons
            $this->crud->addButtonFromModelFunction('line', 'carga_horaria', 'establecerCargaHoraria', 'end'); 
            $this->crud->addButtonFromModelFunction('line', 'administrativo', 'establecerAdministrativo', 'end'); 
            // Eliminamos boton de editar
            $this->crud->removeButton('update');
        }

	// Rol de administrativo                                                                                                                                        
        if(Auth::user()->hasRole('ADMINISTRATIVO') and Auth::user()->idescuelanormal == 1 ){
            $this->crud->removeColumn('plan_id');

            $this->crud->addColumn([
                'name'  => 'licenciatura',
                'type'  => 'text',
                'label' => 'Licenciatura',
            ]);
        }

        // Rol de administrativo
        if(Auth::user()->hasRole('ADMINISTRATIVO')){
            // Permisos de ver y hacer cosas
            $this->crud->denyAccess(['create', 'update', 'reorder', 'delete']);
            // Mostramos lo grupos que tienen asignado el idadministrativo (id user), logueado
            $this->crud->addClause('where', 'idadministrativo', '=', Auth::user()->id);
            // Restringimos que se muestren solo los grupos que no esten cursados (diferente  a 1 el campo cursado)
            $this->crud->addClause('where', 'cursado', '!=', 1);
            
            $this->crud->addButtonFromModelFunction('line', 'alumnos', 'verAlumnos', 'end');
            $this->crud->addButtonFromModelFunction('line', 'nuevo_alumno', 'agregarAlumnoExistenteDB', 'end');
            
            $this->crud->setListView('listado_grupo_administrativo');
            
        }

        // Rol de director
        if(Auth::user()->hasRole('DIRECTOR')){
            $this->crud->denyAccess([ 'create', 'update', 'reorder', 'delete']);
            $this->crud->addButtonFromModelFunction('line', 'alumnos', 'verAlumnos', 'end');
        }
        // $this->crud->denyAccess(['list', 'create', 'update', 'reorder', 'delete']);

        // ------ CRUD REORDER
        // $this->crud->enableReorder('label_name', MAX_TREE_LEVEL);
        // NOTE: you also need to do allow access to the right users: $this->crud->allowAccess('reorder');

        // ------ CRUD DETAILS ROW
        // $this->crud->enableDetailsRow();
        // NOTE: you also need to do allow access to the right users: $this->crud->allowAccess('details_row');
        // NOTE: you also need to do overwrite the showDetailsRow($id) method in your EntityCrudController to show whatever you'd like in the details row OR overwrite the views/backpack/crud/details_row.blade.php

        // ------ REVISIONS
        // You also need to use \Venturecraft\Revisionable\RevisionableTrait;
        // Please check out: https://laravel-backpack.readme.io/docs/crud#revisions
        // $this->crud->allowAccess('revisions');

        // ------ AJAX TABLE VIEW
        // Please note the drawbacks of this though:
        // - 1-n and n-n columns are not searchable
        // - date and datetime columns won't be sortable anymore
        // $this->crud->enableAjaxTable();

        // ------ DATATABLE EXPORT BUTTONS
        // Show export to PDF, CSV, XLS and Print buttons on the table view.
        // Does not work well with AJAX datatables.
        // $this->crud->enableExportButtons();

        // ------ ADVANCED QUERIES
        // $this->crud->addClause('active');
        // $this->crud->addClause('type', 'car');
        // $this->crud->addClause('where', 'name', '==', 'car');
        // $this->crud->addClause('whereName', 'car');
        // $this->crud->addClause('whereHas', 'posts', function($query) {
        //     $query->activePosts();
        // });
        // $this->crud->addClause('withoutGlobalScopes');
        // $this->crud->addClause('withoutGlobalScope', VisibleScope::class);
        // $this->crud->with(); // eager load relationships
        // $this->crud->orderBy();
        // $this->crud->groupBy();
        // $this->crud->limit();

        // Mostrar usuarios de la normal del usuario logueado
        $this->crud->addClause('where', 'idescuelanormal', '=', (string)Auth::user()->idescuelanormal);

        $this->crud->orderBy('semestre');
        $this->crud->orderBy('grupo');
        $this->crud->orderBy('plan_id');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $escuela = Escuelanormal::find( (int)Auth::user()->idescuelanormal ); // Get escuelanormal
        $plan_estudio = Planestudio::where('name', '=', $request->plan_id)->first(); // Get plan d eestudios seleccionado
        
	// pARA NORMALES EN GENERAL                                                                                                                                     
        $licenciatura = 'PRIMARIA';
        if($escuela->nombre == "NORMAL PREESCOLAR")
            $licenciatura = 'PREESCOLAR';

        $cursos = null;
        // Para CAM                                                                                                                                                     
        if($escuela->id == 1){
            $licenciatura = $request->licenciatura;

            // Obtenemos cursos dependiendo del numero de semestre, a la licencatura y plan de estudios                                                                 
            $cursos = Curso::where('semestre', $request->semestre) // Semestre seleccionado por el usuario                                                              
                ->where('licenciatura', '=', $licenciatura) // licenciatura a la qe pertenece la normal logueada
		->where('plan_id', 1) // Los planes 2012 (para no mezclar los dos primeros semestres en primaria)                                                         
                ->get(); // Obtenemos los cursos que son del semestre seleccionado                                                                                      
        }else{ //para las otras normales                                                                                                                                

            // Obtenemos cursos dependiendo del numero de semestre, a la licencatura y plan de estudios                                                                 
            $cursos = Curso::where('semestre', $request->semestre) // Semestre seleccionado por el usuario                                                              
                ->where('licenciatura', '=', $licenciatura) // licenciatura a la qe pertenece la normal logueada                                                        
                ->where('plan_id', '=', $request->plan_id) // el plan de estudios en el que el grupo sera insertado                                                     
                ->get(); // Obtenemos los cursos que son del semestre seleccionado                                                                                      
        }	
        
        // set descripcion
        $request->request->set('descripcion', (string)$request->semestre."".(string)$request->grupo);
        
        // set idcicloescolar (el actual)
        $now = new \DateTime();
        $current_date = $now->format('Y-m-d');
        
        // Obtenemos ciclo escolar acvtivo (1)
        $ciclo_escolar = Cicloescolar::where('vigente', '=', 1)->first();
        
        if($ciclo_escolar != null)
            $request->request->set('idcicloescolar', $ciclo_escolar->id);
        else
            $request->request->set('idcicloescolar', 0);
        
                
        $redirect_location = parent::storeCrud($request);

        // your additional operations after save here

        // Obtenemos el id del grupo registrado (el que se esta creando en este momento)
        $id = $this->crud->entry->id_Grupos;
        // Insertamos en la table muchos a muchos curso_grupos
        // el grupo y sus cursos
        // Asignacion del grupo a cursos
        foreach($cursos as $curso){
            DB::table('curso_grupos')->insert(
                ['curso_id' => $curso->id_curso, 'grupo_id' => $id]
            );
        }
        
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    // Funcion que extrae ids especificos de cada grupo para mostrarlos en los grupos del docente seleccionado con su curso seleccionado
    public function getIdGrupos($cargas){
    	$array_final = [];

        foreach($cargas as $carga){
            // Si no esta el id del grupo actual en el array de grupos se agrega
            if(! in_array($carga->grupo_id, $array_final)){
                array_push($array_final, $carga->grupo_id); // Agregamos al array
            }
        }
        
        return $array_final;
    }

    // Get alumno by curp
    public function getAlumnoByCurp(Request $request){
        // Si es una peticion AJAX
        if( $request->ajax() ){
        	// Buscamos alumno por curp
            $alumnos = Alumno::where('curp', '=', $request->curp) // Donde la curp sea igual
                ->where('idescuelanormal', '=', Auth::user()->idescuelanormal) // donde pertenezca a la escuela del usuario logueado
                ->where('idgrupo', '=', null) // Donde no tenga asignado un grupo
                ->first();

            /*$i = 0;
            foreach($alumnos as $alumno){
                $i++;
            }*/
            // Si existe
            if($alumnos){
                return "{\"result\":\"ok\", \"nombre\": \"".$alumnos->nombre."\", \"app\": \"".$alumnos->app."\", \"apm\": \"".$alumnos->apm."\"}";
            }else{//sino
                return "{\"result\":\"error\"}";                
            }        		
        }
    }

    // Get alumno by curp
    public function actualizarAlumnoGrupo(Request $request){
        // Si es una peticion AJAX
        if( $request->ajax() ){
        	// Buscamos alumno por curp
            $alumno = Alumno::where('curp', '=', $request->curp) // Donde la curp sea igual
                ->where('idescuelanormal', '=', Auth::user()->idescuelanormal) // donde pertenezca a la escuela del usuario logueado
                ->first();

            $grupo = Grupo::find((int) $request->grupoid);

           /* $i = 0;
            foreach($alumno as $alumn){
                $i++;
            }*/

            // Si existe
            if($alumno){
                $alumno->idgrupo = $request->grupoid;
                if($alumno->save())
                    return "{\"result\":\"ok\", \"grupo\": \"".$grupo->descripcion."\"}";                
                else
                    return "{\"result\":\"error\"}";                
            }else{//sino
                return "{\"result\":\"error\"}";                
            }        		
        }
    }
}
