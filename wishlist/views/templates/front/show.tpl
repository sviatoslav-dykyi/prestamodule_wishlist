{extends 'page.tpl'}

{block content}

    <div class="content-list-module">
        <div class="row">
            <div class="col-md-6">
                <div class="alert alert-warning alert-wishlist" role="alert">
                    <p class="alert-text wishlist-p"><span class="list__products-amount"></span> <span class="item-correct">items</span>. Total amount: <span class="list__products-price"></span> ₴
                    </p>
                    <div class="form-cont-wishlist">
                        <form action="" method="post">
                            <input type="hidden" name="ids-to-cart" class="ids-to-cart" value="">
                            <input type="hidden" name="group-add-to-cart" value="">
                            <button id="btn-sbmt-to-cart" class="btn btn-primary light-button btn-sm pull-xs-right" data-url="{$smarty.server.REQUEST_URI}">Buy all now

                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="del-outside">Delete</div>
            </div>
            <div class="col-md-2">
                <div class="dropdown">
                    <button class="btn btn-tertiary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-// haspopup="true" aria-expanded="false">
                    Sorting by:
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <a class="dropdown-item" href="{$urlFrontController}?sort=relevance">Relevance</a>
                        <a class="dropdown-item" href="{$urlFrontController}?sort=title">Title</a>
                        <a class="dropdown-item" href="{$urlFrontController}?sort=lowest">Price (lowest first)</a>
                        <a class="dropdown-item" href="{$urlFrontController}?sort=highest">Price (highest first)</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row wish-row">
            {if $wishList}
                {foreach from=$wishList item=element}
                    <div class="col-sm-3">
                        <div class="module-item-wrapper-grid">
                            <div class="magic-box-cont">
                                <input class="magic-checkbox" type="checkbox" name="layout" id="{$element.name}" value="option" data-id="{$element.product_id}">
                                <label for="{$element.name}"></label>
                            </div>
                            <div class="module-item-heading-grid" data-toggle="modal" data-target="#module-modal-read-more-webcallback">
                                <a href="{$element.product_link}">
                                    <img class="module-logo-thumb-grid" src="{$urlImg}{$element.product_id}-home_default/{$element.link_rewrite}.jpg" alt="Call Back">
                                    <div class="wishlist-prod-title">
                                        <h3 class="text-ellipsis module-name-grid" data-toggle="tooltip" data-placement="top" title="" data-original-title="Call Back">
                                            {$element.name}
                                        </h3>
                                    </div>                                    
                                </a>
                            </div>
                            <span class="del-ico" data-id="{$element.product_id}"><i class="fa fa-times" aria-hidden="true"></i></span>
                            <div class="module-container">
                                <div class="module-container-price">
                                    {number_format($element.price, 2)} ₴
                                </div>
                                <div class="module-quick-action-grid clearfix">
                                    <form action="" method="post">
                                        <input type="hidden" name="token" value="{*{$static_token}*}">
                                        <input type="hidden" name="ids-to-cart" value="{$element.product_id}">
                                        <input type="hidden" name="id_customization" value="{*{$product.id_customization}*}">
                                        <div class="add-cont">
                                            {if $element.id_cart != $currentCartId}
                                                <button
                                                        class="btn btn-primary add-to-cart"
                                                        type="submit"
                                                        data-id="{$element.product_id}"
                                                >Buy now
                                                    <i class="material-icons shopping-cart">&#xE547;</i>
                                                </button>
                                            {else}
                                                Item in cart
                                            {/if}
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                {/foreach}
            {/if}
        </div>
    </div>

    {* <form action="{$smarty.server.PHP_SELF}?fc=module&module=mymodule&controller=message&id_lang=1"
           method="post">
         <label>
             <input type="text" name="name" id="name" class="form-control">
         </label>

         <label>
             <input type="submit" name="submit" id="submit" class="form-control">
         </label>
     </form>*}

{/block}