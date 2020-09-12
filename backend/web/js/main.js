$(document).ready(function() {

    // set activity filter in current page
    (function () {
        if (location.search === "")
            return;

        // найти последние 2 гет-параметра в поисковой строке (sort-field и sort-type)
        var searchStr = location.search;
        var searchParams = searchStr.substring(1, searchStr.length).split('&');
        if (!searchParams.length || searchParams.length < 2)
            return;

        // получить поля, по которым была сортировка
        var sortFieldArr = searchParams[searchParams.length - 2].split('=');
        var sortTypeArr = searchParams[searchParams.length - 1].split('=');
        if (sortFieldArr.length < 2 || sortTypeArr.length < 2)
            return;
        var sortField = sortFieldArr[1];
        var sortType = sortTypeArr[1];

        // добавить класс активности
        $('#' + sortField + ' .' + sortType).addClass('active');
    })();

    // открытие/закрытие попап-окна
    $(document).on('click', '.custom-popup-open', function (event) {
        event.preventDefault();
        var container = $(this).find('.custom-popup-container');
        if (container.is(":hidden")) {
            $('body').addClass('stop-scroll');
            container.show();
        }
    });
    $(document).on('click', '.custom-popup-container', function (event) {
        event.preventDefault();
        event.stopPropagation();
        $('body').removeClass('stop-scroll');
        $('.custom-popup-container').removeAttr('style');
    });

    // при выборе radio-button прокидываем параметр и перезагружаем
    $('.radio-action').click(function() {
        const getParam = 'radio';
        const radioButtonId = $(this).attr('id');

        var searchStart = '';
        if (window.location.search.indexOf('?') === -1) {
            searchStart = '?a=1'
        }
        var re = new RegExp('&*' + getParam + '=[a-zA-Z-]+', 'g');
        var newHref = window.location.pathname + window.location.search.replace(re, '');
        window.location.href = newHref + searchStart + '&' + getParam + '=' + radioButtonId;
    });
    (function () {
        if (location.search === '') {
            return;
        }
        let searchParams = new URLSearchParams(window.location.search);
        searchParams.forEach(function(value, key) {
            if (key !== 'radio') {
                return;
            }
            $("input#" + value).each(function (index, obj) {
                $(this).prop('checked', true);
            });
        });
    })();

    // показ-скрытие блоков
    (function () {
        $(".box-control.close-box[data-box='membership-fee']").hide();
        $(".box-control.close-box[data-box='payment-schedule']").hide();
    })();
    $('.box-control').click(function() {
        var dataBoxAttr = $(this).attr('data-box');
        var box = $('#' + dataBoxAttr);
        if ($(this).hasClass('open-box')) {
            box.addClass('show');
            $(".box-control.open-box[data-box='" + dataBoxAttr + "']").hide();
            $(".box-control.close-box[data-box='" + dataBoxAttr + "']").show();
        } else if($(this).hasClass('close-box')) {
            box.removeClass('show');
            $(".box-control.open-box[data-box='" + dataBoxAttr + "']").show();
            $(".box-control.close-box[data-box='" + dataBoxAttr + "']").hide();
        }
    });
    // показ блока проверки документов
    $(document).on('click', '.container-link', function () {
        var dataBoxAttr = $(this).attr('data-parent');
        $(this).hide();
        $(".container-fluid[data-child='" + dataBoxAttr + "']").show();
    });
    // показ строк в табл. ипотечных платежей
    $(document).on('click', '#opening-hide-row', function (event ) {
        event.preventDefault();
        $('.table .tbody.tbody-default-hide').removeClass('tbody-default-hide');
        $('.tbody.tbody-separator').hide();
    });
});
