<div id="idimage-panel-sync-stat">
    <div class="idimage-wrapper-help">
        <div class="idimage-row-help">
            <hr>

            <div class="idimage-stat-section">
                <h2>Инструкция</h2>
                <div class="idimage-row-help-check">
                    <ol>
                        <li class="idimage-availability">
                            1: Создайте товары
                        </li>
                        <li class="idimage-availability">
                            2: Создайте задания
                        </li>
                        <li class="idimage-availability">
                            3: Отправьте задания в сервис idimage.ru
                        </li>
                        <li class="idimage-availability">
                            3.1: Загрузите изображений в idimage.ru (опционально, если нет доступа из глобального интернета к изображениям)
                        </li>
                        <li class="idimage-availability">
                            4: Дождитесь пока сервис завершит задания
                        </li>

                        <li class="idimage-availability">
                            5: Получите вектора из сервиса
                        </li>
                        <li class="idimage-availability">
                            6: Нажмите "Индексировать товары"
                        </li>
                        <li class="idimage-availability">
                            7: Добавьте сниппет на страницу с товаром
                        </li>
                    </ol>


                </div>
            </div>

            <div class="idimage-stat-section">
                <h2>Информация о системе</h2>
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
                <h2>CLI</h2>
                <div class="idimage-row-help-check">
                    Команды для запуска из под консоля ssh и добавления фоновых заданий
                </div>

                <div class="idimage_help_command">
<pre class="idimage_help_command_pre">
[[+crontabs]]
</pre>
                </div>
            </div>

            <div class="idimage-stat-section">
                <h2>Поддержка</h2>
                <div class="idimage-contacts">
                    <div>
                        <a href="https://t.me/idimage" target="_blank">Telegram</a>
                    </div>
                    <div>
                        <a href="https://idimage.ru/" target="_blank">[[%idimage_help_site]]</a>
                    </div>
                    <div>
                        <a href="https://idimage.ru/api/documentation" target="_blank">Api documentation</a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

