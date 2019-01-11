<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CursoRequest as StoreRequest;
use App\Http\Requests\CursoRequest as UpdateRequest;
use Auth;
use App\Models\Escuelanormal;

class CursoCrudController extends CrudController
{
    public function setup()
    {

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Curso');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/curso');
        $this->crud->setEntityNameStrings('curso', 'cursos');

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        //$this->crud->setListView('listado_cursos');// Vista personalizada para mostrar cursos
        $this->crud->setFromDb();

        $this->crud->addColumn([
            'name' => 'descripcionCurso', // The db column name
            'label' => "Descripción del curso", // Table column heading
        ]);
        
        $this->crud->addColumn([
            'name' => 'duracionHras', // The db column name
            'label' => "Duración (horas)", // Table column heading
        ]);
        
        $this->crud->addColumn([
         'name' => 'numeroUnidades', // The db column name
         'label' => "No. de unidades", // Table column heading
        ]);
        
        $this->crud->addColumn([
            
                // 1-n relationship
                'label' => "Plan", // Table column heading
                'type' => "select",
                'name' => 'plan_id', // the column that contains the ID of that connected entity;
                'entity' => 'plan', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Planestudio", // foreign key model
            
        ]);
            
        $this->crud->addField([
            'name' => 'descripcionCurso',
            'label' => "Descripción del curso"
        ]);

        $this->crud->addField([
         'name' => 'duracionHras', // The db column name
         'label' => "Duración (horas)", // Table column heading
         'type' => 'number',
        ]);
        
        $this->crud->addField([
         'name' => 'numeroUnidades', // The db column name
         'label' => "No. de unidades", // Table column heading
         'type' => 'number',
        ]);

        $this->crud->addField([
         'name' => 'semestre', // The db column name
         'label' => "Semestre (seleccione)", // Table column heading
         'type' => 'select2_from_array',
         'options' => [
             null => '-',
             '1' => '1', '2' => '2',
             '3' => '3', '4' => '4',
             '5' => '5', '6' => '6',
             '7' => '7', '8' => '8',
         ],
         'allows_null' => false,
         'default' => 'null',
        ]);

        $this->crud->addField([  // Select2
            'label' => "Plan de estudios al que pertenece (seleccione)",
            'type' => 'select2',
            'name' => 'plan_id', // the db column for the foreign key
            'entity' => 'plan', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => "App\Models\Planestudio" // foreign key model
        ]);

        $this->crud->addField([
            'name' => 'licenciatura', // The db column name
            'label' => "Licenciatura (seleccione)", // Table column heading
            'type' => 'select2_from_array',
            'options' => [
                null => '-',
                'PRIMARIA' => 'PRIMARIA', 'PREESCOLAR' => 'PREESCOLAR',
                'SECUNDARIA CON ESPECIALIDAD EN ESPAÑOL' => 'SECUNDARIA CON ESPECIALIDAD EN ESPAÑOL',
                'SECUNDARIA CON ESPECIALIDAD EN MATEMÁTICAS' => 'SECUNDARIA CON ESPECIALIDAD EN MATEMÁTICAS', 
            ],
            'allows_null' => false,
            'default' => 'null',
        ]);
        
        $this->crud->enableExportButtons();

	if(!Auth::user()->hasRole('Admin')){
	    $this->crud->removeAllButtons();
            //$this->crud->denyAccess(['create', 'update', 'reorder', 'delete']);
        }

        // Para docentes
        if(Auth::user()->hasRole('DOCENTE')){
            
            // Querys            
            $this->crud->addClause('whereHas', 'profesores', function($query){
                $query->where('id', Auth::user()->id);
            });

            // Actions Buttons
            //$this->crud->addButtonFromModelFunction('line', 'grupos_curso', 'verGruposCurso', 'beginning'); 
	    $this->crud->addButtonFromModelFunction('line', 'grupos_curso', 'verGruposCurso', 'end');
        }

        // Para subdirector academico
        if(Auth::user()->hasRole('SUBDIRECTOR ACADÉMICO')){
            // Definimos que licenciatura va a aparecer (primaria o preescolar)

            // Obtenemos la escuela del usuario que esta logueado
            $escuela = Escuelanormal::find(Auth::user()->idescuelanormal);

            // Si es preescolar
            if($escuela->nombre == "NORMAL PREESCOLAR"){
                $this->crud->addClause('where', 'licenciatura', '=', 'PREESCOLAR');
            }
	    else if($escuela->id == 1){ // Si es cam                                                                                                                    
		$this->crud->addClause('where', 'licenciatura', '=', 'SECUNDARIA CON ESPECIALIDAD EN ESPAÑOL');
                $this->crud->addClause('orWhere', 'licenciatura', '=', 'SECUNDARIA CON ESPECIALIDAD EN MATEMÁTICAS');
                $this->crud->addClause('orWhere', 'licenciatura', '=', 'PRIMARIA');
                $this->crud->addClause('where', 'plan_id', '=', 1);		
                $this->crud->addClause('orderBy', 'licenciatura');
                $this->crud->addClause('orderBy', 'semestre');
                $this->crud->addClause('orderBy', 'created_at');

                $this->crud->removeColumn('plan_id');
                $this->crud->removeColumn('duracionHras');
                $this->crud->addColumn('licenciatura');
            }
	    else{ // Si no es para primarias
                $this->crud->addClause('where', 'licenciatura', '=', 'PRIMARIA');
            }
            

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
        //$this->crud->addButtonFromModelFunction('line', 'open_docentes', 'asignarDocentes', 'beginning'); // add a button whose HTML is returned by a method in the CRUD model
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
                // $this->crud->allowAccess(['list', 'reorder']);
        //$this->crud->denyAccess(['create', 'update', 'delete']);

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
}
