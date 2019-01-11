<script type="text/javascript">
     $(document).ready(function(){
         // Cuando se le da submit al formulario de cambio de estatus en el modal
         $('#btnstatuschange-{{$entry->id_Alumno}}').click(function(){
             var r = confirm("Esta correcto el status?");
             if (r == true) {
                 $.post( 'updatestatusalumno' , { idalumno: '{{$entry->id_Alumno}}', status: $('#estado-{{$entry->id_Alumno}}').val() } )
                  .done(function( data ) {
                      var obj = jQuery.parseJSON( data );
                      if(obj.result == "ok"){ // si se registro correctamente
                          alert("Operacion hecha con exito!");
                          window.location.reload();
                      }else if(obj.result == "error"){
                          alert("Error en la operacion.");
                      }
                  },'json');
             }
         });

         // Cuando se le da submit al formulario de cambio de grupo en el modal
         $('#btngrupochange-{{$entry->id_Alumno}}').click(function(){
             var r = confirm("Esta correcto el grupo de destino?");
             if (r == true) {
                 $.post( 'updategrupoalumnoindividual' , { alumno: '{{$entry->id_Alumno}}', grupo: $('#nuevogrupo-{{$entry->id_Alumno}}').val() } )
                  .done(function( data ) {
                      var obj = jQuery.parseJSON( data );
                      if(obj.result == "ok"){ // si se registro correctamente
                          alert("Operacion hecha con exito!");
                          if(obj.cursado == "si") // Si ya es el ultimo alumno migrado redireccionamos a la lista de grupos
                              window.location.href="grupo";
                          else // si no solo recargamos la pagina
                              window.location.reload();
                      }else if(obj.result == "error"){
                          alert("Error en la operacion.");
                      }
                  },'json');
             }
         });
         
     });
</script>


      <!-- Modal Para el status-->
<div class="modal fade" id="statusModal{{$entry->id_Alumno}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
     <h5 class="modal-title" id="exampleModalLabel">Modificar estatus del alumno {{$entry->nombre}} {{$entry->app}} {{$entry->apm}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">


     <form>
             <div class="form-group">
    <label for="exampleFormControlSelect1">Seleccione Status</label>
    <select class="form-control" id="estado-{{$entry->id_Alumno}}">
      <option value="1">Alta</option>
      <option value="2">Baja Temporal</option>
      <option value="3">Baja Permanente</option>
    </select>
  </div>
            
        
            </form>
     
     
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" id="btnstatuschange-{{$entry->id_Alumno}}" class="btn btn-primary">Aceptar</button>
      </div>
    </div>
  </div>
</div>


    <!-- Modal para el cambio de grupo -->



<div class="modal fade" id="grupoModal{{$entry->id_Alumno}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
     <h5 class="modal-title" id="exampleModalLabel">Cambiar de Grupo al alumno <strong>{{$entry->nombre}} {{$entry->app}} {{$entry->apm}}</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <form>
    <div class="form-group">
    <label for="exampleInputEmail1">Grupo de origen</label>
    <input type="text" class="form-control" id="exampleInputEmail1" value="{{$entry->grupos->descripcion}}" readonly="readonly" aria-describedby="emailHelp" >
    <small id="emailHelp" class="form-text text-muted">El Grupo del Ciclo Escolar Actual.</small>
  </div>
    <br>
             <div class="form-group">
    <label for="exampleFormControlSelect1">Grupo de destino</label>

    <select class="form-control" id="nuevogrupo-{{$entry->id_Alumno}}">
                                                                                                                              @if($data_migration['existesiguienteciclo'] == true)
                                                                                                                              @foreach($data_migration['groups'] as $group)
                                                                                                                              <option value="{{$group->id_Grupos}}">{{$group->descripcion}}</option>

    @endforeach
        @else
            <option disabled> No hay opciones</option>
    @endif
   </select>
        <small id="emailHelp" class="form-text text-muted">Grupo del Ciclo Escolar Siguiente.</small>
  </div>
            
        
            </form>

     
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" id="btngrupochange-{{$entry->id_Alumno}}" class="btn btn-primary">Aceptar</button>
      </div>
    </div>
  </div>
</div>




                                                                                                                        <!-- Contenido (solo botones visibles de opciones)-->
                                                                                                                        
    <div class="card">
  <div class="card-header">
    <strong>Opciones</strong>
  </div>

  <div class="card-body">
    <blockquote class="blockquote mb-0">

                                                                                                                              @if($entry->status == 1)
     <a class="btn btn-default" target="_self"  title="Imprimir formato de inscripción/reinscripción" href="/public/inscripcionpdf/{{$entry->id_Alumno}}" data-toggle="tooltip">
     <i class="glyphicon glyphicon-list-alt"></i> Formato Inscripción</a>
                                                                                                                              
                                                                                                                              @if($data_migration['existesiguienteciclo'] == true)
     <a class="btn btn-default" target="_self" title="Cambair de grupo al alumno"  data-toggle="modal" data-target="#grupoModal{{$entry->id_Alumno}}">
     <i class="glyphicon glyphicon-list-alt"></i> Cambiar de Grupo</a>
                                                                                                                              @endif
                                                                                                                              
                                                                                                                              @endif
     <!-- Button trigger modal -->
     <a class="btn btn-default" id="openModalStatus-{{$entry->id_Alumno}}" title="Dar de baja a un alumno o viceversa" data-toggle="modal" data-target="#statusModal{{$entry->id_Alumno}}">
     <i class="glyphicon glyphicon-info-sign"></i> Cambiar Estatus
     </a>

                                                                                                                              <a class="btn btn-default" target="_self"  title="Imprimir Kardex del Alumno" href="/public/kardexpdf/{{$entry->id_Alumno}}" data-toggle="tooltip">
     <i class="glyphicon glyphicon-tasks"></i> Imprimir Kardex</a>

                                                                                                                              <!--<a class="btn btn-default" target="_self"  title="Imprimir Kardex del Alumno" href="/public/certificadopdf/{{$entry->id_Alumno}}" data-toggle="tooltip">
     <i class="glyphicon glyphicon-tasks"></i> Imprimir Certificado</a>-->
     
    </blockquote>
  </div>
     
</div>

