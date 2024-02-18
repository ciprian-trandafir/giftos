$(document).ready(function () {
    $('body')
        .on('click', '.btn-add-to-cart', function () {
            const $button = $(this);
            $button.attr('disabled', true);

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajax_url,
                data: {
                    ajax: true,
                    action: 'addToCart',
                    id: $button.attr('data-id'),
                },
                success: function (data) {
                    if (!data.success) {
                        alert(data.error);

                        return;
                    }

                    const $message = $('.add-to-cart-success');
                    $message.show(10);

                    setTimeout(function () {
                        $button.attr('disabled', false);
                        $message.hide(100);
                    }, 2000);
                }
            });
        })
});
