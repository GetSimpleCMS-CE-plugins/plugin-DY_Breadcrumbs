<?php

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");
define('DY_BREADCRUMBS', $thisfile);

i18n_merge(DY_BREADCRUMBS) || i18n_merge(DY_BREADCRUMBS, 'en_US');

# register plugin
register_plugin(
  $thisfile,			# ID of plugin, should be filename minus php
  'DY Breadcrumbs',		# Title of plugin
  '1.5',				# Version of plugin
  'Dmitry Yakovlev',	# Author of plugin
  'http://dimayakovlev.ru/laboratory/',	# Author URL
  i18n_r(DY_BREADCRUMBS . '/DY_BREADCRUMBS_PLUGIN_DESCRIPTION'),   # Plugin Description
  '',					# Page type of plugin
  ''					# Function that displays content
);

/* 
 * Simple usage:
 * 
 * <ul itemscope itemtype="https://schema.org/BreadcrumbList">
 * 		<?php dyGetBreadcrumbs(get_page_slug(false)); ?>
 * </ul>
 * 
 * Read more about using microdata with breadcrumbs: https://schema.org/BreadcrumbList
 */

function dyGetBreadcrumbs($slug, $home = true, $homeTitle = false, $parentTitleLength = 0, $currentTitleLength = 0, $fullPath = false, $useMicrodata = true, $useMenuData = false) {
	global $pagesArray;
	$slug = (string)$slug; // Ensure slug is a string
	$tmpSlug = $slug;
	if (isset($pagesArray[$slug])) {
		if ($home) {
			if (!$homeTitle) $homeTitle = dyGetDefaultHomeTitle();
			if ($slug == 'index') {
				if ($useMicrodata) {
					echo '
		<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">',
			'<span itemprop="name">', htmlspecialchars($homeTitle, ENT_QUOTES, 'UTF-8'), '</span><meta itemprop="position" content="1" />
		</li>';
				} else {
					echo '
		<li class="current active">', htmlspecialchars($homeTitle, ENT_QUOTES, 'UTF-8') , '</li>';
				}
				} else {
					if ($useMicrodata) {
						$count = 1;
						echo '
		<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">',
			'<a itemprop="item" href="', find_url('index', ''), '"><span itemprop="name">', htmlspecialchars($homeTitle, ENT_QUOTES, 'UTF-8'), '</span></a><meta itemprop="position" content="', $count, '" />
		</li>';
						$count++;
					} else {
						echo '
		<li>
			<a href="', find_url('index', ''), '">', htmlspecialchars($homeTitle, ENT_QUOTES, 'UTF-8'), '</a>
		</li>';
					}
				}
		}
		while ($pagesArray[$tmpSlug]['parent']) {
			$tmpSlug = $pagesArray[$tmpSlug]['parent'];
			if (!$pagesArray[$tmpSlug]['private'])
			$result[] = $tmpSlug;
		}
		if (isset($result)) {
			$parent = '';
			foreach (array_reverse($result) as $item) {
				if ($useMenuData && trim($pagesArray[$item]['menu']) != '') {
					$title = html_entity_decode(strip_decode($pagesArray[$item]['menu']), ENT_QUOTES, 'UTF-8');
				} else {
					$title = html_entity_decode(strip_decode($pagesArray[$item]['title']), ENT_QUOTES, 'UTF-8');
				}
				if ($parentTitleLength > 0 && mb_strlen($title, 'UTF-8') > $parentTitleLength + 3) {
					$title = mb_substr($title, 0, $parentTitleLength) . '&hellip;';
				}
				if ($useMicrodata) {
					echo '
		<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">',
		   '<a itemprop="item" href="', find_url($item, $parent), '"><span itemprop="name">', htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),'</span></a><meta itemprop="position" content="', $count, '" />
		</li>';
					$count++;
				} else {
					echo '
		<li><a href="', find_url($item, $parent), '">', htmlspecialchars($title, ENT_QUOTES, 'UTF-8'), '</a></li>';
				}
				if ($fullPath) {
					$parent .= $item . '/';
				} else {
					$parent = $item;
				}
			}
		}
		if ($slug != 'index') {
			$title = html_entity_decode(strip_decode($pagesArray[$slug]['title']), ENT_QUOTES, 'UTF-8');
			if ($currentTitleLength > 0 && mb_strlen($title, 'UTF-8') > $currentTitleLength + 3) {
				$title = mb_substr($title, 0, $currentTitleLength) . '&hellip;';
			}
			if ($useMicrodata) {
				echo '
		<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="current active"><span itemprop="name">', htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),'</span><meta itemprop="position" content="', $count, '" /></li>';
			} else {
				echo '
		<li class="current active">', htmlspecialchars($title, ENT_QUOTES, 'UTF-8'), '</li>';
			}
		}
	}
}

function dyGetDefaultHomeTitle() {
	return i18n_r(DY_BREADCRUMBS . '/DY_BREADCRUMBS_HOME');
}
?>
