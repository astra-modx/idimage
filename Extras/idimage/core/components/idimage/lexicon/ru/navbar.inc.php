<?php

include_once 'setting.inc.php';

$_lang['idimage'] = 'Id Image';


$_lang['idimage_navbar_create_product_title'] = '<h3>Создание товаров</h3';
$_lang['idimage_navbar_create_product_btn'] = 'Создать товары';
$_lang['idimage_navbar_create_product_text'] = '<em>Товары с изображениями будут созданы для получения векторов и дальнейшей индексаци</em>';

$_lang['idimage_navbar_indexed_title'] = '<h3>Индексация</h3';
$_lang['idimage_navbar_indexed_btn'] = 'Индексировать товары';
$_lang['idimage_navbar_indexed_text'] = '<em>По завершении индексации каждый товар получает список похожих на него товаров по векторам полученным из сервиса IDimage.</em>';

$_lang['idimage_navbar_embedding_title'] = '<h3>Векторизация</h3';
$_lang['idimage_navbar_embedding_btn'] = 'Создать задания';
$_lang['idimage_navbar_embedding_pull'] = 'Получить векторы';
$_lang['idimage_navbar_embedding_text'] = '<em>По завершении индексации каждый товар получает список похожих на него товаров по векторам полученным из сервиса IDimage.</em>';


$_lang['idimage_navbar_statistic_title'] = '<h3>Статистика</h3>';
$_lang['idimage_navbar_statistic_btn'] = 'Обновить статистику';


# statistic
$_lang['idimage_navbar_total_similar'] = 'С похожими: <b>{0}</b> шт.';
$_lang['idimage_navbar_total_completed'] = 'Завершено: <b>{0}</b> шт.';
$_lang['idimage_navbar_total_embedding'] = 'Товаров с векторами: <b>{0}</b> шт.';
$_lang['idimage_navbar_total_error'] = 'Товаров без векторов: <b>{0}</b> шт.';
$_lang['idimage_navbar_total'] = 'Создано товаров: <b>{0}</b> шт.';
$_lang['idimage_navbar_total_files'] = 'Всего товаров c изображениями: <b>{0}</b> шт.';
$_lang['idimage_navbar_total_tasks'] = 'Всего заданий: <b>{0}</b> шт.';
$_lang['idimage_navbar_total_tasks_pending'] = 'Ожидают завершения: <b>{0}</b> шт.';
$_lang['idimage_navbar_total_tasks_completed'] = 'Завершено: <b>{0}</b> шт.';

# actions
$_lang['idimage_actions_api_task_received'] = 'Отправить задания';
$_lang['idimage_actions_api_task_poll'] = 'Получить вектора';
$_lang['idimage_actions_api_task_poll_desc'] = 'Из сервис IDimage будут получены векторы изображений. По завершении получения всех векторов для товаров можно будет запустить индексацию.';
$_lang['idimage_actions_api_task_upload'] = 'Отправить изображения';
$_lang['idimage_actions_task_creation'] = 'Создать задания';
$_lang['idimage_actions_task_creation_desc'] = 'В сервис IDimage будут отправлены изображения товаров, полученные из базы.';
$_lang['idimage_actions_task_destroy'] = 'Уничтожить все задания';
$_lang['idimage_task_action_poll'] = 'Получить вектора';
$_lang['idimage_task_action_received'] = 'Отправить задание';

$_lang['idimage_actions_indexed_products'] = 'Индексировать товары по векторам';
$_lang['idimage_actions_indexed_products_desc'] = 'Будут проиндексированы товары по векторам, полученным из сервиса IDimage. Убедитесь что все векторы получены.';

//////////////////////// Actions
$_lang['idimage_actions_image_creation'] = 'Добавить товары в очереди';
$_lang['idimage_actions_indexed_poll'] = 'Синхронизировать индекс';
$_lang['idimage_actions_image_destroy'] = 'Уничтожить все товары';
$_lang['idimage_indexed_action_download'] = 'Скачать индекс с товарами';
$_lang['idimage_indexed_action_poll'] = 'Обновить статистику';
$_lang['idimage_actions_indexed_running'] = 'Запустить индексацию';
