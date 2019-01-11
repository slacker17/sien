<table>
<tr>
    <th>semestre</th>
    <th>unidades</th>
    <th>curso</th>
<th>id_curso</th>
    <th>alumno</th>
<th>calificacion</th>
    <th>unidad</th>
    <th>asistencia</th>
  </tr>

@foreach($calificaciones as $calif)
    <tr>

<th>{{$calif->semestre}}</th>
<th>{{$calif->unidades}}</th>

<th>{{$calif->descripcionCurso}}</th>
<th>{{$calif->id_curso}}</th>
<th>{{$calif->id_alumno}}</th>
<th>{{$calif->calificacion}}</th>
<th>{{$calif->unidad}}</th>
<th>{{$calif->asistencia}}</th>

</tr>

    @endforeach
  
</table>

@foreach($calificaciones as $calif)
{{print_r($calif)}}
    <br>
    @endforeach