




function init_color_picker() {
    $('.sp-color-picker').spectrum({
        preferredFormat: "hex",
        showInitial: true,
        chooseText: "Choisir",
        cancelText: "Annuler"
    });
}



function init_select2(selector = null, placeholder = null, dropDownParent = 'body', initials = [], set = true) {
    let $selector = null;
    if (selector) {
        if (selector == 'select') {
            $selector = $('select').not('.swal2-select').filter(function () {
                return !this.closest('.dataTables_length');
            });
        } else {
            $selector = $(selector);
        }
    } else {
        $selector = $('.select2_single, .has-select2, .select2_multiple, .select2-multiple, .select2-single, .select2');
    }

    $selector.each(function () {
        let $this = $(this);
        
        const placeholder = $this.attr('placeholder') || $this.data('select2-placeholder');
        const multiple = $this.prop('multiple');
        const default_placeholder = multiple ? 'Selectionner au moins un élément de la liste' : 'Sélectionner un élément de la liste';
        let $item = $this.select2({
            //placeholder: placeholder || 
            //tags: tag,
            //multiple: multiple,
            tokenSeparators: [","],
            data: initials,
            theme: 'bootstrap-5',
            dropdownParent: $(dropDownParent || 'body'),

            //dropDownParent: dropDownParent,

            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 0,
            /*templateResult: format_data, // omitted for brevity, see the source of this page
            templateSelection: format_data_selection, // omitted for brevity, see the source of this page*/
            noResults: function () {
                return 'Aucun résultat';
            },
            searching: function () {
                return 'Recherche…';
            }
        });

        
        $item.next('.select2-container').width('100%');

        if (Array.isArray(initials) && initials.length && set) {
            let ids = [];
            initials.forEach((initial) => {
                ids.push(initial.id);
            });

            $item.val(ids).trigger('change');
        }
      
    });

}


function init_text_editor(height, options = {}) {

                   
                   
    $('.has-editor').each(function() {
        const $this = $(this);
        if ($this.data('height')) {
            height = +$this.data('height');
        }
        

        let defaults = {
            height: height ? +height: 800,
            lang: 'fr-FR'
        };
    
        options = $.extend(defaults, options);
        $this.summernote(options);
    });
}


function init_date_picker(selector = null, drops = 'down', cb = null, minYear = null, maxYear = null, autoUpdateInput = true, minDate = null, maxDate = null, format = null) {
    format = format || 'DD/MM/YYYY';
    let timepicker = false;

    if (selector == '.datetimepicker') {
        format += ' HH:mm';
        timepicker = true;
    }

    console.log(selector);

    /*if (selector == 'daterangetimepicker') {
        format = 'HH:mm:mm';
        timepicker = true;
    }*/

    var minDate = null;
    var maxDate = null;
    var $selector = $(selector ? selector : '.has-datepicker');




    if ($selector.hasClass('datetimepicker') && !timepicker) {
        format += ' HH:mm';
        timepicker = true;
    }

    if (!maxYear && !minYear) {
        var d = new Date();

        minYear = d.getFullYear() - 5;
        maxYear = d.getFullYear() + 10;
    }

    if (minYear && !minDate) {
        minDate = '01/01/' + minYear;
    }

    if (maxYear && !maxDate) {
        maxDate = '31/12/' + maxYear;
    }

    
    let cbs = [];

    $selector.each(function (index, current) {
        var $this = $(this);


        if ($this.hasClass('no-auto')) {
            autoUpdateInput = false;
        }


        if (!autoUpdateInput && !cb) {
            cb = (start, e) => {
                $this.val(start.format(format));
            };
        }

        $this.daterangepicker({
            singleDatePicker: true,
            autoUpdateInput: autoUpdateInput,
            showDropdowns: true,
            autoApply: true,
            timePicker24Hour: timepicker,
            timePicker: timepicker,
            maxYear: +maxYear,
            minYear: +minYear,
            minDate: minDate,
            maxDate: maxDate,
            drops: drops,
            locale: {
                format: format,
                firstDay: 1,
                "applyLabel": "Choisir",
                "cancelLabel": "Annuler",
                "daysOfWeek":
                    ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
                "monthNames":
                    [
                        "Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin",
                        "Juillet", "Aôut", "Septembre", "Octobre", "Novembre", "Decembre"
                    ],
            }
        }, cb);

        /*$this.on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        });*/
    });
}

$(function () {
    $(document).ready(function () {
        init_select2('select');
        $('.has-datepicker').not('.skip-init').each(function () {
            init_date_picker($(this));
        })
        

        $(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
            $(this).closest(".select2-container").siblings('select:enabled').select2('open');
          })
          
          $(".form-control.select-search").on('select2:closing', function (e) {
            $(e.target).data("select2").$selection.one('focus focusin', function (e) {
              e.stopPropagation();
            });
          });

        $('.alert-tmp').closest('.alert').fadeOut(1000);

        $('.input-float').each(function () {
            const $this = $(this);
            if (!$this[0].hasAttribute('im-insert')) {
                $this.inputmask({
                    alias: 'numeric',
                    allowMinus: false,
                })
            }
        });


        if (typeof $.spectrum != 'undefined') {
            init_color_picker();
        }

        

        $('.custom-file input').change(function (e) {
            var files = [];
            for (var i = 0; i < $(this)[0].files.length; i++) {
                files.push($(this)[0].files[i].name);
            }
            $(this).next('.custom-file-label').html(files.join(', '));
        });


        $('.custom-file input').change(function (e) {
            var files = [];
            for (var i = 0; i < $(this)[0].files.length; i++) {
                files.push($(this)[0].files[i].name);
            }
            $(this).next('.custom-file-label').html(files.join(', '));
        });

        $('.grid-dt-wrapper').on( 'init.dt', function (e, settings, json) {
            var api = new $.fn.dataTable.Api( settings );
            const id = $(this).attr('id');
            $( '.text-filter', '#' + id ).on( 'input', function () {
                const $this = $(this);
                api.columns($this.attr('data-index'))
                       .search( this.value )
                       .draw();
           });
            var select = $( '.select2-filter', '#' + id );
           
           
            select.on( 'change', function () {
                const $this = $(this);
               
                api.columns($this.attr('data-index'))
                       .search( this.value )
                       .draw();
           } );

           $('.date-filter').each(function () {
                const $this = $(this);
                const $id = $('#' + $this.attr('id'));
                init_date_picker($id,  'down', (start, e) => {
                }, null, null, false);
        
                $id.on('apply.daterangepicker blur', function (ev, picker) {
                    const $this = $(this);
                    let searchValue = '';
                    if (ev.type == 'apply' && ev.namespace == 'daterangepicker') {
                        val = picker.startDate.format('DD/MM/YYYY');
                        searchValue = val;
                    } else {
                        val = $this.val();
                        if (val) {
                            let [jour, mois, annee] = val.split('/');
                            searchValue = annee + '-' + mois + '-' + jour;
                        }
                       
                    }
                    $this.val(val);
                   

                    api.columns($this.attr('data-index'))
                    .search( searchValue )
                    .draw()

                });
            })
         

        });
    })
})