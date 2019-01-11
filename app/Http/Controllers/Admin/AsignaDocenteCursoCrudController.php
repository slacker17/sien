<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Auth;
use App\Models\Curso;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\AsignaDocenteCursoRequest as StoreRequest;
use App\Http\Requests\AsignaDocenteCursoRequest as UpdateRequest;
//use App\User;

class AsignaDocenteCursoCrudController extends CrudController
{
    public function setup()
    {

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\AsignaDocenteCurso');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/asignadocentecurso');
        $this->crud->setEntityNameStrings('asignadocentecurso', 'asignar docentes a cursos');
        $this->crud->enableAjaxTable();
        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */

        //$this->crud->setFromDb();

        $this->crud->setListView('listado_asigna_docente_curso');// Vista personalizada para mostrar docentes
        $this->crud->enableDetailsRow();
        $this->crud->allowAccess('details_row');
        
        
        // Many to Many curso grupos
        /*$this->crud->addField([       // Select2Multiple = n-n relationship (with pivot table)
            'label' => "Cursos que impartirá el docente",
            'type' => 'select2_multiple',
            'name' => 'cursos', // the method that defines the relationship in your Model
            'entity' => 'cursos', // the method that defines the relationship in your Model
            'attribute' => 'full_descripcionCurso', // foreign key attribute that is shown to user
            'model' => "App\Models\Curso", // foreign key model
            'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
            //'select_all' => true, // show Select All and Clear buttons?
            ]);*/

        
	$this->crud->addField([
            'label' => "Cursos que impartirá el docente", // Table column heading                                                                                       
            'type' => "select2_multiple",
            'name' => 'cursos', // the column that contains the ID of that connected entity                                                                             
            'entity' => 'cursos', // the method that defines the relationship in your Model                                                                             
            'attribute' => "full_descripcionCurso", // foreign key attribute that is shown to user                                                                      
            'model' => "App\Models\Curso", // foreign key model                                                                                                         
            //'data_source' => url('getcursosnormal'), // url to controller search function (with /{id} should return model)                                              
            'pivot' => true, // on create&update, do you need to add/delete pivot table entries?                                                                        
	    'callback' => function() {
                if(Auth::user()->escuelasnormales->licenciatura == null){ // Si es Cam                                                                                  
                    // Extraemos cursos de CAM                                                                                                                          
                    return Curso::where('licenciatura', 'SECUNDARIA CON ESPECIALIDAD EN ESPAÑOL')
                    ->orWhere('licenciatura', 'SECUNDARIA CON ESPECIALIDAD EN MATEMÁTICAS')
                    ->orWhere('licenciatura', 'PRIMARIA')
                    ->where('plan_id', 1) // Plan 2012                                                                                                                  
                    ->orderBy('licenciatura') // Ordenar primero por licenciatura                                                                                       
                    ->orderBy('semestre') // Ordenar por semestre                                                                                                       
                    ->orderBy('created_at') // Y por el orden en que se fue creando                                                                                     
                    ->get();
                }
                else{
                    if(Auth::user()->escuelasnormales->licenciatura == 'PREESCOLAR')
                        //return DB::table('cursos')->select('descripcionCurso', 'email as user_email')->get();                                                         
                        return Curso::where('licenciatura', '=', 'PREESCOLAR')->get();
                    else if(Auth::user()->escuelasnormales->licenciatura == 'PRIMARIA')
                        //return DB::table('cursos')->select('descripcionCurso', ' as user_email')->get();                                                              
			return Curso::where('licenciatura', '=', 'PRIMARIA')->get();
                }
                //return Role::where('name', 'Superadmin')->get();                                                                                                      
            }            
        ]);        

