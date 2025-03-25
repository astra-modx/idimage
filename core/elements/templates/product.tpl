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



{$modx->runSnippet('idImageSimilar', [
    'min_scope' => 65
])}

{if $modx->getPlaceholder('idimage.ids')}
   {$modx->runSnippet('msProducts', [
       'tpl' => '@FILE chunks/catalog/product.row.tpl',
       'resources' => $ids,
       'sortby' => "FIELD(msProduct.id, {$ids})",
       'parents' => 0,
   ])}
{/if}
                <br>
                [[!msProducts?
                    &resources=`[[+idimage.ids]]`
                    &sortby=`FIELD(msProduct.id, [[+idimage.ids]])`
                    &tpl=`@FILE chunks/catalog/product.row.tpl`
                    &parents=`0`
                ]]
            </div>
        </div>
    </div>
{/block}
