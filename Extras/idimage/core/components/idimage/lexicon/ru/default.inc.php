<?php

include_once 'setting.inc.php';

$_lang['idimage'] = 'Id Image';
$_lang['idimage_menu_desc'] = 'Похожие товары';
$_lang['idimage'] = 'Id Image';
$_lang['idimage_system_settings'] = 'Системные настройки';
$_lang['idimage_system_settings_desc'] = 'Системные настройки Id Image';
$_lang['idimage_clouds'] = 'Cloud';

////////////////////////////////////////////////////////////////////////
//////////////////////// Validate
/// ////////////////////////
$_lang['idimage_site_url_invalid'] = 'Ссылка на сайте содержит локальный адрес: [[+url]]. Укажите другой глобальный хост в настройка (idimage_site_url) для загрузки изображений в сервис или используйте cloud загрузку изображений (в настройках включите idimage_cloud)';
$_lang['idimage_cloud_upload_disabled'] = 'Облачная загрузка отключена. Запросите доступ к облачной загрузке в сервисе Id Image.';


$_lang['idimage_form_total'] = 'Кол-во: ';
$_lang['idimage_catalog_disabled'] = 'Каталог отключен.';


$_lang['idimage_help'] = 'Каталог';
$_lang['idimage_help_intro'] = 'Каталог';


//////////////////////// Actions
$_lang['idimage_actions_image_creation'] = 'Добавить товары в очереди';
$_lang['idimage_actions_indexed_poll'] = 'Синхронизировать индекс';
$_lang['idimage_actions_image_queue/add'] = 'Начать отправку очереди';
$_lang['idimage_actions_image_queue/delete'] = 'Удалить товары из сервиса';
$_lang['idimage_actions_image_status/processing'] = 'Установить статус "В обработке"';
$_lang['idimage_actions_image_status/queue'] = 'Установить статус "В очереди"';
$_lang['idimage_actions_image_upload/enable'] = 'Включить загрузку в облако';
$_lang['idimage_actions_image_upload/disable'] = 'Отключить загрузку в облако';
$_lang['idimage_actions_image_upload/cloud'] = 'Загрузка изображений в облако.';
$_lang['idimage_actions_image_destroy'] = 'Уничтожить все товары';
$_lang['idimage_indexed_action_download'] = 'Скачать индекс с товарами';
$_lang['idimage_indexed_action_poll'] = 'Получить информацию о индексе';
$_lang['idimage_actions_indexed_products'] = 'Импортировать похожие товары';
$_lang['idimage_actions_image_upload/reset'] = 'Сбросить метку загрузки в облако';
$_lang['idimage_actions_image_upload/mark'] = 'Установить метку загрузки в облако';
$_lang['idimage_actions_indexed_running'] = 'Запустить индексацию';
