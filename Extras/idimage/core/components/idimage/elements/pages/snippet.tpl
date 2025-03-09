<div class="idimage-row-help" style="width: 100%;">
    <div class="idimage-method-crontab" style="margin: 0">
        <h2>Сниппет для подключение</h2>
        <p>Разместите код на странице с товаром или в любом другом мест</p>
        <div class="idimage_help_command">
<pre class="idimage_help_command_pre">

{var $ids = $modx->runSnippet('idImageClose', [
    'pid' => $modx->resource->id,
    'min_scope' => 65,
    'limit' => 4
])}
{if $ids}
    {$modx->runSnippet('msProducts', [
        'resources' => $ids,
        'sortby' => "FIELD(msProduct.id, {$ids})",
        'parents' => 0,
    ])}
{/if}

</pre>
        </div>
        <h4>Параметры</h4>
        <div class=".x-grid3">
            <ul>
                <li>
                    <span><b>pid</b> - </span>
                    <span>id товара для которого нужно вернуть близкие изображения</span>
                </li>
                <li>
                    <span><b>min_scope</b> - </span>
                    <span>минимальная вероятность схожести изображения от 0 до 100. Чем меньше вероятность тем больше изображание будет
                                       отличаться от оригинала</span>
                </li>
                <li>
                    <span><b>max_scope</b> - </span>
                    <span>Максимальная вероятность от 100 до 0 (по умолчанию 100, если изображение схоже с оригиналом то вероятность будет 100%)
                                   </span>
                </li>
                <li>
                    <span><b>limit</b> - </span>
                    <span>кол-во возвращаемых изображений</span>
                </li>
            </ul>
        </div>
    </div>

</div>

