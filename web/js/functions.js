function handleCategoryOrProductClick(e) {
    e.preventDefault();

    var $productsWrapper = $('.products-wrapper');
    var loader = $('.js-loader-wrapper').html();
    var url = $(e.currentTarget).data('url');

    $productsWrapper.html(loader);
    window.history.pushState("", "", url);

    $.ajax({
        url: url,
        type: 'POST',
        success: function (data) {
            $productsWrapper.html(data.products);
        }
    }).then(function () {
        $('.js-product').click(function (e) {
            handleCategoryOrProductClick(e);
        });
    });
}