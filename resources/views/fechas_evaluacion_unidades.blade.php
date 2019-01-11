@extends('backpack::layout')
@section('header')
	<section class="content-header">
	  <h1>
	    <span class="text">
    @if(isset($licenciaturas))
          Fechas de evaluación
    @else
    Asignacion para el semestre 
    @endif
	    </span>
	    <small><span></span></small>
	  </h1>
	  
	</section>
@endsection

@section('content')
    <script src="{{ asset('js/fechas_evaluacion.js') }}"></script>

<!-- Default box -->
  <div class="row">

    <!-- THE ACTUAL CONTENT -->
    <div class="col-md-12">
      <div class="box">
        <div class="box-header">
          <div id="datatable_button_stack" class="pull-right text-right hidden-xs">

    </div>
    Plan de estudios vigentes en el ciclo escolar actual:
        </div>

        <div class="box-body overflow-hidden table-responsive">
    
    @if(isset($licenciaturas))
    
    
    <div class="form-group" id="cont_licenciatura">
    <label for="">Licenciatura:</label>
    <select class="form-control" id="licenciaturas">
    <option selected disable>[ -- Seleccione -- ]</option>
    @foreach($licenciaturas as $licenciatura)
    <option value="{{$licenciatura->licenciatura}}">{{$licenciatura->licenciatura}}</option>
    @endforeach
    </select>
    <br>
  </div>
    
  <div class="form-group" id="cont_plan">
    <label for="exampleFormControlSelect1">Seleccione Plan de estudios</label>
    <select class="form-control" id="planes">
    <option selected disable>[ -- Seleccione -- ]</option>
      
    </select>
    <br>
  </div>
    
  <div class="form-group" id="cont_semestre">
    <label for="exampleFormControlSelect2">Seleccione semestre (se muestran los que son cursados en el ciclo escolar actual):</label>
    <select class="form-control" id="semestres">
      <option selected disable>[ -- Seleccione -- ]</option>
    </select>
  </div>

    
    <div class="container" id="btn_enviar">
    <button type="button" id="enviar" class="btn btn-primary">Continuar</button>
    </div>

    @elseif(isset($simplificado))
    <!--
    @foreach($cursos as $curso)
    <table class="table table-bordered">
  <thead>
    <tr>
    <td class="bg-success" colspan="{{$curso[1]}}">{{$curso[0]}}</td>
    </tr>
  </thead>

     <tbody>
    <tr>
@for($i=1;$i<=$curso[1];$i++)
    <th>Unidad {{$i}}</th>
    @endfor
    </tr>

    <tr>
@for($i=1;$i<=$curso[1]; $i++)
    <th>
Fecha Inicio: <input type="date" id="{{$curso[2][$i-1][0]}}"><br>
    Fecha Fin: <input type="date" id="{{$curso[2][$i-1][1]}}">
    </th>
    @endfor
    </tr>

<tr>
      
    </tr>
  </tbody>
    </table>
    @endforeach

-->

   


    @foreach($simplificado as $row)
 <div class="form-group">    
    <table class="table table-bordered">
    <thead>
    <tr>
    <td class="bg-success" colspan="{{count($row)}}">Asignacion de cursos con {{count($row)}} unidades</td> 
    </tr>
    </thead>


    <tbody>
    <tr>
@for($i=1;$i<=(count($row));$i++)
    <th >Unidad {{$i}}</th>
    @endfor
    </tr>

    <tr>
@for($i=1;$i<=count($row); $i++)
    <th>
Fecha Inicio: <br>
 
<input type="date" class="form-control" id="{{$row[$i-1]}}-fi">
 
                                                                                                  <br>
    Fecha Fin: <br><input type="date" class="form-control" id="{{$row[$i-1]}}-ff"><br>
    </th>
    @endfor
    </tr>

<tr>
      
    </tr>
  </tbody>
</table>
                                                                                                  </div>
    @endforeach
@endif
    


        </div><!-- /.box-body -->

      

      </div><!-- /.box -->
    </div>

  </div>
<script type="text/javascript">
            $(function () {
                $('#datetimepicker1').datetimepicker();
            });
        </script>
    
    @endsection

@section('after_styles')
  <!-- DATA TABLES -->
  <link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap.min.css">

  <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/crud.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/form.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/list.css') }}">

  <!-- CRUD LIST CONTENT - crud_list_styles stack -->
  @stack('crud_list_styles')
@endsection

