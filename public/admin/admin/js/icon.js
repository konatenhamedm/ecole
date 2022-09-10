$(document).ready(function() {

    var $collectionHolder;

    var $addTagButton = $('.add_icon');
    $(document).ready(function () {
        $collectionHolder = $('#icon');
        /*$collectionHolder.append($addTagButton);*/
        $collectionHolder.data('index', $collectionHolder.find('.container').length)
        $collectionHolder.find('.container').each(function () {
            addRemoveButton($(this));
        })
        $addTagButton.click(function (e) {
            e.preventDefault();
            addForm();
            $('select').select2();
        })

    })

    function addForm() {
        var prototype = $collectionHolder.data('prototype');
        var index = $collectionHolder.data('index');
        var newForm = prototype;
        newForm = newForm.replace(/__name__/g, index);
        $collectionHolder.data('index', index + 1);
        var $card = $('<div class="container col-md-12"></div>')
        $card.append(newForm);
        addRemoveButton($card);
        $collectionHolder.find('.after').before($card);

    }

    function addRemoveButton($card) {
        var $removeButton = $('<a href="#" class="btn btn-icon btn-sm btn-danger supprimer" data-card-tool="remove" data-toggle="tooltip" data-placement="top" title="" data-original-title="Remove Card"><i class="ki ki-close icon-nm"></i> </a>');
        $removeButton.click(function (e) {
            console.log($(e.target).parent('.container'));

            $(e.target).parents('.container').slideUp(1000, function () {
                $(this).remove();
            });

        })

        $card.find(".supprimer").append($removeButton);
    }


});