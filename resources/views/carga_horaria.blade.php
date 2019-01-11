@extends('backpack::layout')

@section('header')
	<section class="content-header">
	  <h1>
    <span class="text">
    @if($esdocente == 0)
      @if(Auth::user()->idescuelanormal == 1)
        {{ ucwords(mb_strtolower($grupo->licenciatura)) }}
        <br>
      @endif
      Horario del grupo {{$grupo->descripcion}}
    @else
        Horario del Docente {{ucwords(mb_strtolower($nombre))}}
    @endif
	    </span>
    <!--<small><span></span></small>-->
	  </h1>
	  <ol class="breadcrumb">
	    <li><a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}"></a></li>
	    <li><a href="" class="text-capitalize"></a></li>
	    <li class="active"></li>
	  </ol>
	</section>
@endsection

@section('content')
    @if($esdocente == 0)
<script src="{{ asset('js/gruposcursosdocentes.js') }}"></script>
    @endif

<!-- Default box -->
  <div class="row">

    <!-- THE ACTUAL CONTENT -->
    <div class="col-md-12">
      <div class="box">
        <div class="box-header">
          <div id="datatable_button_stack" class="pull-right text-right hidden-xs">
    @if($esdocente == 0)
    <a href="/public/admin/cargahoraria/{{$idgrupo}}/0/1" class="btn btn-primary ladda-button" data-style="zoom-in"><span class="ladda-label"><i class="glyphicon glyphicon-print"></i> Imprimir</span></a>
    @elseif($esdocente == 1)
    <a href="/public/admin/cargahoraria/{{$iddocente}}/1/1" class="btn btn-primary ladda-button" data-style="zoom-in"><span class="ladda-label"><i class="glyphicon glyphicon-print"></i> Imprimir</span></a>
    @endif
    </div>
    
    @if($esdocente == 0)
    <!-- Modal para formulario-->
          <button 
            type="button" 
            class="btn btn-primary" 
            data-toggle="modal" 
            data-target="#favoritesModal"
            id="agregar">
            Agregar Horario
          </button>

          <div class="modal fade" id="favoritesModal" 
            tabindex="-1" role="dialog" 
            aria-labelledby="favoritesModalLabel">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" 
                    data-dismiss="modal" 
                    aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" 
                    id="favoritesModalLabel">Agregar Horario</h4>
                </div>
              <div class="modal-body">
                <form>
                    <div class="form-group">
                      <label for="exampleFormControlSelect1">Curso *</label>
                      <select class="form-control" id="curso">
                            <option  disabled selected value>[ -- Seleccione -- ]</option>
                        @foreach ($cursos as $curso)
                            <option value="{{$curso->id_curso}}">{{$curso->descripcionCurso}}</option>
                        @endforeach

                      </select>
                      <div id="msgcurso"></div>
                    </div>
                    <div class="form-group">
                      <label for="exampleFormControlSelect2">Docente *</label>
                      <select class="form-control" id="docente">
                      
                      </select>
                      <div id="msgdocente"></div>
                    </div>
                    <div class="form-group">
                      <label for="exampleFormControlSelect2">Dia *</label>
                      <select class="form-control" id="dia">
                        <option  disabled selected value>[ -- Seleccione -- ]</option>
                        {{$i=1}}
                        @foreach($dias as $dia)
                          <option value="{{$i}}">{{$dia}}</option>
                          {{$i++}}
                        @endforeach
                      </select>
                      <div id="msgdia"></div>
                    </div>

                    <div class="form-group">
                      <label for="exampleFormControlSelect2">Hora inicio *</label>
		      <input type="time"  class="form-control" id="horainicio">
                      <!--<select class="form-control" id="horainicio">
                        <option  disabled selected value>[ -- Seleccione -- ]</option>
                        {{--@foreach($horas_select as $hora)--}}
                          {{--<option value="{{$hora}}">{{$hora}}</option>--}}
                          {{--@endforeach--}}
                      </select>-->
                      <div id="msghorainicio"></div>
                    </div>

                    <div class="form-group">
                      <label for="exampleFormControlSelect2">Hora fin *</label>
		      <input type="time"  class="form-control" id="horafin">
                      <!--<select class="form-control" id="horafin">
                        <option  disabled selected value>[ -- Seleccione -- ]</option>
                      {{--@foreach($horas_select as $hora)--}}
                          {{--<option value="{{$hora}}">{{$hora}}</option>--}}
                          {{--@endforeach--}}
                      </select>-->
                      <div id="msghorafin"></div>
                    </div>
                  <!-- idgrupo-->
                   <input type="hidden" name="grupo" value="{{$idgrupo}}" id="grupoid">
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" 
                  class="btn btn-default" 
                  data-dismiss="modal">Cancelar</button>
                <span class="pull-right">
                  <button type="button" class="btn btn-primary" id="guardar">
                    Guardar
                  </button>
                </span>
              </div>
            </div>
          </div>
        </div>
        </div>
    @endif

        <div class="box-body overflow-hidden table-responsive">

           <table id="crudTable" class="table table-striped table-bordered table-hover display responsive nowrap" cellspacing="0">
            <thead>
              <tr>
               
                @foreach ($dias as $dia)
                  <th scope="col" class="bg-info" >{{$dia}}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>

@if($max > 0)
    
    @for($i = 0; $i < $max; $i++ )
        <tr>
        @for($j = 0; $j < 7; $j++ )
            <td class="text-center">
                         @if($matriz[$i][$j] != '--')
                              @if($esdocente == 0) <!-- Si no es docente, es decir es subdirector academico -->
                                
                                  {{ $matriz[$i][$j][1] }}<br>{{ $matriz[$i][$j][2] }}<br><b>{{ $matriz[$i][$j][3] }}</b><br>
                                  <a  onclick="eliminarCarga({{$matriz[$i][$j][0]}})" class="btn btn-danger"><i class="fa fa-trash"></i>Eliminar</a>
                                
                              @else<!-- Si es docente -->

                                @if(Auth::user()->idescuelanormal == 1)
                                  {{ $matriz[$i][$j][1] }}<br>{{ $matriz[$i][$j][2] }} - {{ $matriz[$i][$j][4] }}<br>{{ $matriz[$i][$j][3] }}
                                  <br>
                                @else
                                  {{ $matriz[$i][$j][1] }}<br>{{ $matriz[$i][$j][2] }}<br>{{ $matriz[$i][$j][3] }}
                                @endif
                                
                              @endif
                         @else
                            {{ $matriz[$i][$j] }}
                         @endif
                                    </td>
        @endfor
        </tr>
    @endfor
    @else
        <td>Aún no ha agregado horarios.</td>
    @endif

              </tbody>
   
            <tfoot>
             
               
              
            </tfoot>

          </table>
        

        </div><!-- /.box-body -->
      
      </div><!-- /.box -->
    </div>

  </div>

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

