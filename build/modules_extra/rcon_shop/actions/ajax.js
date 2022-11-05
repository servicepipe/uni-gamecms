function onRconShopBuying() {
    let checkBox = $('#buy-checkbox');
    let btn = $('#buy-btn');
    let status = checkBox.attr("data-status");

    if (status == '2') {
        checkBox.prop('checked', true);
        checkBox.attr("data-status", "1");
        btn.removeClass("disabled");
        btn.attr('onclick', 'buyRconShopProduct();');
    } else {
        checkBox.prop('checked', false);
        checkBox.attr("data-status", "2");
        btn.addClass("disabled");
        btn.attr('onclick', '');
    }
    btn.focus();
}

function setRconShopProductTarif(id, price, title) {
    $('#tarif').val(id);
    $('#product-price').html(price);
    $('#product-title').html(title);
}

const RCON_SHOP_CONTROLLER = '../modules_extra/rcon_shop/actions/index.php';

function buyRconShopProduct() {
    onRconShopBuying();
    let params = {};

    for (let [name, value] of (new FormData($('#params').get(0)))) {
        params[name] = value;
    }

    let errorBlock = $('#error-result');
    let successBlock = $('#success-result');

    errorBlock.fadeOut(0);
    successBlock.fadeOut(0);

    ajax({
        controller: RCON_SHOP_CONTROLLER,
        data: {
            buy: true,
            tarif: $('#tarif').val(),
            product: $('#product').val(),
            ...params
        },
        success: (result) => {
            successBlock.fadeIn();
            $('#balance').html(result.shilings);
        },
        error: (result) => {
            if(result.errors.data) {
                errorBlock.fadeIn();
                errorBlock.html(result.errors.data);
            }
        },
        inputs: {
            title: 'category_title'
        },
        progress: true
    });
}