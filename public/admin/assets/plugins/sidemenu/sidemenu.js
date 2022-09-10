(function () {
    "use strict";

    var slideMenu = $('.side-menu');

    // Toggle Sidebar
    $(document).on('click', '[data-bs-toggle="sidebar"]', function (event) {
        event.preventDefault();
        $('.app').toggleClass('sidenav-toggled');
    });


    var position = window.location.pathname.split('/');
    $(".app-sidebar li a").each(function () {
        var $this = $(this);
        var pageUrl = $this.attr("href").split("/");
        if (pageUrl) {
            if (position[position.length - 1] == pageUrl[pageUrl.length - 1]) {
                $(this).addClass("active");
                $(this).parent().addClass("is-expanded");
                $(this).parent().parent().prev().addClass("active");
                $(this).parent().parent().addClass("open");
                $(this).parent().parent().prev().addClass("is-expanded");
                $(this).parent().parent().parent().addClass("is-expanded");
                $(this).parent().parent().parent().parent().addClass("open");
                $(this).parent().parent().parent().parent().prev().addClass("active");
                $(this).parent().parent().parent().parent().parent().addClass("is-expanded");
                return false;
            }
        }
    });
    if ($('.slide-item').hasClass('active')) {
        $('.app-sidebar').animate({
            scrollTop: $('a.slide-item.active').offset().top - 600
        }, 600);
    }
    if ($('.sub-slide-item').hasClass('active')) {
        $('.app-sidebar').animate({
            scrollTop: $('a.sub-slide-item.active').offset().top - 600
        }, 600);
    }


    var toggleSidebar = function () {
        var w = $(window);
        if (w.outerWidth() <= 1024) {
            $("body").addClass("sidebar-gone");
            $(document).off("click", "body").on("click", "body", function (e) {
                if ($(e.target).hasClass('sidebar-show') || $(e.target).hasClass('search-show')) {
                    $("body").removeClass("sidebar-show");
                    $("body").addClass("sidebar-gone");
                    $("body").removeClass("search-show");
                }
            });
        } else {
            $("body").removeClass("sidebar-gone");
        }
    }
    toggleSidebar();
    $(window).resize(toggleSidebar);



    //sticky-header
    $(window).on("scroll", function (e) {
        if ($(window).scrollTop() >= 70) {
            $('.app-header').addClass('fixed-header');
            $('.app-header').addClass('visible-title');
        } else {
            $('.app-header').removeClass('fixed-header');
            $('.app-header').removeClass('visible-title');
        }
    });

    $(window).on("scroll", function (e) {
        if ($(window).scrollTop() >= 70) {
            $('.horizontal-main').addClass('fixed-header');
            $('.horizontal-main').addClass('visible-title');
        } else {
            $('.horizontal-main').removeClass('fixed-header');
            $('.horizontal-main').removeClass('visible-title');
        }
    });
})();

function hovermenu() {

    $(".app-sidebar").hover(function () {
        if ($('.app').hasClass('sidenav-toggled')) {
            $('.app').addClass('sidenav-toggled-open');
        }
    }, function () {
        if ($('.app').hasClass('sidenav-toggled')) {
            $('.app').removeClass('sidenav-toggled-open');
        }
    });
}

// ______________ICON-TEXT JS start
function icontext() {
    $(".app-sidebar").off("mouseenter mouseleave");

    $(document).on('click', ".app-sidebar", function (event) {
        if ($('body').hasClass('sidenav-toggled') == true) {
            $('body').addClass('sidenav-toggled-open');
        }
    });

    $(document).on('click', ".main-content", function (event) {
        $('body').removeClass('sidenav-toggled-open');
    });
}

//________________Horizontal js
jQuery(function () {
    'use strict';
    document.addEventListener("touchstart", function () { }, false);
    jQuery(function () {
        jQuery('body').wrapInner('<div class="horizontalMenucontainer" />');
    });
}());



