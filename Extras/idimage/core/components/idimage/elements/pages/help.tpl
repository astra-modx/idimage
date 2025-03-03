<div id="idimage-panel-sync-stat">
    <div class="idimage-wrapper-help">
        <div class="idimage-row-help">

            <div class="idimage-stat-section">
                <h2>Статистика</h2>
                <span class="idimage-btn x-btn x-btn-small x-btn-icon-small-left primary-button x-btn-noicon" onclick="indexedRunning(true)">
                 <button type="button" class=" x-btn-text">
                         <i class=" icon icon-play"></i> Запустить индексацию
                 </button>
                </span>
                <br>
                <span class="idimage-btn x-btn x-btn-small x-btn-icon-small-left primary-button x-btn-noicon" onclick="indexedProducts(true)">
                 <button type="button" class=" x-btn-text">
                         <i class=" icon icon-play"></i> Импортировать похожие товары
                 </button>
                </span>
            </div>
            <div class="idimage-stat-section">
                <h3>Товары</h3>
                <div class="idimage-stat-text">Всего товаров: <strong>[[+stat.total]] шт.</strong></div>
                <div class="idimage-stat-text idimage-status-color-completed">С похожими: <strong>[[+stat.closes]] шт.</strong></div>

                <span class="idimage-btn x-btn x-btn-small x-btn-icon-small-left primary-button x-btn-noicon" onclick="productCreation()">
                 <button type="button" class=" x-btn-text">
                         <i class=" icon icon-play"></i> Добавить товары в очередь
                 </button>
                </span>
            </div>
            <div class="idimage-stat-section">
                <h3>Очередь</h3>
                <div class="idimage-stat-text">В очередь: <strong>[[+stat.queue]] шт.</strong></div>
                <div class="idimage-stat-text"> Отправлено в сервис: <strong>[[+stat.send]] шт.</strong>
                    [[+stat.error:isnot=`0`:then=`<span style="color: red"> (С ошибкой: <strong>[[+stat.error]] шт.</strong>)</span>`]]
                </div>

                <span class="idimage-btn x-btn x-btn-small x-btn-icon-small-left primary-button x-btn-noicon" onclick="productQueueAdd()">
                <button type="button" class=" x-btn-text">
                    <i class=" icon icon-play"></i> Запустить очередь
                </button>
                </span>

                [[-
                <div class="idimage-stat-text">Получено похожих: <strong>[[+stat.completed]] шт.</strong></div>
                <span class="idimage-btn x-btn x-btn-small x-btn-icon-small-left primary-button x-btn-noicon" onclick="indexedProducts()">
                 <button type="button" class=" x-btn-text">
                         <i class=" icon icon-play"></i> [[%idimage_actions_indexed_products]]
                 </button>
             </span>]]
            </div>

            [[+cloud:is=`1`:then=`
            <div class="idimage-stat-section">
                <h3>Облачное хранилище</h3>
                <div class="idimage-stat-text">В очереди: <strong>[[+stat.cloud_queue]] шт.</strong></div>
                <div class="idimage-stat-text">Загружено в облако: <strong>[[+stat.cloud_upload]] шт.</strong></div>

                <span class="idimage-btn x-btn x-btn-small x-btn-icon-small-left danger-button x-btn-noicon" onclick="productUpload()">
                 <button type="button" class=" x-btn-text">
                         <i class=" icon icon-upload"></i>  Загрузка изображений в облако.
                 </button>
             </span>
            </div>
            `]]


            [[-
            <div id="schedule_service">
                <div id="schedule_service_status"></div>
                <div id="schedule_service_button_add">
                 <span class="x-btn x-btn-small x-btn-icon-small-left primary-button x-btn-noicon" onclick="checkAvailability()">
                     <button type="button" class=" x-btn-text">
                             <i class=" icon icon-play"></i> Проверить доступно
                     </button>
                 </span>
                </div>
            </div>
            <hr>
            ]]


            <div class="idimage-stat-section">
                <h3>Информация о системе</h3>
                <div class="idimage-row-help-check">
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

                    [[+cloud:is=`1`:then=`
                    <div class="idimage-availability">
                        <span class="idimage-availability-name">Загрузка в облачное хранилище:</span>
                        <span class="idimage-status success">Включена</span>
                    </div>
                    `]]

                    [[+cloud:is=``:then=`
                    <div class="idimage-availability">
                        <span class="idimage-availability-name">Глобальный доступ к изображениям:</span>
                        [[+validate_site_url:is=`1`:then=`
                        <span class="idimage-status success">Да</span>
                        `:else=`
                        <span class="idimage-status error">Отсутствует </span>
                        `]]
                    </div>
                    `]]
                </div>


                <div id="schedule_service">
                    <div id="schedule_service_status"></div>
                    <div id="schedule_service_button_add">
                 <span class="idimage-btn x-btn x-btn-small x-btn-icon-small-left primary-button x-btn-noicon" onclick="checkAvailability()">
                     <button type="button" class=" x-btn-text">
                             <i class=" icon icon-play"></i> Проверить доступно
                     </button>
                 </span>
                    </div>
                </div>

            </div>


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

