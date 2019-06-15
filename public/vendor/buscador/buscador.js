$(document).ready(function() {
    var str;
    var consulta = [];
    $('#buscar').on('click', function(e) {
        e.preventDefault();
        $('#coleccion').empty();
        str = $('#palabla').val();
        str = str.toLowerCase();
        url = "/calcular/" + str;
        $.get(url, function(response, state) {
            console.log(response);
            consulta = response;
            consulta.forEach(function(element) {
                console.log("anadido: " + element.url);
                $('#coleccion').append('<a href="' + element.url + '" class="stretched-link">' + element.name_doc + '</a> <hr>');
            });
        });
    });
});