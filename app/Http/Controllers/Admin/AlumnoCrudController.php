<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Auth;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\AlumnoRequest as StoreRequest;
use App\Http\Requests\AlumnoRequest as UpdateRequest;
use Illuminate\Support\Facades\Input;
use App\Models\CodigoPostal;
use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\Cicloescolar;
use Illuminate\Http\Request;

class AlumnoCrudController extends CrudController
{
    
    public function setup()
    {

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Alumno');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/alumno');
        $this->crud->setEntityNameStrings('alumno', 'alumnos');

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->enableDetailsRow();
        $this->crud->allowAccess('details_row');
        //$this->crud->setFromDb();

        // Columns.
        $this->crud->setColumns([
            [
                'name'  => 'nombre',
                'label' => 'Nombre',
                'type'  => 'text',
            ],
            [
                'name'  => 'app',
                'label' => 'A. Paterno',
                'type'  => 'text',
            ],
            [
                'name'  => 'apm',
                'label' => 'A. Materno',
                'type'  => 'text',
            ],
            [
                'name'  => 'curp',
                'label' => 'CURP',
                'type'  => 'text',
            ],
        ]);
        
	    
            
        
        
        /*$campos = [
            ['nombre', 'Nombre del alumno'],
            ['app', 'Apellido Paterno'],
            ['apm', 'Apellido Materno'],
            ['domicilio', 'Domicilio'],
            ['localidad', 'Localidad'],
            ['municipio', 'Municipio'],
            ['estado', 'Estado'],
            
        ];

        foreach($campos as $campo){
            $this->crud->addField([
                'name' => $campo[0],
                'label' => $campo[1],
                'attributes' => [
                    'onBlur'  => 'mayusculas(this)',
                ],
            ]);
        }*/

        // Si se va a dar de alta un alumno que ya se registro pero no esta asignado a un grupo
        
        $this->crud->addField([
            'name' => 'nombre', // The db column name
            'label' => "Nombre del alumno", // Table column heading
            'attributes' => [
                'onBlur'  => 'mayusculas(this)',
            ],
        ]);
        
        $this->crud->addField([
            'name' => 'app', // The db column name
            'label' => "Apellido Paterno", // Table column heading
            'attributes' => [
                'onBlur'  => 'mayusculas(this)',
            ],
        ]);
        
        $this->crud->addField([
            'name' => 'apm', // The db column name
            'label' => "Apellido Materno", // Table column heading
            'attributes' => [
                'onBlur'  => 'mayusculas(this)',
            ],
        ]);
        
        $this->crud->addField([
            'name' => 'curp', // The db column name
            'label' => "CURP", // Table column heading
            'type' => 'text',
            'attributes' => [
                'onBlur'  => 'mayusculas(this)',
                'id'      => 'curp',
                'oninput' => "validarInput(this)",
            ],
        ]);
        
        $this->crud->addField([
            'name' => 'matricula', // The db column name
            'label' => "Matricula",// Table column heading
            'type' => 'text',
            'attributes' => [
                'onBlur'  => 'mayusculas(this)',
            ],
        ]);

        // Foto
        /*$this->crud->addField([
            'name' => 'correo', // The db column name
            'label' => "Correo Electrónico", // Table column heading
        ]);

        $this->crud->addField([
            'name' => 'cp',
            'label' => "Codigo Postal", // Table column heading
            'attributes' => [
                'onBlur'  => 'mayusculas(this)',
                'id'      => 'cp',
            ],
        ]);*/
        
        
        $this->crud->addField([
            'name' => 'correo', // The db column name
            'label' => "Correo Electrónico", // Table column heading
            
        ]);

	$this->crud->addField([
            'name' => 'cp',
            'label' => "Codigo Postal (Para llenar campos de estado y municipio)", // Table column heading                                                              
            'attributes' => [
                'onBlur'  => 'mayusculas(this)',
                'id'      => 'cp',
            ],
        ], 'create');

        $this->crud->addField([
            'name' => 'cp',
            'label' => "Codigo Postal (Para llenar campos de estado y municipio)", // Table column heading                                                              
            'attributes' => [
                'onBlur'  => 'mayusculas(this)',
                'readonly' => 'readlonly',
            ],
        ],'update');
        /*
        $this->crud->addField([
            'name' => 'cp',
            'label' => "Codigo Postal (Para llenar campos de estado y municipio)", // Table column heading
            'attributes' => [
                'onBlur'  => 'mayusculas(this)',
                'id'      => 'cp',
            ],
        ]);*/
        
