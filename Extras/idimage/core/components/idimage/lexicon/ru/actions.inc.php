<?php

include_once 'setting.inc.php';


# products

# actions
$_lang['idimage_actions_api_task_received'] = 'Отправить задания';
$_lang['idimage_actions_api_task_poll'] = 'Получить вектора';
$_lang['idimage_actions_api_task_poll_desc'] = 'Из сервис IDimage будут получены векторы изображений. По завершении получения всех векторов для товаров можно будет запустить индексацию.';

$_lang['idimage_actions_task_creation'] = 'Создать задания';
$_lang['idimage_actions_task_creation_desc'] = 'В сервис IDimage будут отправлены изображения товаров, полученные из базы.';
$_lang['idimage_task_action_poll'] = 'Получить вектора';
$_lang['idimage_task_action_received'] = 'Отправить задание';

$_lang['idimage_actions_indexed_products'] = 'Индексировать товары по векторам';
$_lang['idimage_actions_indexed_products_desc'] = 'Будут проиндексированы товары по векторам, полученным из сервиса IDimage. Убедитесь что все задания для получения векторов завершены.';

//////////////////////// Actions
$_lang['idimage_actions_image_creation'] = 'Добавить товары в очереди';
$_lang['idimage_actions_indexed_poll'] = 'Синхронизировать индекс';
$_lang['idimage_indexed_action_download'] = 'Скачать индекс с товарами';
$_lang['idimage_indexed_action_poll'] = 'Обновить статистику';
$_lang['idimage_actions_indexed_running'] = 'Запустить индексацию';



$_lang['idimage_actions_task_destroy'] = 'Уничтожить все задания';

# product destroy
$_lang['idimage_actions_product_destroy'] = 'Уничтожить все товары';
$_lang['idimage_actions_product_destroy_desc'] = 'Внимание!!! Уничтожая все товары вы удаляете только результаты индексации товаров, вектора полученные из сервиса idimage.ru не будут удалены.';


# product destroy
$_lang['idimage_actions_api_task_upload'] = 'Отправить изображения';
$_lang['idimage_actions_api_task_upload_desc'] = 'Превью изображения будут отправлены в сервис IDimage для доступа к локальным изображениям которые сейчас не доступны из глобального интернета.';




$_lang['idimage_navbar_create_product_btn'] = 'Создать товары';
$_lang['idimage_navbar_indexed_btn'] = 'Индексировать товары';
$_lang['idimage_navbar_tasks_pull'] = 'Получить векторы';
$_lang['idimage_navbar_statistic_btn'] = 'Обновить статистику';


# Actions
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
