$(document).ready(function () {
    $("#membre_region").on('change',function(e) {
        var id_select = $(this).val();

        console.log(id_select);
        $('#membre_departement').disabled = false;
        var res = $(this).closest('.card-body');
        $.ajax({
            url:        '/admin/liste_tarife',
            type:       'POST',
            data:     {id:id_select},
            dataType:   'json',
            success: function(json,status){
                console.log(json);
                res.find('#membre_departement').html(''); //je vide la 2ème list
                res.find('#membre_departement').append('<option value="default">Selectionnez un villgage</option>');
                $.each(json, function(index, value) {
                    //console.log(value)// et une boucle sur la réponse contenu dans la variable passé à la function du success "json"
                    res.find('#membre_departement').append('<option value="'+ value.id +'" > ' + value.libelle +'</option>');
                });
            }
        });
    })

})