        $this->crud->addField([
            'name' => 'estado', // The db column name
            'label' => "Estado", // Table column heading
            'attributes' => [
                'readonly'  => 'readonly',
                'id'      => 'estado',
                'onBlur'  => 'mayusculas(this)',
            ],
        ]);
        
        $this->crud->addField([
            'name' => 'municipio', // The db column name
            'label' => "Municipio", // Table column heading
            'attributes' => [
                'readonly'  => 'readonly',
                'id'      => 'municipio',
                'onBlur'  => 'mayusculas(this)',
            ],
        ]);

	$this->crud->addField([
            'name' => 'localidad', // The db column name                                                                                                                
            'label' => "Localidad", // Table column heading                                                                                                             
            'type' => 'select2_from_array',
            #'default' => '',                                                                                                                                           
            'options' => [],
            'attributes' => [
                'id' => 'localidad',
                //'onBlur'  => 'mayusculas(this)',                                                                                                                      
            ],
        ], 'create');

        $this->crud->addField([
            'name' => 'localidad', // The db column name                                                                                                                
            'label' => "Localidad", // Table column heading                                                                                                             
            'type' => 'text',
            #'default' => '',                                                                                                                                           
            /*'options' =>  [                                                                                                                                           
                '' => '',                                                                                                                                               
                ],*/
            'attributes' => [
                'id' => 'localidad',
                'readonly' => 'readonly',
                //'onBlur'  => 'mayusculas(this)',                                                                                                                      
            ],
        ], 'update');

        /*
        $this->crud->addField([
            'name' => 'localidad', // The db column name
            'label' => "Localidad", // Table column heading
            'type' => 'select_from_array',
            #'default' => '',
            'options' => [],
            'attributes' => [
                'id' => 'localidad',
                //'onBlur'  => 'mayusculas(this)',
            ],
        ]);*/

        $this->crud->addField([
            'name' => 'domicilio',
            'label' => 'Domicilio actual (calle y número)',
            'type' => 'textarea',
            'attributes' => [
                'onBlur'  => 'mayusculas(this)',
            ],
        ]);
        
        $this->crud->addField([
            'name'  => 'telefono', // The db column name
            'label' => "Teléfono (sin espacios ejemplo: 2461256987)", // Table column heading
            'type'  => 'number',
            'attributes' => [
                'size'  => '10',
            ],
        ]);
        
        $this->crud->addField([
         'name'  => 'celular', // The db column name
         'label' => "Tel. Celular (sin espacios ejemplo: 2461256987)", // Table column heading
         'type'  => 'number',
         'attributes' => [
                'size'  => '10',
            ],
        ]);

        $this->crud->addField([
         'name'  => 'estado_civil', // The db column name
         'label' => "Estado Civil", // Table column heading
         'type'  => 'select2_from_array',
         'options' => [
             'SOLTERO(A)' => 'Soltero(a)',
             'CASADO' => 'Casado',
             'UNIÓN LIBRE' => 'Unión Libre',
             'VIUDO(A)' => 'Viudo(a)',
             'OTRO' => 'Otro',
         ],
         'allows_null' => false,
         'default' => '',
        ]);        

        $this->crud->addField([
            'name' => 'sexo',
            'label' => 'Sexo',
            'type' => 'radio',
            'options' => [
                'MASCULINO' => "MASCULINO",
                'FEMENINO' => "FEMENINO",
            ],
            'default' => 'MASCULINO',
        ]);

        $this->crud->addField([
         'name'  => 'servicio_medico', // The db column name
         'label' => "Servicio Médico", // Table column heading
         'type'  => 'select2_from_array',
         'options' => [
             'IMSS' => 'IMSS',
             'ISSSTE' => 'ISSSTE',
             'SESA' => 'SESA',
             'SEDENA' => 'SEDENA',
         ],
         'allows_null' => false,
         'default' => '',
        ]);

        $this->crud->addField([
            'name'  => 'contacto_accidente', // The db column name
            'label' => "En caso de accidente avisar a:", // Table column heading
            'type'  => 'text',
            'attributes' => [
                'onBlur'  => 'mayusculas(this)',
            ],
        ]);

        $this->crud->addField([
            'name'  => 'tel_accidente', // The db column name
            'label' => "Teléfono de contaco (sin espacios ejemplo: 2461256987)", // Table column heading
            'type'  => 'number',
        ]);
        
