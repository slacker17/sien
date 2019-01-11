@extends('backpack::layout')

@section('header')
    <section class="content-header">
      <h1>
        Certificación
      </h1>
    </section>
@endsection


@section('content')
	La Secretaría de Educación Pública de Tlaxcala a través de la Escuela {{$alumno->escuelasnormales->nombre}} {{$alumno->escuelasnormales->nombre_personaje}}, con Clave de Trabajo {{$alumno->escuelasnormales->cct}} <b>CERTIFICA</b> que <b>{{$alumno->nombre}} {{$alumno->app}} {{$alumno->apm}}</b> con CURP <b>{{$alumno->curp}}</b>, cursó los estudios completos de Licenciado en Educación {{$alumno->escuelasnormales->licenciatura}}, en la modalidad Escolarizada, conforme al Plan de Estudios 2012, con clave de carrera {{$alumno->escuelasnormales->clave_carrera}} y clave de Institución {{$alumno->escuelasnormales->clave_institucion}}, según constancias que obran en el Área de Control Escolar.
   
@endsection
