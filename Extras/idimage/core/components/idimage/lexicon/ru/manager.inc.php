<?php

include_once 'setting.inc.php';

$_lang['idimage'] = 'Id Image';
$_lang['idimage_menu_desc'] = 'Похожие товары';
$_lang['idimage_intro_msg'] = 'Вы можете выделять сразу несколько предметов при помощи Shift или Ctrl.';


$_lang['idimage_manual_desc'] = '<h3>Инструкция</h3>

<h3>Проверка системы</h3>
<p>Проверьте секцию с <strong>"Информация о системе"</strong>, все проверки должны быть пройдены успешно, для дальше использовать сервиса idimage.</p>
<br>
<h3>Индексация товаров</h3>
<p>  
    1. Нажмите кнопку <strong>"Добавить товары в очередь"</strong>. На вкладке <strong>"Товары"</strong> будут созданы все товары с изображениями  
    из вашего каталога.<br><br>  
    2. Нажмите кнопку <strong>"Запустить очередь"</strong>, чтобы передать товары в сервис. Дождитесь завершения передачи.<br><br>  
    3. Нажмите кнопку <strong>"Запустить индексацию"</strong>. Сервис idimage начнет обработку переданных товаров.<br><br>  
    4. Используйте кнопку <strong>"Получить информацию о индексе"</strong>, чтобы проверить статус индексации.<br><br>  
    5. После завершения индексации нажмите <strong>"Импортировать похожие товары"</strong>, чтобы скачать последнюю версию индекса  
    и импортировать похожие товары.<br>  
    По завершении импорта в колонке <strong>"Кол-во похожих"</strong> на вкладке <strong>"Товары"</strong>  
    отобразится количество найденных похожих товаров.<br><br>  
    6. Найдите товар с похожими на вкладке <strong>"Товары"</strong>, разместите сниппет на страницу с найденным товаром  
    и проверьте результат.<br>  
</p>  
<br>
<p class="text-warning"><strong>Внимание!</strong> Запускайте индексацию только после передачи всех товаров в сервис.  
После завершения индексации импортируйте похожие товары.</p>

';


////////////////////////
//// Install
////////////////////////
$_lang['idimage_button_install'] = 'Установить компонент';
$_lang['idimage_button_download'] = 'Скачать компонент';
$_lang['idimage_button_download_encryption'] = 'Скачать компонент c шифрацией';

////////////////////////
//// Combo
////////////////////////
$_lang['idimage_combo_select'] = 'Выберите';
$_lang['idimage_all'] = 'Все';
$_lang['idimage_active'] = 'Включен';
$_lang['idimage_inactive'] = 'Выключен';
$_lang['idimage_all_statuses'] = 'Все статусы';
$_lang['idimage_all_statuses_services'] = 'Все статусы сервиса';


////////////////////////
//// grid
////////////////////////
$_lang['idimage_grid_search'] = 'Поиск';
$_lang['idimage_grid_actions'] = 'Действия';
$_lang['idimage_actions_dropdown'] = 'Действия';


////////////////////////
//// Clouds

$_lang['idimage_close_action_upload'] = 'Загрузить в облако';
$_lang['idimage_close_upload'] = 'Загружено в облако';
$_lang['idimage_close_upload_link'] = 'Ссылка в облаке';

////////////////////////
//// Closes
////////////////////////
$_lang['idimage_closes'] = 'Товары';
$_lang['idimage_close_id'] = 'Id';
$_lang['idimage_close_status'] = 'Статус';
$_lang['idimage_close_status_service'] = 'Статус в сервисе';
$_lang['idimage_close_total'] = 'Кол-во близких';
$_lang['idimage_close_version'] = 'Версия';
$_lang['idimage_close_min_scope'] = 'Мин бал';
$_lang['idimage_close_pid'] = 'Pid';
$_lang['idimage_close_errors'] = 'Ошибки';
$_lang['idimage_close_picture'] = 'Изображение';

$_lang['idimage_close_tags'] = 'Теги фильтрации';
$_lang['idimage_close_received'] = 'Синхронизирован';
$_lang['idimage_close_received_at'] = 'Дата синхронизации';
$_lang['idimage_close_createdon'] = 'Дата создания';
$_lang['idimage_close_updatedon'] = 'Дата обновления';
$_lang['idimage_close_description'] = 'Описание';
$_lang['idimage_close_active'] = 'Активно';


# Actions
$_lang['crontabmanager_actions_dropdown'] = 'Действия';
$_lang['crontabmanager_actions_dropdown_status'] = 'Смена статусов';
$_lang['idimage_actions_creation'] = 'Найти изображения';
$_lang['idimage_actions_destroy'] = 'Удалить все';
$_lang['idimage_actions_upload'] = 'Начать загрузку в cloud';
$_lang['idimage_actions_poll'] = 'Опросить статус';
$_lang['idimage_actions_reindex'] = 'Переиндексировать';
$_lang['idimage_actions_upversion'] = 'Поднять версию';
$_lang['idimage_actions_queue_add'] = 'Добавить в очередь';
$_lang['idimage_actions_queue_delete'] = 'Удалить из очереди';


