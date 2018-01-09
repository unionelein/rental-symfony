function handleCategoryOrProductClick(e) {
    e.preventDefault();

    var $productsWrapper = $('.products-wrapper');
    var loader = $('.js-loader-wrapper').html();
    var url = $(e.currentTarget).data('url');

    $productsWrapper.html(loader);
    window.history.pushState("", "", url);

    var offset = $('body').offset();

    if ($(e.currentTarget).hasClass('js-category')) {
        $('.js-category').removeClass('active-category');
        $(e.currentTarget).addClass('active-category');
    }

    $.ajax({
        url: url,
        type: 'POST',
        success: function (data) {
            $productsWrapper.html(data.products);
            window.scrollTo(offset.left, offset.top);
        }
    }).then(function () {
        $('.js-product').click(function (e) {
            handleCategoryOrProductClick(e);
        });

        $('.js-page').click(function (e) {
            handleCategoryOrProductClick(e);
        });
    });
}