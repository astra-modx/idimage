<?php

namespace IdImage\Ai;

use pdoFetch;

class CategoryTree
{
    private $categories;
    private bool $build = false;

    public function build(\idImage $idImage)
    {
        if ($this->build === false) {
            $this->build = true;

            /* @var pdoFetch $pdo */
            $pdo = $idImage->modx->getService('pdoFetch');
            $resources = $pdo->getCollection('msCategory', ['class_key' => 'msCategory'], ['select' => ['id', 'parent']]);

            $this->categories = $pdo->buildTree($resources);
        }

        return $this;
    }

    public function getChildrenIds(array $children)
    {
        // Инициализация массива для хранения ID
        $ids = [];

        // Перебор всех элементов
        foreach ($children as $child) {
            // Добавляем ID текущего элемента
            $ids[] = (int)$child['id'];

            // Если у элемента есть дочерние элементы, рекурсивно вызываем функцию
            if (!empty($child['children'])) {
                $ids = array_merge($ids, $this->getChildrenIds($child['children']));
            }
        }

        // Возвращаем массив с найденными ID
        return $ids;
    }

    public function all()
    {
        return $this->categories;
    }
}

