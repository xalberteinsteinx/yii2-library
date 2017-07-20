$(document).ready(function () {

    /*FIND WIDGET*/
    var widget = $('#input-tree');
    var currentCategoryId = widget.attr('data-current-category');

    autoOpen(currentCategoryId);

    /*OPEN ON CLICK*/
    widget.on('click', '.category-toggle.fa-plus', (function () {

        $(this).removeClass('fa-plus');
        $(this).addClass('fa-minus');
        var thisField= $(this).siblings('div');

        var categoryId = $(thisField).children('input').val();

        if ($(thisField).siblings('ul')) {
            $(thisField).siblings('ul').show(300);
        }
    }));

    /*CLOSE ON CLICK*/
    widget.on('click', '.category-toggle.fa-minus', (function () {
        $(this).removeClass('fa-minus');
        $(this).addClass('fa-plus');

        var thisField= $(this).siblings('div');

        var categoryId = $(thisField).children('input').val();

        if ($(thisField).siblings('ul')) {
            $(thisField).siblings('ul').hide(300);
        }
    }));
});


function autoOpen(currentCategoryId) {

    var currentCategoryInput = $('input[value=' + currentCategoryId + ']');
    var currentCategoryUl = $(currentCategoryInput).parents(".input-tree-ul");
    currentCategoryUl.each(function(i) {
        $(currentCategoryUl[i]).show();

        var button = $(currentCategoryUl[i]).siblings('p');
        $(button).removeClass('fa-plus');
        $(button).addClass('fa-minus');
    });
}