
function onClickBtnvalider(event){
    //event.preventDefault();
    const url = this.href;
    const  class_=this;
    //console.log(icon.classList.value);
    // alert()

    $.ajax({
        url:      url,
        type:       'get',
        dataType:   'json',
        success: function(response,status){
            console.log("hhhhhhh")
            if (response.code === 200){
                //$('.etape').hide();
                // ligne.remove();
            }
            $('.etape-1').hide();


        },
        error :function(error)
        {
            console.log(error);
        }
    });

}
document.querySelectorAll('.etape').forEach(function (link) {
    link.addEventListener('click',onClickBtnvalider);
})
/*

function onClickBtnvalider2(event){
    event.preventDefault();
    const url = this.href;
    const  class_=this;
    //console.log(icon.classList.value);
    // alert()

    $.ajax({
        url:      url,
        type:       'get',
        dataType:   'json',
        success: function(response,status){
            console.log("hhhhhhh")
            if (response.code === 200){
                //$('.etape').hide();
                // ligne.remove();
            }
            $('.etape-2').hide();


        },
        error :function(error)
        {
            console.log(error);
        }
    });

}
document.querySelectorAll('.etape-2').forEach(function (link) {
    link.addEventListener('click',onClickBtnvalider2);
})


function onClickBtnvalider3(event){
    event.preventDefault();
    const url = this.href;
    const  class_=this;
    //console.log(icon.classList.value);
    // alert()

    $.ajax({
        url:      url,
        type:       'get',
        dataType:   'json',
        success: function(response,status){
            console.log("hhhhhhh")
            if (response.code === 200){
                //$('.etape').hide();
                // ligne.remove();
            }
            $('.etape-3').hide();


        },
        error :function(error)
        {
            console.log(error);
        }
    });

}
document.querySelectorAll('.etape-3').forEach(function (link) {
    link.addEventListener('click',onClickBtnvalider3);
})*/