        $this->crud->addField([
            'name'  => 'direccion_accidente', // The db column name
            'label' => "Domicilio de contaco", // Table column heading
            'type'  => 'textarea',
            'attributes' => [
                'onBlur'  => 'mayusculas(this)',
            ],
        ]);
        
        $this->crud->addField([
            'name' => 'capacidadDiferente',
            'label' => 'Capacidad Diferente',
            'type' => 'radio',
            'options' => [
                'NO' => "No",
                'SI' => "Si",
            ],
            'default' => 'NO',
        ]);
        
        // Id escuela normal del usuario logueado
        $this->crud->addField([   // Hidden
            'name' => 'idescuelanormal',
            'type' => 'hidden',
            'value' => Auth::user()->idescuelanormal,
        ]);
        
        // Status del alumno (por defecto alta)
        $this->crud->addField(
            [ // select_from_array
                'name' => 'status',
                'label' => "Estado",
                'type' => 'select2_from_array',
                'options' => ['1' => 'Alta', '2' => 'Baja Temporal', '3' => 'Baja Permanente'], // Opciones
                'allows_null' => false,
                'default' => '1',
                // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;
            ]
        );

        
        $this->crud->enableExportButtons();
        // Mostrar alumnos de la normal del usuario logueado
        $this->crud->addClause('where', 'idescuelanormal', '=', (string)Auth::user()->idescuelanormal); 
        // Que no sean egresados
        if(\Request::input('certificado') and Auth::user()->hasRole('ADMINISTRATIVO')){
            $this->crud->addClause('where', 'egresado', '=', 1);
            $this->crud->disableDetailsRow();
            $this->crud->denyAccess('details_row');

            $this->crud->certificacion = true; // Decimos que es para mostrar los certificados
            
            $this->crud->setListView('listado_alumnos_administrativo');
        }
	
	// Para alumnos preinscritos (sin asignacion de grupos)                                                                                                         
        else if(\Request::input('preinscrito') and Auth::user()->hasRole('ADMINISTRATIVO')){
            $this->crud->addClause('where', 'egresado', 0); // Que no sean egresados                                                                                    
            $this->crud->addClause('where', 'idgrupo', NULL);       // Que no tenga un grupo asignado                                                                   

            $this->crud->disableDetailsRow();
            $this->crud->denyAccess('details_row');
            // $this->crud->addClause('where', 'egresado', 0);                                                                                                          
        }	

