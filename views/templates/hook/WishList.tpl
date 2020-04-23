
<div class="my-wish-list-container">
    {if $inList === false}
        <button class="btn-smt-list" type="button" data-url="{$smarty.server.REQUEST_URI}">
            <div class="heart-in heart-list"></div>
        </button>
        <input type="hidden" class="prod-name-link-post" value="{$product.name}|{$product.link}">
        <input type="hidden" class="req-uri" value="{$smarty.server.REQUEST_URI}">
    {elseif $inList === true}
        <a href="{$urlFrontController}">
            <div class="heart-container">
                <div class="heart-out heart-list"></div>
            </div>
        </a>
    {/if}
</div>




