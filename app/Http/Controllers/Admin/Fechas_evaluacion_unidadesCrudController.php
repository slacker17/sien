<?php
namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\Fechas_evaluacion_unidadesRequest as StoreRequest;
use App\Http\Requests\Fechas_evaluacion_unidadesRequest as UpdateRequest;
use App\Models\Fechas_evaluacion_unidades;
use App\Models\Planestudio;
use App\Models\Cicloescolar;
use App\Models\Grupo;
use App\Models\Curso;
//use App\Models\Escuelanormal;
use Auth;
use DB;

class Fechas_evaluacion_unidadesCrudController extends CrudController
{
    public function setup()
    {

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Fechas_evaluacion_unidades');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/fechas_evaluacion_unidades');
        $this->crud->setEntityNameStrings('fechas_evaluacion_unidades', 'fechas_evaluacion_unidades');

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        
        $this->crud->setFromDb();

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

    public function store(StoreRequest $request){
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request){
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
    
    public function showFechas(){
        // Clico escolar actual
        $ciclo_actual = Cicloescolar::where('vigente', 1)->first();
        // Obenetemos licenciaturas
        $licenciaturas = DB::table('cursos')
            ->select('licenciatura')
            ->distinct()
            ->get();

        $data['licenciaturas'] = $licenciaturas;
        
        return view('fechas_evaluacion_unidades', $data);
    }
    
    
    /*** Metodos por donde se haran las operaciones de create, update, delete, read***/
    
    // Metodo que muestra la vista principal
    public function asignarFechas($licenciatura = "PRIMARIA", $plan = 1, $semestre = 1){
        // Sacamos el minimo de unidades y el maximo por el semestre seleccionado
        $min = DB::table('cursos')
            ->where('plan_id', $plan) // Comparamoas con el plan id
            ->where('licenciatura', $licenciatura) // comparamos con la licenciatuara
            ->where('semestre', $semestre)
            ->min('numeroUnidades');

        $max = DB::table('cursos')
            ->where('plan_id', $plan) // Comparamoas con el plan id
            ->where('licenciatura', $licenciatura) // comparamos con la licenciatuara
            ->where('semestre', $semestre)
            ->max('numeroUnidades');
        
        $final = [];
        // Recorremos rango de min y maximo para crear los formularios
        for($i=$min; $i <= $max; $i++){
            $temp = [];
            
            for($j=1; $j<=$i; $j++){
                array_push($temp, 'u-'.$j);
            }

            array_push($final, $temp);
        }
        
        /*********************************************************************/
        // Primero obtenemos los cursos con los parametros dados del formulario
        $cursos = Curso::where('semestre', $semestre)
            ->where('plan_id', $plan)
            ->where('licenciatura', $licenciatura)
            ->get();
        
        // Muestra del formulario  
        $af = [];
        
        foreach($cursos as $curso){
            $unidades = [];
            
            for($i = 1; $i <= $curso->numeroUnidades; $i++)
                array_push($unidades, ['u-'.$i.'-'.$curso->id_curso.'-fi', 'u-'.$i.'-'.$curso->id_curso.'-ff']);
            
            array_push($af, [
                $curso->descripcionCurso,
                $curso->numeroUnidades,
                $unidades
            ]);
        }
        
        // Data
        $data['cursos'] = $af;
        $data['simplificado'] = $final;
        // Retornamos vista
        return view('fechas_evaluacion_unidades', $data);
    }
    
    
    // Guardamos nuevo periodo de evaluacion
    public function saveFecha(Request $request){
        // Si es una peticion AJAX
        if($request->ajax()){
            
            // Buscamos si ya existe un registro similar (si existe es actualizacion)
            $existe = Fechas_evaluacion_unidades::where('unidad', $request->unidad)
                ->where('curso', $request->curso)
                ->where('cicloescolar', $request->ciclo_escolar)
                ->first();
            
            if($existe){ // Si existe actualizamos
                $existe->fecha_inicio = $request->fecha_inicio;
                $existe->fecha_fin = $request->fecha_fin;
                
                if($existe->save()) // Si se guarda la actualizacion
                    return "{\"result\": \"updateok\"}";
                else // Si no
                    return "{\"result\": \"updateerror\"}";
            }else{ // Si no creamos uno nuevo
                $nueva_fecha = new Fechas_evaluacion_unidades;
                
                $nueva_fecha->fecha_inicio = $request->fecha_inicio;
                $nueva_fecha->fecha_fin = $request->fecha_fin;
                $nueva_fecha->unidad = $request->unidad;
                $nueva_fecha->curso = $request->curso;
                $nueva_fecha->cicloescolar = $request->ciclo_escolar;
                
                if($nueva_fecha->save())
                    return "{\"result\": \"ok\"}";
                else
                    return "{\"result\": \"error\"}";
            }
        }
    }
    
    // Metodo para eliminar una fecha
    public function deleteFecha(Request $request){
        // Si es una peticion AJAX
        if( $request->ajax() ){
            // Buscamos elemento a traves del id recibido
            $fecha = Fechas_evaluacion_unidades::find( (int)$request->id );
            
            // Si se elimina con exito
        	if( $registro->delete() )
        		return "{\"result\":\"ok\"}";
        	else
        		return "{\"result\":\"error\"}";
        }
    }
}