	else{
            $this->crud->addColumn(
                [
                    // Mostramos el nombre del curso que tiene asignado
                    'label'     => 'Grupo asignado', // Table column heading
                    'type'      => 'select',
                    'name'      => 'idgrupo', // the method that defines the relationship in your Model
                    'entity'    => 'grupos', // the method that defines the relationship in your Model
                    'attribute' => 'descripcion', // foreign key attribute that is shown to user
                    'model'     => 'App\Models\Grupo', // foreign key model    
                ]
                
            );

            $this->crud->addColumn(
                [
            'label' => 'Status',
            'name'  => 'status',
            'type'  => 'radio',
            'options' => [
                1 => "Alta",
                2 => "Baja Temporal",
                3 => "Baja Permanente",
            ],
	    ]
            );
                
            $this->crud->addClause('where', 'egresado', '=', 0);
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

        // Acciones para director
        if(Auth::user()->hasRole('DIRECTOR')){
            $this->crud->denyAccess([ 'create', 'update', 'reorder', 'delete']);

            $this->crud->disableDetailsRow();
            $this->crud->denyAccess('details_row');
            if(\Request::input('grupo_id')){
                $this->crud->addClause('where', 'idgrupo', '=', \Request::input('grupo_id'));
            }

            // Pasamos objeto de grupo a la vista por medio de crud
            $this->crud->gruppo = Grupo::find(\Request::input('grupo_id'));
            $this->crud->setListView('listado_alumnos_administrativo');

        }
        
        // Acciones para administrativo
        if(Auth::user()->hasRole('ADMINISTRATIVO')){
            /**** CERTIFICACION ****/
            /*if(\Request::input('certificado')){
                $this->crud->addClause('where', 'egresado', '=', 1);
                }*/

            // Quitamos link que aparece al inicio de crear un alumno para evitar errores en las vistas
            //$this->crud->denyAccess('list');
            $this->crud->removeButton('cancel');
            
            //$this->crud->removeAllButtons();
            // $this->crud->addButtonFromModelFunction('line', 'formato_inscripcion', 'formatoInscripcion', 'end'); 
            // $this->crud->removeButton('create');	
            // Querys
            //
            $this->crud->setListView('listado_alumnos_administrativo');
            $this->crud->setCreateView('create_alumno_administrativo'); //Vista de crear
	    $this->crud->setEditView('edit_alumno_administrativo'); //Vista de editar 
            $this->crud->addClause('where', 'idgrupo', '=', \Request::input('grupo_id'));
            
            // Pasamos parametro grupoidd a vista para seguir obteniendo el id del grupo
            $this->crud->grupoidd = \Request::input('grupo_id');

            // Pasamos objeto de grupo a la vista por medio de crud
            $this->crud->gruppo = Grupo::find(\Request::input('grupo_id'));

            
            // Agregamos el field del id del grupo cuando es pasado por post
            if(\Request::input('grupo_id')){
                $this->crud->addField([
                    'type' => 'hidden',
                    'name' => 'idgrupo',
                    'value' => \Request::input('grupo_id'),
                ]);
            }

            else{
		$descripcion = 'descripcion'; // Para las demas normales que no son el cam                                                                              

                if(Auth::user()->idescuelanormal == 1)// si es cam                                                                                                      
                    $descripcion = 'full_descripcion';

                $this->crud->addField([
                    'label'       => "Grupo",
                    'type'        => 'select2',
                    'name'        => 'idgrupo',
                    'entity'      => 'grupos',
                    'attribute'   => $descripcion,
                    //'attribute' => 'descripcion',                                                                                                                     
                    'model' => "App\Models\Grupo",
                    //'data_source' => url('getgruposadministrativo'),                                                                                                  
                    //'placeholder' => "Seleccione grupo",                                                                                                              

                    'callback' => function() {

                        return Grupo::where('idadministrativo', '=', Auth::user()->id)->paginate(100);
                        // Extraemos cursos de CAM                                                                                                                      

                    }
                ]);
		/*
                $this->crud->addField([
                    'label' => "Grupo",
                    'type' => 'select2_from_ajax',
                    'name' => 'idgrupo',
                    'entity' => 'grupos',
                    'attribute' => 'descripcion',
                    'model' => "App\Models\Grupo",
                    'data_source' => url('getgruposadministrativo'),
                    'placeholder' => "Seleccione grupo",
                    'minimum_input_length' => 0,
                ]);*/
            }

            
            /* Para periodo de cambio de grupo */

            // Sacamos periodo actual del grupo
            $current_grupo = Grupo::where('id_Grupos', \Request::input('grupo_id'))->first();
            $current_grupo_ciclo_escolar = Cicloescolar::find( (int) $current_grupo['idcicloescolar'] );
                        
            // Buscamos periodo posterior del ciclo escolar (si es que hay)
            $siguiente_ciclo = Cicloescolar::where( 'fecha_inicio' , '>', (string) $current_grupo_ciclo_escolar['fecha_inicio'] )
                ->orderBy('fecha_inicio')
                ->first();

            // Si existe la query (existe un ciclo escolar mayor)
            if($siguiente_ciclo){
                // Obtenemos los grupos disponibles del siguiente grupo (x ejempo si el actual es 5b sacamos todos los de sexto)
                // Para llenar el select de migrar grupo destino
                $grupos = Grupo::where('semestre', '=', (int)$this->crud->gruppo['semestre'] + 1)
                    ->where('idcicloescolar', '=', $siguiente_ciclo['id'])
                    ->where('idescuelanormal', '=', Auth::user()->idescuelanormal)
                    //->where('grupo', '=', $current_grupo['grupo']) // Que sea la misma letra de grupo // Pendiente checar
                    ->get();
                
                $i = 0;
                foreach($grupos as $group){ $i++; }
                
                if($i > 0){
                    $this->crud->existesiguienteciclo = true;
                    $this->crud->groups = $grupos;
                }else{
                    $this->crud->existesiguienteciclo = false;
                }
            }else
                $this->crud->existesiguienteciclo = false;

            
        }

        /*
        // Acciones para docente
        if(Auth::user()->hasRole('DOCENTE')){
            // Permisos de ver y hacer cosas
            $this->crud->denyAccess(['create', 'update', 'reorder', 'delete']);
            // Actions Buttons
            $this->crud->addButtonFromModelFunction('line', 'calificaciones_asistencia', 'addCalificacion', 'beginning'); 
            
            // Querys
            // Get id grupo
            //$id_grupo = Input::get('idgrupo');
            // Mostramos los alumnos pertenecientes a este grupo
            $this->crud->addClause('where', 'idgrupo', '=', Input::get('idgrupo'));
            \Alert::success(trans(Input::get('idgrupo')))->flash();
            //$this->crud->addClause('whereHas', 'curso', function($query){
            //    $query->where('id_curso', Auth::user()->id_curso);
            //});
        }*/
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
        // $this->crud->addClause('where', 'status', '!=', 3);
        // $this->crud->addClause('whereName', 'car');
        // $this->crud->addClause('whereHas', 'posts', function($query) {
        //     $query->activePosts();
        // });
        // $this->crud->addClause('withoutGlobalScopes');
        // $this->crud->addClause('withoutGlobalScope', VisibleScope::class);
        // $this->crud->with(); // eager load relationships
        // $this->crud->orderBy();
        $this->crud->orderBy('app');
        $this->crud->orderBy('apm');
        $this->crud->orderBy('nombre');
        // $this->crud->groupBy();
        // $this->crud->limit();
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry

        // Si es administrativo volvemos a la lista del grupo donde
        // se dio de alta el alumno
        if(Auth::user()->hasRole('ADMINISTRATIVO')){
            /*$grupo = Alumno::select('idgrupo')
              ->orderby('created_at','DESC')->first();
            
            return \Redirect::to('/admin/alumno?grupo_id='.
            (string)$grupo->idgrupo);*/
            return \Redirect::to('/admin/alumno/create');
        }else
            return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry

        // Si es administrativo volvemos a la lista del grupo donde
        // se dio de alta el alumno
        if(Auth::user()->hasRole('ADMINISTRATIVO')){
            /*$grupo = Alumno::select('idgrupo')
                ->orderby('created_at','DESC')->first();
            return \Redirect::to('/admin/alumno?grupo_id='.
            (string)$grupo->idgrupo);*/
	    return \Redirect::to('/admin/alumno?grupo_id='.
            (string)$this->crud->entry->idgrupo);
        }else
            return $redirect_location;
    }


