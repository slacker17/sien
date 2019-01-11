@extends('backpack::layout')

@section('header')
    <section class="content-header">
      <h1>
        Consultar certificado
      </h1>
    </section>
@endsection


@section('content')
<div class="row">
        <div class="col-md-4 col-md-offset-0">
<form action="/foo" method="POST">
    @method('POST')
    @csrf
    
    <div class="form-group">
    	<label for="formGroupExampleInput">Ingrese folio de certificado: </label>
    	<input type="text" class="form-control" id="formGroupExampleInput" placeholder="Folio">
    </div>
    <span id="msg"></span>
    <button type="submit" class="btn btn-primary">Buscar</button>
</form>	
</div>
</div>
@endsection
