$(document).ready(function () {
    function updateProduct(id, qty) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_url,
            data: {
                ajax: true,
                action: 'updateProduct',
                id,
                qty,
            },
            success: function (data) {
                if (!data.success) {
                    alert(data.error);

                    return;
                }

                $('.cart-products').html(data.products);
                $('.cart-totals').html(data.totals);
            }
        });
    }

    $('body')
        .on('change', '.product-line-qty', function () {
            const enteredValue = $(this).val();

            if ($.isNumeric(enteredValue) && Math.floor(enteredValue) == enteredValue) {
                if (enteredValue < 1) {
                    $(this).val(1);
                }

                if (enteredValue > parseInt($(this).attr('data-qty'))) {
                    $(this).val($(this).attr('data-qty'));
                }

                updateProduct($(this).closest('.product-line-item').attr('data-id'), $(this).val());
                return;
            }

            $(this).val(1);
            updateProduct($(this).closest('.product-line-item').attr('data-id'), $(this).val());
        })
        .on('click', '.product-line-delete', function () {
            const $button = $(this);

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajax_url,
                data: {
                    ajax: true,
                    action: 'deleteProduct',
                    id: $button.closest('.product-line-item').attr('data-id'),
                },
                success: function (data) {
                    if (!data.success) {
                        alert(data.error);

                        return;
                    }

                    $('.cart-products').html(data.products);
                    $('.cart-totals').html(data.totals);
                }
            });
        });
});