    // Funcion que busca el estado municipio y colonias a traves del codigo postal
    public function getDomicilio(Request $request){
        if($request->ajax()){
        	// Obtenemos el curso elegido
        	$cp = CodigoPostal::where('CodigoPostal', '=', $request->cp );

            $k = 0; foreach($cp as $c){ $k++; }
            // Si si se encontro algun registro con el cp recibido
            if($k > 0){
                $resultado = ''; // Cadena que contendra los option select
                
                // Extraemos colonias
                $colonias = explode(";", $cp->Colonia);
                
                // Recorremos las colonias para generar los option select
                foreach ($colonias as $colonia)
                    // Agregamos cada option con su respectivo resultado
                    $resultado .=
                    '<option value=\''.$colonia.'\'>'.$colonia.'</option>';

                // retornamos valores
                return "{\"result\": \"ok\", \"estado\": \"".
                    $cp->Estado.
                    "\", \"municipio\": \"".
                    $cp->Municipio."\", \"colonias\": \"".$resultado."\" }";
            }else{
                // retornamos valores
                return "{\"result\": \"noexistecp\"}";
            }
        }
    }
    

    public function showDetailsRow($id){
       
        $this->crud->hasAccessOrFail('details_row');
        
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;

        // Proceso para sacar el id del grupo del alumno actual
        $alumno = Alumno::find($id);
        // Obtenemos datos
        $this->data['data_migration'] = $this->getNextGroups($alumno->idgrupo);
        
// load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('crud::details_alumno_administrativo_row', $this->data);
        
    }

    // Update status alumno (request ajax)
    public function updateStatusAlumno(Request $request){
        if($request->ajax()){
            $alumno = Alumno::find($request->idalumno);
            
            $alumno->status = $request->status;
            
            if($alumno->save())
                return "{\"result\": \"ok\"}";
            else
                return "{\"result\": \"error\"}";
            
        }
    }    

    // Update whole group
    public function updateWholeGroup(Request $request){
        if($request->ajax()){
            // Actualizamos alumnos
            $alumnos = Alumno::where('idgrupo', $request->oldgrupo)
                ->where('status', 1)// Que esten dados de alta
                ->update(['idgrupo' => $request->newgrupo]);
            // Establecemos que el grupo del que se migro esta cursado
            $grupo = Grupo::find($request->oldgrupo);
            $grupo->cursado = 1; // Ponemos que el grupo ya fue cursado
            
            if($alumnos and $grupo->save()) return "{\"result\": \"ok\"}";
            else return "{\"result\": \"error\"}";
        }
    }

