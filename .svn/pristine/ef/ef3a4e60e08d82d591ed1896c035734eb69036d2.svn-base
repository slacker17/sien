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
      <h3 class="text-center">Horario {{$grupo}}</h3>
@elseif($esdocente == 1)
    <h3 class="text-center">Horario {{$nombre}} </h3>
    @endif
           <table border="1"  cellspacing="0">
            <thead>
              <tr>
                <th bgcolor="#7ECCD2" scope="col" class="bg-info">Hora</th>
                @foreach ($dias as $dia)
                  <th bgcolor="#7ECCD2" scope="col" class="bg-info" >{{$dia}}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
               
               
                @foreach ($matriz as $fila)
                    <tr>
		
                    @foreach($fila as $columna)
                      @if( $columna[0] != '--' )
		
                        @if( $columna[1] != '-' and $columna[1] != 'h')
                          <td bgcolor="#7ED29E" class="bg-success text-center">
                            {{ $columna[0] }}<br>
</td>
                        @elseif($columna[1] == 'h')
			  <td bgcolor="#7ECCD2" class="bg-info text-center">{{ $columna[0] }}</td>
			@else
                          <td bgcolor="#7ED29E" class="bg-success text-center">{{ $columna[0] }}</td>
                        @endif

                      @else
                        <td>{{ $columna[0] }}</td>
                      @endif
                    @endforeach
		    
                    </tr>
                @endforeach

            </tbody>

            <tfoot>
             
               
              
            </tfoot>

          </table>
 </div>
            </div>
        </div>
    </body>
</html>