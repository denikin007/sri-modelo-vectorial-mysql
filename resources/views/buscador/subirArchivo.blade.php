@extends('welcome')
@section('content')
<div class="container p-5">
        <form action="/indexar" class="form-group" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <h1>Subir un archivo html</h1>
            <div class="form-group">
                <label for="">Archivo</label>
                <input type="file" name="archivo">
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
</div>

@endsection