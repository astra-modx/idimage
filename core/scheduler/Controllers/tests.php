<?php

use IdImage\Ai\Similar;

/**
 * Демонстрация контроллера
 */
class CrontabControllerTests extends modCrontabController
{

    public function process()
    {
        $pids = [40];

        /* @var idImage $idImage */
        $idImage = $this->modx->getService('idimage', 'idImage', MODX_CORE_PATH.'components/idimage/model/');

        $indexed = $idImage->indexer();


        // Все товары
        $indexedType = $indexed->indexFirstLevelCategory();
        $similar = $indexed->similar();
        // Индексация по родительской категории
        #$IndexedType = $indexed->indexByParentCategory();
        // Индексация товаров первого уровня
        #$IndexedType = $indexed->indexFirstLevelCategory();

        // Запускаем сравнение товаров
        $results = $indexed::comparison(
            $idImage,
            $indexedType,
            $similar,
            $pids
        );

        $response = $indexed->response($results);
        dd($response);

        // Индексация всего каталога
        #$productIndexer->indexAll();


        // Индексация категории первого уровня
        #$indexedFirstLevelCategory = $productIndexer->indexByParentCategory();


        // Выводим результаты
        echo 'Индексируем все товары: ';
        print_r($allProducts);

        echo 'Индексируем по родительской категории: ';
        print_r($indexedByParentCategory);

        echo 'Индексируем категорию первого уровня: ';
        print_r($indexedFirstLevelCategory);
    }

}
