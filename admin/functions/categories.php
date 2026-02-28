<?php

function buildCategoryTree($comp, $excludeId = 0)
{
	$language_id = isset($_SESSION['admin_lang']) ? $_SESSION['admin_lang'] : 1;

	// Lấy tất cả category	
	$allCategories = $GLOBALS['sp']->getAll("
        SELECT * FROM {$GLOBALS['db_sp']}.categories 
        WHERE active=1 and comp = {$comp} " . ($excludeId ? " AND id <> {$excludeId}" : "") . "
        ORDER BY num ASC
    ");

	// Map theo id
	$catMap = [];
	foreach ($allCategories as $cat) {
		$catMap[$cat['id']] = $cat;
	}

	// Lấy quan hệ cha-con
	$relations = $GLOBALS['sp']->getAll("SELECT category_id, related_id FROM {$GLOBALS['db_sp']}.categories_related");
	$childrenMap = [];
	$parentMap = [];
	foreach ($relations as $rel) {
		$childrenMap[$rel['related_id']][] = $rel['category_id']; // con của cha
		$parentMap[$rel['category_id']] = $rel['related_id'];     // cha của con
	}
	// 3. Lấy tất cả chi tiết ngôn ngữ của các category
	$categoryIds = array_column($allCategories, 'id');
	if ($categoryIds) {
		$idsStr = implode(',', $categoryIds);
		$detailsList = $GLOBALS['sp']->getAll("
		SELECT * FROM {$GLOBALS['db_sp']}.categories_detail 
		WHERE categories_id IN ({$idsStr})
	");

		// Map chi tiết theo category_id và languageid
		$categoryDetails = [];
		foreach ($detailsList as $d) {
			$categoryDetails[$d['categories_id']][$d['languageid']] = $d;
		}
	} else {
		$categoryDetails = [];
	}

	// 4. Hàm đệ quy dựng cây
	$build = function ($parentIds, $level = 0, $parent_id = 0) use (&$build, $catMap, $childrenMap, $parentMap, $categoryDetails) {
		$tree = [];
		foreach ($parentIds as $pid) {
			if (!isset($catMap[$pid])) continue;
			$cat = $catMap[$pid];

			// Gán tất cả chi tiết ngôn ngữ
			$cat['detailsList'] = isset($categoryDetails[$pid]) ? $categoryDetails[$pid] : [];
			$cat['level'] = $level;
			$cat['parent_id'] = $parent_id;

			// Nếu có con, đệ quy
			if (isset($childrenMap[$pid])) {
				$cat['children'] = $build($childrenMap[$pid], $level + 1, $pid);
			} else {
				$cat['children'] = [];
			}

			$tree[] = $cat;
		}
		return $tree;
	};


	// RootIds (những category không phải con của ai)
	$allIds = array_column($allCategories, 'id');
	$childIds = array_column($relations, 'category_id');
	$rootIds = array_diff($allIds, $childIds);

	return $build($rootIds);
}
