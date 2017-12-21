$(document).ready(function() {
    var $menu = $('.menu');
    var $productsWrapper = $('.products-wrapper');
    
    $menu.on('click', '.js-category', function (e) {
        e.preventDefault();

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
        });
    })
});