    // Update individual alumno group
    public function updateGroup(Request $request){
        if($request->ajax()){
            $alumno = Alumno::find($request->alumno);

            // Vemos si es el ultimo alumno por migrar
            // es decir si el viejo grupo ya va a quedar cursado
            $cantidad_alumnos_old_grupo = Alumno::where('idgrupo', $alumno->idgrupo) // Pertendezca la grupo actual
                ->where('status', 1) // este dado de alta
                ->get();

            $contt = 0;
            foreach($cantidad_alumnos_old_grupo as $r){
                $contt++;
            }
            // Si es el ultimo alumno a migrar, establecemos al viejo grupo como cursado
            if($contt == 1){
                $grupo = Grupo::find($alumno->idgrupo);
                $grupo->cursado = 1; // establecemos que ya se curso el grupo
                $grupo->save(); // Guardamos el status del grupo
            }
            
            $alumno->idgrupo = $request->grupo;
            if($alumno->save())
                if($contt == 1)
                    return "{\"result\": \"ok\", \"cursado\": \"si\"}";
                else
                    return "{\"result\": \"ok\", \"cursado\": \"no\"}";
            else
                return "{\"result\": \"error\"}";
        }
    }


    // Obtenemos grupos disponibles para migracion de semestre
    private function getNextGroups($idGrupo){
        $data = [];
        // Sacamos periodo actual del grupo
        
        $current_grupo = Grupo::where('id_Grupos', $idGrupo)
            ->first(); //\Request::input('grupo_id'))->first();
        $current_grupo_ciclo_escolar = Cicloescolar::find( (int) $current_grupo->idcicloescolar );
                
        // Buscamos periodo posterior del ciclo escolar (si es que hay)
        $siguiente_ciclo = Cicloescolar::where( 'fecha_inicio' ,'>',
        (string) $current_grupo_ciclo_escolar['fecha_inicio'] )
            ->orderBy('fecha_inicio')
            ->first();
        
        // Si existe la query (existe un ciclo escolar mayor)/ Buscamos periodo posterior del ciclo escolar (si es que hay)
        $siguiente_ciclo = Cicloescolar::where( 'fecha_inicio' ,'>',
        (string) $current_grupo_ciclo_escolar['fecha_inicio'] )
            ->first();

        // Si existe la query (existe un ciclo escolar mayor)
        if($siguiente_ciclo){
            // Obtenemos los grupos disponibles del siguiente grupo (x ejempo si el actual es 5b sacamos todos los de sexto)
            // Para llenar el select de migrar grupo destino
            $grupos = Grupo::where('semestre', '=', (int)$current_grupo['semestre'] + 1)
                ->where('idcicloescolar', '=', $siguiente_ciclo['id'])
                ->where('idescuelanormal', '=', Auth::user()->idescuelanormal)
                //->where('grupo', '=', $current_grupo['grupo']) // Que sea la misma letra de grupo // Pendiente checar
                ->get();

            $i = 0;
            foreach($grupos as $group){ $i++; }

            if($i > 0){
                $data['existesiguienteciclo'] = true;
                $data['groups'] = $grupos;
            }else
                $data['existesiguienteciclo'] = false;
        }else

        if($siguiente_ciclo){
            // Obtenemos los grupos disponibles del siguiente grupo (x ejempo si el actual es 5b sacamos todos los de sexto)
            // Para llenar el select de migrar grupo destino
            $grupos = Grupo::where('semestre', '=', (int)$current_grupo['semestre'] + 1)
                ->where('idcicloescolar', '=', $siguiente_ciclo['id'])
                ->where('idescuelanormal', '=', Auth::user()->idescuelanormal)
                //->where('grupo', '=', $current_grupo['grupo']) // Que sea la misma letra de grupo // Pendiente checar
                ->get();
            
            $i = 0;
            foreach($grupos as $group){ $i++; }
            
            if($i > 0){
                $data['existesiguienteciclo'] = true;
                $data['groups'] = $grupos;
            }else
                $data['existesiguienteciclo'] = false;
        }else
            $data['existesiguienteciclo'] = false;
        
        return $data;
    }
}


/*

  @if($crud->existesiguienteciclo)
    @foreach($crud->groups as $group)
    <option value="{{$group->id_Grupos}}">{{$group->descripcion}}</option>
     
    @endforeach
    @endif



    @if($crud->existesiguienteciclo)
	 @endif
 */
