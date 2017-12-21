$(document).ready(function() {
    $(window).off('popstate').on('popstate', function() {
        $( location ).attr("href", $(location).attr('href'));
    });

    $('.menu').on('click', '.js-category', function (e) {
        handleCategoryOrProductClick(e);
    });

    $('.js-product').click(function (e) {
        handleCategoryOrProductClick(e);
    });
});