function menuClick() {
    // clearing the clicking functions already present on the element
    $("[data-bs-toggle='slide']").off('click');
    $("[data-bs-toggle='sub-slide']").off('click');
    $("[data-bs-toggle='sub-slide2']").off('click');

    // initiating the click function
    $("[data-bs-toggle='slide']").on('click', function (e) {
        var $this = $(this);
        var checkElement = $this.next();
        var animationSpeed = 300,
            slideMenuSelector = '.slide-menu';
        if (checkElement.is(slideMenuSelector) && checkElement.is(':visible')) {
            checkElement.slideUp(animationSpeed, function () {
                checkElement.removeClass('open');
            });
            checkElement.parent("li").removeClass("is-expanded");
        } else if ((checkElement.is(slideMenuSelector)) && (!checkElement.is(':visible'))) {
            var parent = $this.parents('ul').first();
            var ul = parent.find('ul:visible').slideUp(animationSpeed);
            ul.removeClass('open');
            var parent_li = $this.parent("li");
            checkElement.slideDown(animationSpeed, function () {
                checkElement.addClass('open');
                parent.find('li.is-expanded').removeClass('is-expanded');
                parent_li.addClass('is-expanded');
            });
        }
        if (checkElement.is(slideMenuSelector)) {
            e.preventDefault();
        }
    });

    // Activate sidebar slide toggle
    $("[data-bs-toggle='sub-slide']").on('click', function (e) {
        var $this = $(this);
        var checkElement = $this.next();
        var animationSpeed = 300,
            slideMenuSelector = '.sub-slide-menu';
        if (checkElement.is(slideMenuSelector) && checkElement.is(':visible')) {
            checkElement.slideUp(animationSpeed, function () {
                checkElement.removeClass('open');
            });
            checkElement.parent("li").removeClass("is-expanded");
        } else if ((checkElement.is(slideMenuSelector)) && (!checkElement.is(':visible'))) {
            var parent = $this.parents('ul').first();
            var ul = parent.find('ul:visible').slideUp(animationSpeed);
            ul.removeClass('open');
            var parent_li = $this.parent("li");
            checkElement.slideDown(animationSpeed, function () {
                checkElement.addClass('open');
                parent.find('li.is-expanded').removeClass('is-expanded');
                parent_li.addClass('is-expanded');
            });
        }
        if (checkElement.is(slideMenuSelector)) {
            e.preventDefault();
        }
    });

    // Activate sidebar slide toggle
    $("[data-bs-toggle='sub-slide2']").on('click', function (e) {
        var $this = $(this);
        var checkElement = $this.next();
        var animationSpeed = 300,
            slideMenuSelector = '.sub-slide-menu2';
        if (checkElement.is(slideMenuSelector) && checkElement.is(':visible')) {
            checkElement.slideUp(animationSpeed, function () {
                checkElement.removeClass('open');
            });
            checkElement.parent("li").removeClass("is-expanded");
        } else if ((checkElement.is(slideMenuSelector)) && (!checkElement.is(':visible'))) {
            var parent = $this.parents('ul').first();
            var ul = parent.find('ul:visible').slideUp(animationSpeed);
            ul.removeClass('open');
            var parent_li = $this.parent("li");
            checkElement.slideDown(animationSpeed, function () {
                checkElement.addClass('open');
                parent.find('li.is-expanded').removeClass('is-expanded');
                parent_li.addClass('is-expanded');
            });
        }
        if (checkElement.is(slideMenuSelector)) {
            e.preventDefault();
        }
    });

    // To close the sub menu dropdown by clicking on inner content
    $('.hor-content').on('click', function () {
        $('.side-menu li').each(function () {
            $('.side-menu ul.open').slideUp(300)
            $(this).parent().removeClass("is-expanded");
            $(this).parent().parent().removeClass("open");
            $(this).parent().parent().prev().removeClass("is-expanded");
            $(this).parent().parent().parent().removeClass("is-expanded");
            $(this).parent().parent().parent().parent().removeClass("open");
            $(this).parent().parent().parent().parent().parent().removeClass("is-expanded");
        })
    })
}

function HorizontalHovermenu() {
    let value = document.querySelector('body').classList.contains('horizontal-hover')
    if (value && window.innerWidth >= 768) {
        $("[data-bs-toggle='slide']").off('click');
        $("[data-bs-toggle='sub-slide']").off('click');
        $("[data-bs-toggle='sub-slide2']").off('click');
        slideClick();
    } else {
        menuClick();
    }
}
HorizontalHovermenu();

// for Icon-text Menu
//icontext();

// default layout
hovermenu();


// ______________HOVER JS start
function hovermenu() {
    $(".app-sidebar").hover(function () {
        if ($('body').hasClass('sidenav-toggled')) {
            $('body').addClass('sidenav-toggled-open');
        }
    }, function () {
        if ($('body').hasClass('sidenav-toggled')) {
            $('body').removeClass('sidenav-toggled-open');
        }
    });
}
// ______________HOVER JS end

// ______________ICON-TEXT JS start
function icontext() {
    $(".app-sidebar").off("mouseenter mouseleave");

    $(document).on('click', ".app-sidebar", function (event) {
        if ($('body').hasClass('sidenav-toggled') == true) {
            $('body').addClass('sidenav-toggled-open');
        }
    });

    $(document).on('click', ".app-content", function (event) {
        $('body').removeClass('sidenav-toggled-open');
    });

    //Mobile menu
    jQuery(document).ready(function ($) {
        var alterClass = function () {
            var ww = document.body.clientWidth;
            if (ww < 992) {
                $('body').removeClass('sidenav-toggled');
            } else if (ww >= 991) {
                $('body').addClass('sidenav-toggled');
            };
        };
        $(window).resize(function () {
            alterClass();
        });
        //Fire it when the page first loads:
        alterClass();
    });

}

