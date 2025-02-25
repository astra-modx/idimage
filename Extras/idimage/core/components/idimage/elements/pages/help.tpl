<div id="idimage-panel-home-div-help" style="display: none">
    <div id="modx-page-help-content-help" class=" container">
        <h2>Помощь</h2>
        <div id="contactus" style="width: 100%">

            <div class="idimage-wrapper-help">
                <div class="idimage-row-help">
                    <div class="idimage-method-crontab">
                        <h2>Быстрый старт</h2>


                        <h4>1: Создайте индекс</h4>
                        <p>Индексация -> Действия -> Создать версию</p>
                        <h4>2: Добавьте изображения из товаров</h4>
                        <p>Близкие изображения -> Действия -> Добавить изображения</p>

                        <h4>3: Отправка изображений</h4>
                        <p>Близкие изображения -> Действия -> Отправить в сервис</p>

                        <h4>4: Запустите индекс</h4>
                        <p>Индексация -> Иконка Play</p>

                        <h4>5: Дождитесь обновления</h4>
                        <p>Индексация -> Действия -> Получить обновления</p>

                        <h4>6: Загрузка близких изображений в товар</h4>
                        <p>Индексация -> Использовать версию</p>
                        <p>После чего к изображениям из списка "Близкие изображения" будет прикреплены схожие товары полученные во время индексации</p>

                        <h4>7: Разместит код на странице</h4>
                        <p>Ниже приведен код для размещения на странице</p>
                    </div>
                    <hr>
                    <div class="idimage-method-crontab">
                        <h2>Код для встравки на страницу</h2>
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
                <div class="idimage-row-help idimage-row-help-check">
                    <h2>Проверка доступности</h2>
                    <br>

                    <div>
                        <div class="idimage-availability">
                            <span class="idimage-availability-name">Сервис включен:</span>
                            [[+enable:is=`1`:then=`
                            <span class="idimage-status success">Да</span>
                            `:else=`
                            <span class="idimage-status error">Нет</span>
                            `]]
                        </div>

                        <div class="idimage-availability">
                            <span class="idimage-availability-name">Токен добавлен:</span>
                            [[+token:is=`1`:then=`
                            <span class="idimage-status success">Да</span>
                            `:else=`
                            <span class="idimage-status error">Нет</span>
                            `]]
                        </div>
                        <div class="idimage-availability">
                            <span class="idimage-availability-name">zip архиватор:</span>
                            [[+zip:is=`1`:then=`
                            <span class="idimage-status success">Доступен</span>
                            `:else=`
                            <span class="idimage-status error">Не доступен</span>
                            `]]
                        </div>

                        <div class="idimage-availability">
                            <span class="idimage-availability-name">Минимальная версия php 7.4:</span>
                            [[+php:is=`1`:then=`
                                <span class="idimage-status success">[[+php_current]]</span>
                            `:else=`
                                <span class="idimage-status error">Несовместимая версия [[+php_current]]</span>
                            `]]
                        </div>
                        <div class="idimage-availability">
                            <span class="idimage-availability-name">Глобальный доступ к изображениям:</span>
                            [[+validate_site_url:is=`1`:then=`
                            <span class="idimage-status success">Да</span>
                            `:else=`
                            <span class="idimage-status error">отсутствует (обратитесь в тех поддержку)</span>
                            `]]
                        </div>
                        [[+cloud:is=`1`:then=`
                        <div class="idimage-availability">
                            <span class="idimage-availability-name">Загрузка в облачное хранилище:</span>
                            <span class="idimage-status error">Нет</span>
                        </div>
                        `]]

                    </div>



                    <div id="schedule_service">
                        <hr>
                        <div id="schedule_service_status"></div>
                        <div id="schedule_service_button_add">
                            <span class="x-btn x-btn-small x-btn-icon-small-left primary-button x-btn-noicon" onclick="checkAvailability()">
                                <button type="button" class=" x-btn-text">
                                        <i class=" icon icon-play"></i> Проверить доступно
                                </button>
                            </span>
                        </div>
                    </div>

                    [[-
                    <div id="schedule_service">
                        <hr>
                        <div id="schedule_service_status"></div>
                        <div id="schedule_service_button_add">
                            <span class="x-btn x-btn-small x-btn-icon-small-left primary-button x-btn-noicon" onclick="scheduleCronTabAjax('add')">
                                <button type="button" class=" x-btn-text">
                                        <i class=" icon icon-play"></i> Запустить индексацию
                                </button>
                            </span>
                        </div>
                        <p>Вы можете выполнить все шаги автоматически, просто нажмите запустить индексацию, по завершению вам останеться только разместить
                            код на странице</p>
                        <p>Внимание!!! После запуска не уходите с этой страницы, чтобы процедура завершилась автоматически</p>
                    </div>]]

                    <hr>
                    <h3>Поддержка</h3>
                    <div class="idimage-contacts">
                        <div>
                           <a href="https://t.me/idimage" target="_blank">Telegram</a>
                        </div>
                        <div>
                           <a href="https://idimage.ru/" target="_blank">Cайт</a>
                        </div>
                        <div>
                           <a href="https://idimage.ru/api/documentation" target="_blank">Api documentation</a>
                        </div>
                    </div>


                </div>
            </div>

        </div>
    </div>

</div>
