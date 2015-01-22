/* global $ */

$(document).ready(function () {

    $('table.reporte th span').css('cursor', 'pointer');

    $('table.reporte th span').click(function (event) {
        event.preventDefault();
        $('form input[name="order_by"]').val($(this).attr('order_by'));
        $('form input[name="order_sort"]').val($(this).attr('order_sort'));
        $('form').submit();
    });

    $('td.subtotal[colspan]').parent().each(function(i) {
        if ($(this).size() == 1) {
            if ($(this).children(':eq(1)').html() != '&nbsp;') {
                $(this).children(':eq(1)').html('<span class="glyphicon glyphicon-minus-sign"></span> ' + $(this).children(':eq(1)').html());
                $(this).children(':eq(1)').css('cursor', 'pointer');
                $(this).addClass('tr_subtotal_' + i);
                $(this).children().addClass('tr_subtotal');
            }
        }
    });

    $('tr[class^="tr_subtotal_"]').each(function() {
        var clase = $(this).attr('class');
        $(this).nextUntil('tr[class^="tr_subtotal_"]').addClass(clase + '_data');
    })

    $('table.reporte td.tr_subtotal').click(function() {
        var icon_plus   = 'glyphicon-plus-sign',
            icon_minus  = 'glyphicon-minus-sign',
            clase       = $(this).parent().attr('class');
        var icon_expand = ($(this).parent().next().is(':visible')) ? icon_plus : icon_minus;

        $(this).children('span').removeClass(icon_minus).removeClass(icon_plus);
        $(this).children('span').addClass(icon_expand);
        $('tr.' + clase + '_data').toggle();
    });

});

/* ============================================================================================ */
/* Fixed Header                                                                                 */
/* ============================================================================================ */

(function($) {

$.fn.fixedHeader = function (options) {
    var config = {
        topOffset: 40,
        bgColor: '#FFF'
    };

    if (options){ $.extend(config, options); }

    return this.each( function() {
        var o = $(this);

        var $win = $(window)
        , $head = $('thead.header', o)
        , isFixed = 0;
        var headTop = $head.length && $head.offset().top - config.topOffset;

        function processScroll() {
            if (!o.is(':visible')) return;
            var i, scrollTop = $win.scrollTop();
            var t = $head.length && $head.offset().top - config.topOffset;
            if (!isFixed && headTop != t) { headTop = t; }
            if      (scrollTop >= headTop && !isFixed) { isFixed = 1; }
            else if (scrollTop <= headTop && isFixed) { isFixed = 0; }
            isFixed ? $('thead.header-copy', o).removeClass('hide')
                    : $('thead.header-copy', o).addClass('hide');
        }

        $win.on('scroll', processScroll);

        // hack sad times - holdover until rewrite for 2.1
        $head.on('click', function () {
            if (!isFixed) setTimeout(function () {  $win.scrollTop($win.scrollTop() - 47) }, 10);
        })

        $head.clone().removeClass('header').addClass('header-copy header-fixed').appendTo(o);

        var ww = [];
        o.find('thead.header > tr:first > th').each(function (i, h){
            ww.push($(h).width()+10);
        });
        $.each(ww, function (i, w){
            o.find('thead.header > tr > th:eq('+i+'), thead.header-copy > tr > th:eq('+i+')').css({width: w});
        });

        o.find('thead.header-copy').css({ margin:'0 auto',
                                        width: o.width(),
                                       'background-color':config.bgColor });
        processScroll();
    });
};

})(jQuery);

$(document).ready(function() {
    $('table.table-fixed-header').fixedHeader({topOffset: 0});
});