// ______________ICON-TEXT JS end


let slideLeft = document.querySelector(".slide-left");
let slideRight = document.querySelector(".slide-right");
slideLeft.addEventListener("click", () => {
    slideClick()
}, true)
slideRight.addEventListener("click", () => { slideClick() }, true)

// used to remove is-expanded class and remove class on clicking arrow buttons
function slideClick() {
    let slide = document.querySelectorAll(".slide");
    let slideMenu = document.querySelectorAll(".slide-menu");
    slide.forEach((element, index) => {
        if (element.classList.contains("is-expanded") == true) {
            element.classList.remove("is-expanded")
        }
    });
    slideMenu.forEach((element, index) => {
        if (element.classList.contains("open") == true) {
            element.classList.remove("open");
            element.style.display = "none";
        }
    });
}

// horizontal arrows
var sideMenu = $(".side-menu");
var slide = "100px";

let menuWidth = document.querySelector('.horizontal-main')
let menuItems = document.querySelector('.side-menu')

$(window).resize(
    () => {
		let marginLeftValue = Math.ceil(window.getComputedStyle(menuItems).marginLeft.split('px')[0]);
        let marginRightValue = Math.ceil(window.getComputedStyle(menuItems).marginRight.split('px')[0]);
		let check = menuItems.getBoundingClientRect().width + (0 -  menuWidth?.offsetWidth);
		// to check and adjst the menu on screen size change
        if($('body').hasClass('ltr')){
            if(marginLeftValue > -check == false && menuWidth?.offsetWidth < menuItems.scrollWidth ){
                sideMenu.stop(false, true).animate({
                    marginLeft: -check
                }, {
                    duration: 400
                })
            }
            else{
                sideMenu.stop(false, true).animate({
                    marginLeft: 0
                }, {
                    duration: 400
                })
            }

            //
            if( menuWidth?.offsetWidth > menuItems.scrollWidth){
                $("#slide-left").addClass("d-none");
                $("#slide-right").addClass("d-none");
            }
            else if(marginLeftValue == 0){
                $("#slide-left").addClass("d-none");
                $("#slide-right").removeClass("d-none");
            }
            else if(marginLeftValue >= -check){
                $("#slide-right").addClass("d-none");
                $("#slide-left").removeClass("d-none");
            }
        }
        else{
            if(marginRightValue > -check == false && menuWidth?.offsetWidth < menuItems.scrollWidth ){
                sideMenu.stop(false, true).animate({
                    marginRight: -check
                }, {
                    duration: 400
                })
            }
            else{
                sideMenu.stop(false, true).animate({
                    marginRight: 0
                }, {
                    duration: 400
                })
            }
            //
            if( menuWidth?.offsetWidth > menuItems.scrollWidth){
                $("#slide-left").addClass("d-none");
                $("#slide-right").addClass("d-none");
            }
            else if(marginRightValue == 0){
                $("#slide-left").addClass("d-none");
                $("#slide-right").removeClass("d-none");
            }
            else if(marginRightValue >= -check){
                $("#slide-right").addClass("d-none");
                $("#slide-left").removeClass("d-none");
            }
        }
        checkHoriMenu()
    }
)