$_lang['idimage_actions_status_upload'] = 'Установить статус "Загрузка в cloud"';
$_lang['idimage_actions_status_proccessing'] = 'Установить статус "ожидание"';
$_lang['idimage_actions_status_queue'] = 'Установить статус "В очереди"';


$_lang['idimage_close_create'] = 'Создать товар';
$_lang['idimage_close_update'] = 'Изменить товар';
$_lang['idimage_close_enable'] = 'Включить товар';
$_lang['idimage_closes_enable'] = 'Включить товары';
$_lang['idimage_close_disable'] = 'Отключить товар';
$_lang['idimage_closes_disable'] = 'Отключить товары';
$_lang['idimage_close_remove'] = 'Удалить товар';
$_lang['idimage_closes_remove'] = 'Удалить товары';
$_lang['idimage_close_remove_confirm'] = 'Вы уверены, что хотите удалить этот товар?';
$_lang['idimage_closes_remove_confirm'] = 'Вы уверены, что хотите удалить эти товары?';
$_lang['idimage_close_active'] = 'Включено';

$_lang['idimage_close_err_name'] = 'Вы должны указать имя товара.';
$_lang['idimage_close_err_ae'] = 'Товар с таким именем уже существует.';
$_lang['idimage_close_err_nf'] = 'Товар не найден.';
$_lang['idimage_close_err_ns'] = 'Товар не указан.';
$_lang['idimage_close_err_remove'] = 'Ошибка при удалении товара.';
$_lang['idimage_close_err_save'] = 'Ошибка при сохранении товара.';
$_lang['idimage_actions_confirm_title'] = 'Массовое действие';
$_lang['idimage_actions_confirm_text'] = 'Вы уверены что хотите выполнить это действие?';


////////////////////////
//// Indexed
////////////////////////
$_lang['idimage_indexeds'] = 'Индексация';
$_lang['idimage_indexed_total_products'] = 'Все товаров';
$_lang['idimage_indexed_id'] = 'Id';
$_lang['idimage_indexed_name'] = 'Имя каталог';
$_lang['idimage_indexed_code'] = 'Идентификатор каталога';
$_lang['idimage_indexed_upload_api'] = 'Загрузка в хранилище разрешена';
$_lang['idimage_indexed_awaiting_processing'] = 'В очереди';
$_lang['idimage_indexed_version'] = 'Версия';
$_lang['idimage_indexed_upload'] = 'Доступен';
$_lang['idimage_indexed_size'] = 'Размер файла';
$_lang['idimage_indexed_download_link'] = 'Ссылка для скачивания';
$_lang['idimage_indexed_images'] = 'Кол-во товаров';
$_lang['idimage_indexed_closes'] = 'Кол-во близких';
$_lang['idimage_indexed_status'] = 'Статус';
$_lang['idimage_indexed_sealed'] = 'Запечатан';
$_lang['idimage_indexed_use_version'] = 'Используется';
$_lang['idimage_indexed_upload_at'] = 'Дата загрузки файла';
$_lang['idimage_indexed_upload'] = 'Загружен';
$_lang['idimage_indexed_createdon'] = 'Дата создания';
$_lang['idimage_indexed_updatedon'] = 'Дата обновления';
$_lang['idimage_indexed_description'] = 'Описание';
$_lang['idimage_indexed_active'] = 'Активно';


##### actions
$_lang['idimage_indexed_create_images'] = 'Всего товаров';
$_lang['idimage_indexed_create_closes'] = 'Отправлено в сервис';


$_lang['idimage_indexed_action_info'] = 'Информация о индексе';
$_lang['idimage_indexed_action_useversion'] = 'Закрепить похожие товары';
$_lang['idimage_indexed_action_running'] = 'Разрешить запуск индексации';
$_lang['idimage_indexed_action_remove'] = 'Удалить версию индекса';

////////////////////////
$_lang['idimage_indexed_action_create_version'] = 'Создать версию';
$_lang['idimage_indexed_window_create'] = 'Создать версию';
$_lang['idimage_indexed_window_update'] = 'Информация о индексе';


####
$_lang['idimage_actions_image_creation_desc'] = 'В очередь для отправки в сервис будут добавлены все товары из minishop2 с изображениями, после завершения добавления товаров в очередь, запустите процесс "Запустить отправку в сервис"';
$_lang['idimage_actions_image_queue/add_desc'] = 'Созданные товары будут синхронизированы с сервисом "Id Image". Товары будут добавлены в очередь на индексацию. По завершению синхронизации товаров запустите индексацию каталога нажав на иконку "Play"';

$_lang['idimage_actions_selected_records'] = 'Выделено записей';
$_lang['idimage_action_title'] = 'Выполнить действие';
$_lang['idimage_action_confirm'] = 'Вы уверены что хотите выполнить это действие?';


$_lang['idimage_sync'] = 'Синхронизация';
$_lang['idimage_update_products'] = 'Обновление товаров';


