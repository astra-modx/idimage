<?php

$_lang['area_idimage_main'] = 'Основные';

$_lang['setting_idimage_api_url'] = 'Адрес сервиса Id Image';
$_lang['setting_idimage_api_url_desc'] = 'По умолчанию: https://idimage.ru/api/.';

$_lang['setting_idimage_token'] = 'Токен для сервисов AI';
$_lang['setting_idimage_token_desc'] = 'Укажите токен для сервисов Id image, получить токен можно в личном кабинете на сайте <a href="https://idimage.ru/account/" target="_blank">idimage.ru</a>.';

$_lang['setting_idimage_enable'] = 'Включить получение векторов изображений';
$_lang['setting_idimage_enable_desc'] = 'По умолчанию включено. Если выбрать "Нет", сайт не будет получать вектора изображений из сервиса Id Image.';
$_lang['setting_idimage_token_ai_desc'] = 'Укажите токен для сервисов AI, чтобы использовать для получения векторных данных изображений.';


$_lang['setting_idimage_maximum_products_found'] = 'Максимальное количество похожих товаров';
$_lang['setting_idimage_maximum_products_found_desc'] = 'По умолчанию: 50. Это максимальное количество похожих товаров которые будут найдены для одного товара';

$_lang['setting_idimage_minimum_probability_score'] = 'Минимальный процент совпадения';
$_lang['setting_idimage_minimum_probability_score_desc'] = 'По умолчанию: 70. Чем меньше процент, тем меньше товар будет похож на оригинальный. Рекомендуется не менее 70%, чтобы совпадения были более точными';

$_lang['setting_idimage_send_file'] = 'Включить отправку файлов';
$_lang['setting_idimage_send_file_desc'] = 'По умолчанию отключано. Если выбрать "Да", сайт будет отправлять бинарные даные изображений на сервер Id Image. Используется только в случае если изображения не доступны по ссылке.';

$_lang['setting_idimage_root_parent'] = 'Родительский раздел для поиска похожих товаров';
$_lang['setting_idimage_root_parent_desc'] = 'По умолчанию: 0. Это ID раздела, который будет отображаться в дереве категорий при создании товаров';

$_lang['setting_idimage_site_url'] = 'Альтернативный URL';
$_lang['setting_idimage_site_url_desc'] = 'Вы можете указать альтернативный URL для сервиса IDimage откуда будут скачиваться изображений товаров из интернета для создания векторных данных.';

$_lang['area_idimage_limit'] = 'Лиминты';
$_lang['setting_idimage_limit_indexed'] = 'Индексировать за раз';
$_lang['setting_idimage_limit_indexed_desc'] = 'По умолчанию: 50. Это количество товаров которые будут индексированы за раз, чем больше товаров, тем дольше будет индексация.';

$_lang['setting_idimage_limit_creation'] = 'Создавать товаров за раз';
$_lang['setting_idimage_limit_creation_desc'] = 'По умолчанию: 100. Это количество товаров которые будут созданы за раз';

$_lang['setting_idimage_limit_show_similar_products'] = 'Похожих изображений в разделе синхронизации ';
$_lang['setting_idimage_limit_show_similar_products_desc'] = 'По умолчанию: 5. В списке товаров в административной части сайта, в разделе синхронизации товаров, будут показываться похожие товары.';

$_lang['setting_idimage_limit_upload'] = 'Кол-во изображений для загрузки';
$_lang['setting_idimage_limit_upload_desc'] = 'По умолчанию: 5. Используется для режима "Включить отправку файлов"';


$_lang['setting_idimage_indexed_service'] = 'Индексация товаров в сервисе IDimage';
$_lang['setting_idimage_indexed_service_desc'] = 'По умолчанию Нет. Если выбрать Да, сайт будет получать похожие товары из сервиса IDimage. Для больших каталогов, лучше использовать индексацию товаров в сервисе IDimage, а не на сайте.';


$_lang['setting_idimage_limit_task'] = 'Кол-во заданий для запуска';
$_lang['setting_idimage_limit_task_desc'] = 'По умолчанию 1000.';


$_lang['setting_idimage_indexed_type'] = 'Тип индексации';
$_lang['setting_idimage_indexed_type_desc'] = 'Подробней см. на сайте <a href="https://docs.modx.pro/components/idimage" target="_blank">docs.modx.pro</a>';
