<?php
/*
 * Copyright (c) 2014, Josef Kufner  <jk@frozen-doe.net>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace Duf\Renderer\HtmlFilter;

/**
 * Simple numeric paginator.
 */
class Paginator extends SimpleButton implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		// Get value early, so class can be set
		$filters = $form->getViewData($widget_conf['group_id']);
		$page_size = isset($widget_conf['page_size']) ? $widget_conf['page_size'] : 30;
		$offset = isset($filters['offset']) ? $filters['offset'] : 0;
		$page_count = isset($filters['_count']) ? ceil($filters['_count'] / $page_size) : ceil($offset / $page_size) + 3;
		$current_page = ceil($offset / $page_size);

		echo "<div";
		static::renderClassAttr($form, $template_engine,
			isset($widget_conf['class']) ? $widget_conf['class'] : null,
			'filter_paginator');
		echo ">";

		// Previous page
		if ($current_page > 0) {
			static::renderPageButton($form, $template_engine, $filters, _('Previous'), 'page prev', array(
				'offset' => ($current_page - 1) * $page_size,
				'limit' => $page_size,
			));
		}

		// Page numbers
		for ($page = 0; $page < $page_count; $page++) {
			static::renderPageButton($form, $template_engine, $filters, $page + 1, 'page', array(
				'offset' => $page * $page_size,
				'limit' => $page_size,
			));
		}

		// Next page
		if ($current_page + 1 < $page_count) {
			static::renderPageButton($form, $template_engine, $filters, _('Next'), 'page next', array(
				'offset' => ($current_page + 1) * $page_size,
				'limit' => $page_size,
			));
		}

		echo "</div>\n";
	}


	private static function renderPageButton($form, $template_engine, $filters, $page_html_label, $page_class, $page_filters)
	{
		echo "<a href=\"", htmlspecialchars(static::buildFilterLink($filters, $page_filters)), "\"";
		static::renderClassAttr($form, $template_engine,
			'filter button',
			$page_class,
			static::isFilterActive($filters, $page_filters) ? 'active' : null);
		echo ">";
		echo $page_html_label;
		echo "</a>\n";
	}

}