        /*$this->crud->addField([
            'label' => "Cursos que impartirá el docente", // Table column heading
            'type' => "select2_multiple",
            'name' => 'cursos', // the column that contains the ID of that connected entity
            'entity' => 'cursos', // the method that defines the relationship in your Model
            'attribute' => "full_descripcionCurso", // foreign key attribute that is shown to user
            'model' => "App\Models\Curso", // foreign key model
            'data_source' => url('getcursosnormal'), // url to controller search function (with /{id} should return model)
            'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
            'callback' => function() {
                if(Auth::user()->escuelasnormales->licenciatura == 'PREESCOLAR')
                    //return DB::table('cursos')->select('descripcionCurso', 'email as user_email')->get();
                    return Curso::where('licenciatura', '=', 'PREESCOLAR')->get();
                else if(Auth::user()->escuelasnormales->licenciatura == 'PRIMARIA')
                    //return DB::table('cursos')->select('descripcionCurso', ' as user_email')->get();
                    return Curso::where('licenciatura', '=', 'PRIMARIA')->get();
                //return Role::where('name', 'Superadmin')->get();
            }
        ]);*/
        /*
        $this->crud->addField([
            // n-n relationship
            'label' => "Cursos que impartirá el docente", // Table column heading
            'type' => "select2_from_ajax_multiple",
            'name' => 'cursos', // the column that contains the ID of that connected entity
            'entity' => 'cursos', // the method that defines the relationship in your Model
            'attribute' => "descripcionCurso", // foreign key attribute that is shown to user
            'model' => "App\Models\Curso", // foreign key model
            'data_source' => url('getcursosnormal'), // url to controller search function (with /{id} should return model)
            'placeholder' => "Seleccione y/o busque aqui", // placeholder for the select
            'minimum_input_length' => 0, // minimum characters to type before querying results
            'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
            ]);*/
        /*
	$this->crud->addField([
	    'label' => "Cursos que impartirá el docente",
	    'type'  => 'checklist',

    ]);*/
        /*$this->crud->addField([
                'label' => "Curso que impartirá el docente",
                'type' => 'select2',
                'name' => 'id_curso', // the db column for the foreign key
                'entity' => 'cursos', // the method that defines the relationship in your Model
                'attribute' => 'descripcionCurso', // foreign key attribute that is shown to user
                'model' => "App\Models\Curso" // foreign key model
	]);*/

       // Columns.
        $this->crud->setColumns([
            [
                'name'  => 'name',
                'label' => trans('backpack::permissionmanager.name'),
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
	    [
	    	'name'  => 'status',
		'label' => 'Status',
		'type'  => 'boolean',
		'options' => [0 => 'Inactivo' , 1 => 'Activo'],
	    ],
	    /*[
		'name'   => 'cursos',
		'entity' => 'cursos',
	    	'label'  => 'No. cursos',
		'type'   => 'text',
		'model'  => "App\Models\Curso",
	],*/
	    /*
            [
                // n-n relationship (with pivot table)
                'label' => "Cursos Asignados", // Table column heading
                'type' => 'select_multiple',
                'name' => 'cursos', // the method that defines the relationship in your Model
                'entity' => 'cursos', // the method that defines the relationship in your Model
                'attribute' => "descripcionCurso", // foreign key attribute that is shown to user
                'model' => "App\Models\Curso", // foreign key model
	],*/
            /*
            [
                // Mostramos el nombre del curso que tiene asignado
                'label'     => 'Curso asignado', // Table column heading
                'type'      => 'select',
                'name'      => 'id_curso', // the method that defines the relationship in your Model
                'entity'    => 'cursos', // the method that defines the relationship in your Model
                'attribute' => 'descripcionCurso', // foreign key attribute that is shown to user
                'model'     => 'App\Models\Curso', // foreign key model    
            ],*/
        ]);

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
        // possible positions: 'beginning' and 'end'; defaults to 'beginning' for the 'line' stack, 'end' for the others;
        // $this->crud->addButton($stack, $name, $type, $content, $position); // add a button; possible types are: view, model_function
        // $this->crud->addButtonFromModelFunction($stack, $name, $model_function_name, $position); // add a button whose HTML is returned by a method in the CRUD model
        // $this->crud->addButtonFromView($stack, $name, $view, $position); // add a button whose HTML is in a view placed at resources\views\vendor\backpack\crud\buttons
        // $this->crud->removeButton($name);
        // $this->crud->removeButtonFromStack($name, $stack);
        // $this->crud->removeAllButtons();
        // $this->crud->removeAllButtonsFromStack('line');
        $this->crud->enableExportButtons();
        // ------ CRUD ACCESS
        // $this->crud->allowAccess(['list', 'create', 'update', 'reorder', 'delete']);
        $this->crud->denyAccess(['create', 'reorder', 'delete']);
        //$this->crud->addButtonFromModelFunction('line', 'add_grupo', 'addGrupo', 'beginning'); 
	$this->crud->addButtonFromModelFunction('line', 'ver_horario', 'verHorario', 'end');
        // ------ CRUD REORDER
        // $this->crud->enableReorder('label_name', MAX_TREE_LEVEL);
        // NOTE: you also need to do allow access to the right users: $this->crud->allowAccess('reorder');

        // ------ CRUD DETAILS ROW
        $this->crud->enableDetailsRow();
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
        // Mostrar usuarios solo con el rol 'DOCENTE'
        $this->crud->addClause('whereHas', 'roles', function($query){
            $query->where('name','DOCENTE');
        });
        // Order by
        $this->crud->orderBy('app');
        $this->crud->orderBy('apm');
        $this->crud->orderBy('name');
        $this->crud->orderBy('curp');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
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

    public function showDetailsRow($id)
    {
        //$user = User::find($id);
        
        $this->crud->hasAccessOrFail('details_row');
        
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        //$this->data['cursos'] = $user->cursos;
        
        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('crud::details_cursodocente_row', $this->data);
    }
}
