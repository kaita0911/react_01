<?php

function getCategoryTree($sp, $db_sp, $langid)
{
    $sql_cat = "SELECT c.id, c.comp, c.active,
               d.name AS name_detail,
               d.unique_key,
               c.img_vn
        FROM {$db_sp}.categories AS c
        LEFT JOIN {$db_sp}.categories_detail AS d
            ON d.categories_id = c.id
            AND d.languageid = {$langid}
        WHERE c.active = 1
        ORDER BY c.num ASC";

    $categories = $sp->getAll($sql_cat);

    $catMap = [];
    foreach ($categories as $c) {
        $catMap[$c['id']] = $c;
    }

    $sql_rel = "SELECT category_id, related_id
                FROM {$db_sp}.categories_related";

    $relations = $sp->getAll($sql_rel);

    $childrenMap = [];
    $childIds = [];

    foreach ($relations as $r) {
        $childrenMap[$r['related_id']][] = $r['category_id'];
        $childIds[] = $r['category_id'];
    }

    $rootCats = [];

    foreach ($categories as $c) {
        if (!in_array($c['id'], $childIds)) {
            $rootCats[] = $c;
        }
    }

    function buildTree($cat, $childrenMap, $catMap)
    {
        $catId = $cat['id'];
        $cat['categories'] = [];

        if (!empty($childrenMap[$catId])) {
            foreach ($childrenMap[$catId] as $childId) {
                if (isset($catMap[$childId])) {
                    $cat['categories'][] =
                        buildTree($catMap[$childId], $childrenMap, $catMap);
                }
            }
        }

        return $cat;
    }


    $tree = [];

    foreach ($rootCats as $root) {
        $tree[] = buildTree($root, $childrenMap, $catMap);
    }

    return $tree;
}

function normalizeArray($data) {
    return array_map(function ($item) {
        return array_filter(
            $item,
            function ($key) {
                return !is_int($key);
            },
            ARRAY_FILTER_USE_KEY
        );
    }, $data);
}
