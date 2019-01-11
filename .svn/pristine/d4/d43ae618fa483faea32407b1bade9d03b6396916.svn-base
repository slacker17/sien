<!--<div class="alert alert-success" role="alert">
  This is a success alert with <a href="#" class="alert-link">an example link</a>. Give it a click if you like.
</div>-->


<div class="card text-center">
  <div class="card-header">
         <strong>
Detalles
         </strong>
  </div>
  <div class="card-body">
         
         <h5 class="card-title">Nombre: <strong>{{$entry->name}} {{$entry->app}} {{$entry->apm}}</strong></h5>
Cursos Asignados:
    @if(count($entry->cursos) > 0)
        <br>
        @foreach($entry->cursos as $curso)
    <strong>{{$curso->descripcionCurso}}</strong><br> 
@endforeach
    @else
        <strong>Ninguno</strong>
    @endif
    <!--<a href="#" class="btn btn-primary">Go somewhere</a>-->
  </div>
  <div class="card-footer">
Status:
                                                                                     @if($entry->status == 1)
                                                                                         <strong>Activo</strong>
                                                                                     @elseif($entry->status == 0)
                                                                                         <strong>Inactivo</strong>
                                                                                         @endif
  </div><br><br>
</div>