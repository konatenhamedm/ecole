var id_select = $('#dossier').val();
var vendeur = $('.vendeur').val();
var acheteur = $('.acheteur').val();
var lib = $('#libelleEtape');


function ajax(etape) {

    $.ajax({
        method: "GET",
        url: "valider",
        data: {"id": id_select, "etape": etape},
        dataType: 'json',
    }).done(function (response,status) {
        //  $("form").submit();
        //$('.tt').get(0).click()
        console.log(response)
        //alert(response.status)
        if (etape === 1) {
            lib.val("Recueil des pièces")
            $('.step-22').show()
            $('.sw-btn-next').click()
            $('#ident').hide()
            $('#piece_valider').show()
            $('.libelleVide2').hide()
        } else if (etape === 2) {

            lib.val("Redaction")
            $('.libelleVide3').hide()
            $('.step-33').show()
            $('.sw-btn-next').click()
            $('#redaction_valider').show()

        } else if (etape === 3) {

            lib.val("Signature")
            $('.step-44').show()
            $('#signer').show()
            $('.sw-btn-next').click()
            $('.libelleVide4').hide()

        } else if (etape === 4) {

            lib.val("Enregistrement")
            $('.step-55').show()
            $('.sw-btn-next').click()
            $('.libelleVide5').hide()
            $('#enr').show()
        }
        else if (etape === 5) {

            lib.val("Acte")
            $('.step-66').show()
            $('.sw-btn-next').click()
            $('.libelleVide6').hide()
            $('#remise_acte').show()
        }
        else if (etape === 6) {

            lib.val("Obtention")
            $('.step-77').show()
            $('.sw-btn-next').click()
            $('.libelleVide7').hide()
            $('#obtention_valider').show()
        }
        else if (etape === 7) {

            lib.val("Remise")
            $('.step-88').show()
            $('.sw-btn-next').click()
            $('.libelleVide8').hide()

            $('#remise_valider').show()
        }
        else if (etape === 8) {

            lib.val("Classification")
            $('.step-99').show()
            $('.sw-btn-next').click()
            $(".readonly").prop("disabled", true);
            $('.libelleVide9').hide()
            $('#classification_valider').show()
        }

    });
}

$('#ident').click(function (event) {
    event.preventDefault();

    ajax(1)
    const btn = $(this);
    btn.hide();
})

$('#piece_valider').click(function (event) {
    event.preventDefault();

    ajax(2)
    const btn = $(this);
    btn.hide();
})

$('#redaction_valider').click(function (event) {
    event.preventDefault();
    ajax(3)
    const btn = $(this);
    btn.hide();
})

$('#signer').click(function (event) {
    event.preventDefault();
    ajax(4)
    const btn = $(this);
    btn.hide();
})

$('#enr').click(function (event) {
    event.preventDefault();
    ajax(5)
    const btn = $(this);
    btn.hide();
})
$('#remise_acte').click(function (event) {
    event.preventDefault();
    ajax(6)
    const btn = $(this);
    btn.hide();
})
$('#obtention_valider').click(function (event) {
    event.preventDefault();
    ajax(7)
    const btn = $(this);
    btn.hide();
})
$('#remise_valider').click(function (event) {
    event.preventDefault();
    ajax(8)
    const btn = $(this);
    btn.hide();
})
$('#classification_valider').click(function (event) {
    event.preventDefault();
    ajax(9)
    const btn = $(this);
    btn.hide();
})