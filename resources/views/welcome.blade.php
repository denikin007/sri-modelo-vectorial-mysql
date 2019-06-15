<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="/vendor/bootstrap/css/bootstrap.min.css">
    <script src="/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="/js/jquery-3.4.1.min.js"></script>
    <script src="/vendor/buscador/buscador.js"></script>

</head>
<body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="/">Recuperacion de la información</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                {{-- <li class="nav-item active">
                    <a class="nav-link" href="#">Buscar <span class="sr-only">(current)</span></a>
                </li> --}}
                <li class="nav-item">
                    <a class="nav-link" href="/subirArchivo">Subir Archivo</a>
                </li>
                </ul>
                <form id="buscador" class="form-inline my-4 my-lg-0">
                    <input id="palabla" name="palabra" class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                    <button id="buscar" class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                </form>
                
            </div>
        </nav>
    @yield('content')

</body>
</html>