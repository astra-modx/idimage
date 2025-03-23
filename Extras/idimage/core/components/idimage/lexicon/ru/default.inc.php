<?php

include_once 'setting.inc.php';

$_lang['idimage'] = 'ID image';
$_lang['idimage_menu_desc'] = 'Похожие товары';
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


$_lang['idimage_help_site'] = 'Cайт';



$_lang['idimage_received'] = 'Есть вектора';
$_lang['idimage_inreceived'] = 'Нет векторов';
$_lang['idimage_received_all'] = 'Вектора';

$_lang['idimage_similar'] = 'Есть похожие';
$_lang['idimage_insimilar'] = 'Нет похожих';
$_lang['idimage_similar_all'] = 'Похожие';
$_lang['idimage_balance_text'] = 'На счету: ';
$_lang['idimage_token_not_set'] = 'Не указан параметр "<b>'.$_lang['setting_idimage_token'].'</b>".<br> Скопируйте токен из личного кабинета <a 
    href="https://idimage.ru/account/info" target="_blank">idimage.ru</a> и вставьте его в <a href="/manager/?a=system/settings&ns=idimage" target="_blank">системные настройки</a>';
