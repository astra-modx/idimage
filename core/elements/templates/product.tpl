{extends 'file:templates/base.tpl'}
{block 'main'}
    <div id="content" class="main">
        <div class="row">
            <div class="col-md-12">
                {$modx->getChunk('msProduct.content')}
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">

                {var $ids = $modx->runSnippet('idImageClose', [
                    'min_scope' => 65
                ])}
                {if $ids}
                    {$modx->runSnippet('msProducts', [
                    'tpl' => '@FILE chunks/catalog/product.row.tpl',
                    'resources' => $ids,
                    'sortby' => "FIELD(msProduct.id, {$ids})",
                    'parents' => 0,
                    ])}

                {/if}
            </div>
        </div>
    </div>
{/block}
