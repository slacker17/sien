@if($guardado)
    guardado
@else
    error
@endif

        @foreach($lineas as $linea)
{{print_r($linea)}}<br><br><br>
        @endforeach