function checkHoriMenu(){
    let menuWidth = document.querySelector('.horizontal-main')
    let menuItems = document.querySelector('.side-menu')
	$("#slide-left").addClass("d-none");
	let marginLeftValue = Math.ceil(window.getComputedStyle(menuItems).marginLeft.split('px')[0]);
    let marginRightValue = Math.ceil(window.getComputedStyle(menuItems).marginRight.split('px')[0]);
	let check = menuItems.getBoundingClientRect().width + (0 -  menuWidth?.offsetWidth);
	let body = document.querySelector('body').classList.contains('ltr')

	if( menuWidth?.offsetWidth > menuItems.scrollWidth){
		$("#slide-left").addClass("d-none");
		$("#slide-right").addClass("d-none");
	}
	else if(marginLeftValue >= 0 && body == true){
		$(".ltr #slide-left").addClass("d-none");
	}
	else if(marginLeftValue <= -check && body == true){
		$(".ltr #slide-right").addClass("d-none");
	}
	else if(marginRightValue >= 0 && body == false){
		$(".rtl #slide-left").addClass("d-none");
	}
	else if(marginRightValue <= -check && body == false){
		$(".rtl #slide-right").addClass("d-none");
	}
	else {
		$(".ltr #slide-left").removeClass("d-none");
		$(".ltr #slide-right").removeClass("d-none");
	}
}
checkHoriMenu();
$(document).on("click", ".ltr #slide-left", function() {
	let marginLeftValue = Math.ceil(window.getComputedStyle(menuItems).marginLeft.split('px')[0]);
	let check = menuItems.getBoundingClientRect().width + (0 -  menuWidth?.offsetWidth);


	 if(marginLeftValue < 0){
		sideMenu.stop(false, true).animate({
            // marginRight : 0,
            marginLeft: "+=" + slide
        }, {
            duration: 400
        })
		$("#slide-right").removeClass("d-none");
	}
	// else{
	// 	$("#slide-left").addClass("d-none");
	// }

	if(marginLeftValue >= 0){
		$("#slide-left").addClass("d-none");
		sideMenu.stop(false, true).animate({
            // marginRight : 0,
            marginLeft: 0
        }, {
            duration: 400
        })
	}
	// to remove dropdown when clicking arrows in horizontal menu
	let subNavSub = document.querySelectorAll('.sub-nav-sub');
	subNavSub.forEach((e) => {
		e.style.display = '';
	})
	let subNav = document.querySelectorAll('.nav-sub')
	subNav.forEach((e) => {
		e.style.display = '';
	})
	//
});
$(document).on("click", ".ltr #slide-right", function() {
	let menuWidth = document.querySelector('.horizontal-main')
	let marginLeftValue = Math.ceil(window.getComputedStyle(menuItems).marginLeft.split('px')[0]);
	let check = menuItems.scrollWidth + (0 -  menuWidth?.offsetWidth);
    console.log(menuItems.scrollWidth);
	if(marginLeftValue > -check){
		sideMenu.stop(false, true).animate({
			// marginLeft : 0,
			marginLeft: "-=" + slide
		}, {
			duration: 400
		})
	}
	else{
		$("#slide-right").addClass("d-none");
	}

	if(marginLeftValue != 0){
		$("#slide-left").removeClass("d-none");
	}
	// to remove dropdown when clicking arrows in horizontal menu
	let subNavSub = document.querySelectorAll('.sub-nav-sub');
	subNavSub.forEach((e) => {
		e.style.display = '';
	})
	let subNav = document.querySelectorAll('.nav-sub')
	subNav.forEach((e) => {
		e.style.display = '';
	})
	//
});

$(document).on("click", ".rtl #slide-left", function() {
    let marginRightValue = Math.ceil(window.getComputedStyle(menuItems).marginRight.split('px')[0]);
	let check = menuItems.getBoundingClientRect().width + (0 -  menuWidth?.offsetWidth);


	 if(marginRightValue < 0){
		sideMenu.stop(false, true).animate({
            // marginRight : 0,
            marginLeft: 0,
            marginRight: "+=" + slide
        }, {
            duration: 400
        })
		$("#slide-right").removeClass("d-none");
	}
	else{
		$("#slide-left").addClass("d-none");
	}

	if(marginRightValue >= 0){
		$("#slide-left").addClass("d-none");
		sideMenu.stop(false, true).animate({
            // marginRight : 0,
            marginLeft: 0
        }, {
            duration: 400
        })
	}
	// to remove dropdown when clicking arrows in horizontal menu
	let subNavSub = document.querySelectorAll('.sub-nav-sub');
	subNavSub.forEach((e) => {
		e.style.display = '';
	})
	let subNav = document.querySelectorAll('.nav-sub')
	subNav.forEach((e) => {
		e.style.display = '';
	})
	//
});
$(document).on("click", ".rtl #slide-right", function() {
	let menuWidth = document.querySelector('.horizontal-main')
    let marginRightValue = Math.ceil(window.getComputedStyle(menuItems).marginRight.split('px')[0]);
	let check = menuItems.scrollWidth + (0 -  menuWidth?.offsetWidth);
	if(marginRightValue > -check){
		sideMenu.stop(false, true).animate({
			// marginLeft : 0,
			marginLeft: 0,
			marginRight: "-=" + slide
		}, {
			duration: 400
		})

	}
	else{

		$("#slide-right").addClass("d-none");
	}

	if(marginRightValue != 0){
		$("#slide-left").removeClass("d-none");
	}
	// to remove dropdown when clicking arrows in horizontal menu
	let subNavSub = document.querySelectorAll('.sub-nav-sub');
	subNavSub.forEach((e) => {
		e.style.display = '';
	})
	let subNav = document.querySelectorAll('.nav-sub')
	subNav.forEach((e) => {
		e.style.display = '';
	})

	//




});
