<div id="idimage-panel-sync-stat">
    <div class="idimage-wrapper-help">
        <div class="idimage-row-help">
                <span class="idimage-btn x-btn x-btn-small x-btn-icon-small-left  x-btn-noicon" onclick="indexedPoll(true)">
                 <button type="button" class=" x-btn-text">
                         <i class=" icon icon-play"></i> Обновить статистику
                 </button>
                </span>

            <div class="idimage-stat-section">
                <h2 style="margin-bottom: 20px">Статистика</h2>
                <div class="idimage-stat-text">Всего товаров: <strong>[[+stat.total]] шт.</strong>
                    [[+stat.total_error:isnot=`0`:then=`<span style="color: red"> (С ошибкой: <strong>[[+stat.total_error]] шт.</strong>)</span>`]]
                </div>
                <div class="idimage-stat-text idimage-status-color-completed">С похожими: <strong>[[+stat.total_similar]] шт.</strong></div>

                <div class="idimage-stat-text">Товаров с векторами: <strong>[[+stat.embedding.all]] шт.</strong></div>
                <div class="idimage-stat-text">Товаров без векторов: <strong>[[+stat.embedding.empty]] шт.</strong></div>

                <div class="idimage-stat-text">Товаров для индексации: <strong>[[+stat.indexed.all]] шт.</strong></div>
                [[-
                <div class="idimage-stat-text">Проиндексировано: <strong>[[+stat.indexed.completed]] шт.</strong></div>
                ]]
            </div>


            <div class="idimage-stat-section">
                <h3>1: Создание товаров</h3>
                <span class="idimage-btn x-btn x-btn-small x-btn-icon-small-left primary-button x-btn-noicon" onclick="productCreation()">
                     <button type="button" class=" x-btn-text">
                             <i class=" icon icon-play"></i> Создать товары
                     </button>
                </span>
                <p><em>Все товары с изображениями будут созданы для получения векторов и дальнейшей индексации.</em></p>

            </div>
            <div class="idimage-stat-section">
                <h3>2: Получение векторов</h3>

                <span class="idimage-btn x-btn x-btn-small x-btn-icon-small-left primary-button x-btn-noicon" onclick="apiGetEmbedding()">
                     <button type="button" class=" x-btn-text">
                             <i class=" icon icon-play"></i> Получить вектора
                     </button>
                </span>
                <p><em>По API будут загружены векторы из сервиса IDimage. Для работы требуется положительный баланс в сервисе.</em></p>

            </div>
            <div class="idimage-stat-section">
                <h3>3: Индексация</h3>
                <span class="idimage-btn x-btn x-btn-small x-btn-icon-small-left primary-button x-btn-noicon" onclick="indexedProducts(true)">
                 <button type="button" class=" x-btn-text">
                         <i class=" icon icon-play"></i> Индексировать товары
                 </button>
                </span>
                <p><em>По завершении индексации каждый товар получает список похожих на него товаров по векторам полученным из сервиса IDimage.</em></p>
            </div>
            <br>
            <div class="idimage-stat-section">
                <h2>Информация о системе</h2>
                <br>
                <span class="idimage-btn x-btn x-btn-small x-btn-icon-small-left x-btn-noicon" onclick="apiBalance(true)">
                     <button type="button" class=" x-btn-text">
                             <i class=" icon icon-play"></i> Запросить баланс
                     </button>
                </span>
                <p><em>Из сервиса idimage вернется текущий баланс аккаунта</em></p>
            </div>

            <div class="idimage-stat-section">
                <div class="idimage-row-help-check">
                    <div class="idimage-availability">
                        <span class="idimage-availability-name">Получение векторов:</span>
                        [[+enable:is=`1`:then=`
                        <span class="idimage-status success">Включено</span>
                        `:else=`
                        <span class="idimage-status error">Отключено</span>
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
                        <span class="idimage-availability-name">Минимальная версия php 7.4:</span>
                        [[+php:is=`1`:then=`
                        <span class="idimage-status success">[[+php_current]]</span>
                        `:else=`
                        <span class="idimage-status error">Несовместимая версия [[+php_current]]</span>
                        `]]
                    </div>
                </div>
            </div>


            <div class="idimage-stat-section">

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