######################
$_lang['idimage_actions_image_upload/cloud_desc'] = '<span style="color: darkred">Отправка в облачное хранилище займет достаточно много времени, не уходите со страницы до завершения процесса отправки. Рассмотрите возможность указать альтернативный url откуда возможно скачать изображения</span>';
$_lang['idimage_actions_indexed_products_desc'] = '<span style="color: darkred">Перед импортом похожих товаров, убедитесь что индексация завершена и находиться в статусе <b>"Finished"</b>.</span>';
$_lang['idimage_actions_indexed_running_desc'] = '<span style="color: darkred">После запуска процесса индексации, прервать процесс будет невозможно, убедитесь что вы отправили в сервис все товары для индексации и обработка товаров завершена.</span>';


$_lang['ms2_utilities_gallery'] = 'Галерея';
$_lang['ms2_utilities_gallery_done'] = 'Завершено';
$_lang['ms2_utilities_gallery_done_message'] = 'Обновление превью изображений успешно завершено.';
$_lang['ms2_utilities_gallery_err_noproducts'] = 'В каталоге нет товаров';
$_lang['ms2_utilities_gallery_for_step'] = 'Обработать товаров за 1 шаг';
$_lang['ms2_utilities_gallery_information'] = 'Выбранный источник файлов: <strong>{0}</strong> <a href="?a=source/update&id={1}" target="_blank"><i class="icon icon-cog"></i></a><br>Всего товаров: <strong>{2} шт.</strong> <br> Изображений: <strong>{3} шт.</strong> ';
$_lang['ms2_utilities_gallery_intro'] = 'Обновление всех изображений товаров согласно указанным параметрам. <br>Данная операция является трудозатратной, поэтому не указывайте большое число для одной итерации.';
$_lang['ms2_utilities_gallery_refresh'] = 'Обновить';
$_lang['ms2_utilities_gallery_step_offset'] = 'Пропустить от начала';
$_lang['ms2_utilities_gallery_updating'] = 'Обновлению превью';
$_lang['ms2_utilities_import'] = 'Импорт';
$_lang['ms2_utilities_import_debug'] = 'Режим отладки';
$_lang['ms2_utilities_import_file_ns'] = 'Не выбран файл для импорта.';
$_lang['ms2_utilities_import_file_ext_err'] = 'Не верное расширение файла.<br> Разрешен только *.csv';
$_lang['ms2_utilities_import_file_nf'] = 'Файл с расположением <strong>[[+path]]</strong> не найден';
$_lang['ms2_utilities_import_fields_ns'] = 'Не заполнен параметр <br><strong>Поля импорта</strong>.';
$_lang['ms2_utilities_import_intro'] = 'Простой импорт каталога товаров. <br>При импорте большого объема товаров ваш сайт может зависнуть.<br>Рекомендуем запускать выполнение в фоновом режиме, для этого необходимо отметить чекбокс "Использовать планировщик" (требуется установленный компонент <a href="https://modstore.pro/packages/utilities/scheduler" target="_blank">Scheduler</a>)';
$_lang['ms2_utilities_import_key_ns'] = 'Не заполнен параметр <br><strong>Уникальное поле</strong>.';
$_lang['ms2_utilities_import_success'] = 'Импорт выполнен.<br>Всего обработано строк: <strong>[[+total]]</strong><br>Создано: <strong>[[+created]]</strong><br>Обновлено: <strong>[[+updated]]</strong>';
$_lang['ms2_utilities_import_label_delimiter'] = 'Разделитель колонок в файле';
$_lang['ms2_utilities_import_label_fields'] = 'Поля импорта';
$_lang['ms2_utilities_import_label_file'] = 'Файл для импорта';
$_lang['ms2_utilities_import_label_file_empty'] = 'Выберите файл';
$_lang['ms2_utilities_import_required_field'] = 'Пропущено обязательное поле <strong>[[+field]]</strong>';
$_lang['ms2_utilities_import_save_fields'] = 'Сохранить настройки полей';
$_lang['ms2_utilities_import_save_fields_title'] = 'Сохранено';
$_lang['ms2_utilities_import_save_fields_message'] = 'Список полей для импорта сохранен';
$_lang['ms2_utilities_import_skip_header'] = 'Пропускать первую строку-шапку';
$_lang['ms2_utilities_import_submit'] = 'Импортировать';
$_lang['ms2_utilities_import_update_key'] = 'Уникальное поле для обновления';
$_lang['ms2_utilities_import_update_products'] = 'Обновить товары';
$_lang['ms2_utilities_import_use_scheduler'] = 'Использовать планировщик';
$_lang['ms2_utilities_params'] = 'Параметры ';
$_lang['ms2_utilities_scheduler_nf'] = 'У вас не установлен компонент Scheduler';
$_lang['ms2_utilities_scheduler_task_ce'] = 'Не удалось создать задание Scheduler';
$_lang['ms2_utilities_scheduler_success'] = 'Задание Scheduler создано';
