<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

        <!--<title>Laravel y data en PDF | Rimorsoft Online</title>-->

    </head>
    <body>
        <div class="container">
      <!--<div class="row">

      
                <img src="{{ asset('logos/sepe.jpg') }}" align=center class="img-responsive"  width="15%" />
      
                    
      
                <p class="text-center"> 
                    <strong>Unidad de Servicios Educativos de Tlaxcala
                    <br>
                    Dirección de Educación Terminal<br>
                    Sistema de Información de Escuelas Normales
                    </strong>
                </p>
      
                   
            <div class="text-center col-sm-4 col-md-4 hidden-xs hidden-sm">
                <img src="{{ asset('logos/uset.jpg') }}" align=center class="img-responsive"  width="13%" />
            </div>

        </div>-->
      
            <div class="row">
                <div class="col-xs-12">
      @if($esdocente == 0)
      <h4 class="text-center">Horario {{$grupo->descripcion}}
      @if(Auth::user()->idescuelanormal == 1)
          ({{ucwords(mb_strtolower($grupo->licenciatura))}})
              @endif  </h4>
              @elseif($esdocente == 1)
              <h3 class="text-center">Horario {{ucwords(mb_strtolower($nombre))}} </h3>
        @endif
        <table border="1"  cellspacing="0">
          <thead>
              <tr>
                <!--<th bgcolor="#7ECCD2" scope="col" class="bg-info">Hora</th>-->
                @foreach ($dias as $dia)
                  <th bgcolor="#7ECCD2" scope="col" class="bg-info" >{{$dia}}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
               
               @if($max > 0)
    
    @for($i = 0; $i < $max; $i++ )
        <tr>
@for($j = 0; $j < 7; $j++ )
    
    @if($matriz[$i][$j] != '--')
        @if($esdocente == 1 and Auth::user()->idescuelanormal == 1) <!-- si es docente y es cam -->
            <td bgcolor="#7ED29E" class="bg-success text-center" >
                  {{ $matriz[$i][$j][1] }}<br>{{ $matriz[$i][$j][2] }} - {{ $matriz[$i][$j][4] }}<br><b>{{ $matriz[$i][$j][3] }}</b>
      </td>
        @else
        <td bgcolor="#7ED29E" class="bg-success text-center" >
                  {{ $matriz[$i][$j][1] }}<br>{{ $matriz[$i][$j][2] }}<br><b>{{ $matriz[$i][$j][3] }}</b>
      </td>
        @endif
                                @else
                                    <td class="text-center">
                            {{ $matriz[$i][$j] }}
      </td>
                                @endif
    
                                    
@endfor
    </tr>
@endfor
@endif
    
    
            </tbody>

            <tfoot>
             
               
              
            </tfoot>

          </table>
 </div>
            </div>
        </div>
    </body>
</html>