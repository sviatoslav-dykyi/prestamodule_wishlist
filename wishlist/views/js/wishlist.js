$(document).ready(function () {
    /*hookDisplayProductActions*/    

    // handle add btn
    $('.btn-smt-list').click(function() {
        $.ajax({
            url: this.dataset.url,
            method: 'POST',
            cache: false,
            data: {
                name: $('.prod-name-link-post').val()
            },
            dataType: 'html',
            success(data) {
                document.location.reload(true);
            }
        });
    });   


    /*Front-controller-show*/

    $('.del-outside').hide();
    prepareNotGroupMode();

    // handle delete btn
    $('.content-list-module').click(function(e) {
        let id = [];
        if ($(e.target).is('.fa-times')) {
            id.push([$(e.target).closest('span')[0].dataset.id]);
            del_ajax(id);

        } else if ($(e.target).is('.del-outside')) {
            $('.magic-checkbox').each(function() {
                if (this.checked) id.push(this.dataset.id);
            });
            if (id.length) {
                del_ajax(id);
            } else{
                alert('Chose some product!');
            }
        }
    });

    function del_ajax(id=[]) {
        id = id.join(',');
        $.ajax({
            url: $('#btn-sbmt-to-cart')[0].dataset.url,
            method: 'POST',
            cache: false,
            data: {
                id
            },
            dataType: 'html',
            success(data) {
                //alert(data);
                document.location.reload(true);
            }
        });
    }

    // handle group checkbox actions
    $('.magic-checkbox').change(function() {
        if (!this.checked) {
            let hideDelBtn = true;
            $('.magic-checkbox').each(function() {
                if (this.checked) {
                    hideDelBtn = false;
                    return;
                }
            });
            if (hideDelBtn) {
                $('.del-outside').hide(100);
            }
            toggleAction();
        } else {
            $('.del-outside').show(100);
            toggleAction();
        }
    });

    function toggleAction() {
        let count = 0;
        let totalPrice = 0;
        let idsToCart = [];
        $('.magic-checkbox').each(function() {
            if (this.checked) {
                count++;
                price = $(this).closest('.module-item-wrapper-grid').find('.module-container-price').text();
                totalPrice += parseFloat(price);
                console.log($(this).closest('.module-item-wrapper-grid').find('.add-to-cart')[0]);
                let btn = $(this).closest('.module-item-wrapper-grid').find('.add-to-cart')[0];
                if (btn) {
                    idsToCart.push(btn.dataset.id);
                }
            }
        });
        if (count > 0) {
            $('.ids-to-cart').val(idsToCart.join(','));
            $('.list__products-amount').text(count);
            $('.list__products-price').text(totalPrice.toFixed(2));
            count == 1 ? $('.item-correct').text('item') : $('.item-correct').text('items');
        } else {
            prepareNotGroupMode();
        }
    }

    // handle add-to-cart btn
    /*$('.add-to-cart').click(function() {
        //$(this).css('visibility', 'hidden');
        //$(this).hide(250);
        setTimeout(() => {

            console.log($('#blockcart-modal')[0]);
            $('#blockcart-modal').on('hide.bs.modal', function () {
                document.location.reload(true);
            });

        }, 1800);
    });*/
    $('#btn-sbmt-to-cart').click(function() {
        let groupMode = false;
        $('.magic-checkbox').each(function (){
           if (this.checked) {
               groupMode = true;
               return;
           }
        });
        if (!groupMode) {
            let idsToCart = [];
            $('.add-to-cart').each(function() {
                idsToCart.push($(this)[0].dataset.id);
            });
            $('.ids-to-cart').val(idsToCart.join(','));
        }
    });

    function prepareNotGroupMode() {
        let listProductsAmount = 0;
        let listProductsPrice = 0;
        $('.add-to-cart').each(function() {
            listProductsAmount++;
            let price = $(this).closest(".module-container").find('.module-container-price').text();
            listProductsPrice += parseFloat(price);
        });
        $('.list__products-amount').text(listProductsAmount);
        $('.list__products-price').text(listProductsPrice.toFixed(2));
        listProductsAmount == 1 ? $('.item-correct').text('item') : $('.item-correct').text('items');
    }

    /*hookDisplayProductListReviews*/

    let wishIds = $('.wish-list-id-d')[0].dataset.listIds.split(',');
    let controller = $('.wish-list-id-d')[0].dataset.controller;
    console.log($('.wish-list-id-d')[0]);
    if (controller != 'product') {
        $('.my-wish-list-container-mini').each(function() {
            let id = $(this).closest('.product-miniature')[0].dataset.idProduct;
            let title = $(this).closest('.product-description').find('.product-title').text();
            let href = $(this).closest('.thumbnail-container').find('a')[0].href;
            $(this).find('.prod-name-link-post').val(title + '|' + href);
            $(this).find('.prod-id-post').val(id);

            if (~wishIds.indexOf(id)) {
                $(this).find('.wish-container-mini-anchor').show();
            } else {
                $(this).find('.wish-container-mini-btn').show();
            }
        })
    }

});