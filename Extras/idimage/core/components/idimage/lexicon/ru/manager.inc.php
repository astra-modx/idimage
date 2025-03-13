<?php

include_once 'setting.inc.php';

$_lang['idimage'] = 'Id Image';
$_lang['idimage_menu_desc'] = 'Похожие товары';
$_lang['idimage_intro_msg'] = 'Вы можете выделять сразу несколько предметов при помощи Shift или Ctrl.';


$_lang['idimage_manual_desc'] = '<h2>Быстрый старт</h2>

<h3>Проверка системы</h3>
<p>Проверьте секцию с <strong>"Информация о системе"</strong>, все проверки должны быть пройдены успешно и баланс должен быть положительным, для дальше использовать сервиса idimage.</p>
<br>
<h3>Создание индекса товаров</h3>
<p>  
    1. Нажмите кнопку <strong>"Создать товары"</strong>, для получения списка товаров с изображениями.<br><br>  
    2. Нажмите кнопку <strong>"Получить вектора"</strong>, из сервиса idimage будут получены вектора для изображений для каждого товара.<br><br>  
    3. Нажмите кнопку <strong>"Индексировать товары"</strong>, для сравнение изображений товаров с помощью векторов и получения похожих товаров для каждого товара с векторами.<br><br>  
  
</p>  
<p>
<em>Используйте кнопку <strong>"Обновить статистику"</strong>, чтобы получить актуальную о состоянии сервиса.</em> </p>

';


////////////////////////
//// Install
////////////////////////
$_lang['idimage_actions_api_task_received'] = 'Отправить задания';
$_lang['idimage_actions_api_task_poll'] = 'Получить задания';
$_lang['idimage_actions_api_task_upload'] = 'Отправить изображения';
$_lang['idimage_actions_task_creation'] = 'Создать задания';
$_lang['idimage_actions_task_destroy'] = 'Уничтожить все задания';
$_lang['idimage_task_action_poll'] = 'Получить задание';
$_lang['idimage_task_action_received'] = 'Отправить задание';

$_lang['idimage_button_install'] = 'Установить компонент';
$_lang['idimage_button_download'] = 'Скачать компонент';
$_lang['idimage_button_download_encryption'] = 'Скачать компонент c шифрацией';
$_lang['idimage_task_action_action/received'] = 'Получен задание';

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

$_lang['idimage_task_action_send'] = 'Запрос в сервис';
$_lang['idimage_task_action_resetattempts'] = 'Сбросить попытки';
$_lang['idimage_task_action_remove'] = 'Удалить задание';
$_lang['idimage_tasks'] = 'Задания';
$_lang['idimage_tasks_intro_msg'] = 'Вы можете выделять сразу несколько предметов при помощи Shift или Ctrl.';

$_lang['idimage_pid'] = 'Pid';
$_lang['idimage_attempt'] = 'Попытки';
$_lang['idimage_hash'] = 'hash';
$_lang['idimage_status'] = 'Статус';
$_lang['idimage_type'] = 'Тип';
$_lang['idimage_createdon'] = 'Дата создания';
$_lang['idimage_updatedon'] = 'Дата обновления';
$_lang['idimage_active'] = 'Включен';
$_lang['idimage_picture'] = 'Изображение';
$_lang['idimage_total'] = 'Кол-во';
$_lang['idimage_task_id'] = 'Task id';
$_lang['idimage_image_available'] = 'Изображение доступно';
$_lang['idimage_etag'] = 'Etag';


$_lang['idimage_action_assign'] = 'Создание товаров для категорий';

////////////////////////
//// Closes
////////////////////////

$_lang['idimage_task_confirmed'] = 'Подтвержден';


////////////////////////
//// Closes
////////////////////////
$_lang['idimage_closes'] = 'Товары';
$_lang['idimage_close_id'] = 'Id';
$_lang['idimage_close_ball'] = 'Информация о похожих';
$_lang['idimage_close_pagetitle'] = 'Название';
$_lang['idimage_close_status'] = 'Статус';
$_lang['idimage_close_status_service'] = 'Статус в сервисе';
$_lang['idimage_close_total'] = 'Кол-во близких';
$_lang['idimage_close_version'] = 'Версия';
$_lang['idimage_close_min_scope'] = 'Мин. найдены бал';
$_lang['idimage_close_max_scope'] = 'Макс. найдены бал';
$_lang['idimage_close_search_scope'] = 'Бал поиска >';
$_lang['idimage_close_pid'] = 'Товар';
$_lang['idimage_close_errors'] = 'Ошибки';
$_lang['idimage_close_picture'] = 'Изображение';
$_lang['idimage_close_attempt'] = 'Попытки';

$_lang['idimage_close_tags'] = 'Теги фильтрации';
$_lang['idimage_close_received'] = 'Векторы получены';
$_lang['idimage_close_received_at'] = 'Дата получения векторов';
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
$_lang['idimage_indexed_action_balance'] = 'Проверить баланс';
$_lang['idimage_actions_confirm_title'] = 'Массовое действие';
$_lang['idimage_actions_confirm_text'] = 'Вы уверены что хотите выполнить это действие?';

####
$_lang['idimage_actions_api_embedding'] = 'Получение векторов';
$_lang['idimage_actions_api_embedding_desc'] = '[Платная функция] Внимание!! Вы запускате получение векторов для всех изображений товаров, это действие может занимает продолжительное время, не закрывайте окно до завершения действия!';
$_lang['idimage_actions_image_creation_desc'] = 'В очередь для отправки в сервис будут добавлены все товары из minishop2 с изображениями, после завершения добавления товаров в очередь, запустите процесс "Запустить отправку в сервис"';
$_lang['idimage_actions_image_queue/add_desc'] = 'Созданные товары будут синхронизированы с сервисом "Id Image". Товары будут добавлены в очередь на индексацию. По завершению синхронизации товаров запустите индексацию каталога нажав на иконку "Play"';

$_lang['idimage_actions_selected_records'] = 'Выделено записей';
$_lang['idimage_action_title'] = 'Выполнить действие';
$_lang['idimage_action_confirm'] = 'Вы уверены что хотите выполнить это действие?';

$_lang['idimage_sync'] = 'Синхронизация';
$_lang['idimage_update_products'] = 'Обновление товаров';

