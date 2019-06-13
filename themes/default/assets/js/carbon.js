$(window).on("load", function() {



}),$(document).ready(function () {
    /**
     * Sidebar Dropdown
     */

     $("#toggle-menu").click(function(e) {
       e.preventDefault(), $("#main").toggleClass("toggled")
     })

    $('.nav-dropdown-toggle').on('click', function (e) {
        e.preventDefault();
        $(this).parent().toggleClass('open');
    });

    // open sub-menu when an item is active.
    $('ul.nav').find('a.active').parent().parent().parent().addClass('open');

    /**
     * Sidebar Toggle
     */
    $('.sidebar-toggle').on('click', function (e) {
        e.preventDefault();
        $('body').toggleClass('sidebar-hidden');
    });

    /**
     * Mobile Sidebar Toggle
     */
    $('.sidebar-mobile-toggle').on('click', function () {
        $('body').toggleClass('sidebar-mobile-show');
    });

    $(".pr.animated").progressPie({
        mode:$.fn.progressPie.Mode.COLOR,
        strokeWidth: 1,
        ringWidth: 3,
        animate: {
            dur: "3s"
        }
    });

    $(".pp.animated").progressPie({
        mode:$.fn.progressPie.Mode.COLOR,
        animate: true
    });


});


(function() {
    $(document).ready(function() {

        $('#navbox-trigger').click(function() {
            return $('#navigation-bar').toggleClass('navbox-open');
        });
        $('#mobi-navbox-trigger').click(function() {
            return $('#navigation-bar').toggleClass('navbox-open');
        });

        return $(document).on('click', function(e) {
            var $target;
            $target = $(e.target);
            if (!$target.closest('.navbox').length && !$target.closest('#navbox-trigger').length) {
                return $('#navigation-bar').removeClass('navbox-open');
            }
        });
    });

    setInterval(function() {
        $('#date').load(' #date');

    }, 1000);

}.call(this));

