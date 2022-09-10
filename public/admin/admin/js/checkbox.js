$(document).ready(function(){
    var tab = [];
    var tabReceive= [];
    var i=1;
    var j=1;
    $(".checkOne").change(function (e) {
        var a = $(this).val();
        if ($(this).is(":checked")) {

            tabReceive = tab.push(a);
            /* $(this).closest('tr').addClass("highlight_row");*/
        } else {
           /* var a = $(this).val();*/
            tabReceive = tabReceive.filter(item => item !== a)
            /* $(this).closest('tr').removeClass("highlight_row");*/
        }
        console.log(tabReceive);
    });
    $('#checkAll').change(function(){
        if($(this).prop('checked')){
            $('tbody tr td input[type="checkbox"]').each(function(item){

                $(this).prop('checked', true);
                var a = $(this).val();
                tab = tab.filter(item => item !== a);
                tab[j]= $(this).val();
                j++;
                console.log(tab[j]);
            });
        }else{
            $('tbody tr td input[type="checkbox"]').each(function(item){
                $(this).prop('checked', false);
                tab = [];
            });
        }
        console.log(tab);
    });

});