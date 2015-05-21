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
 * Month paginator (one page per month).
 */
class MonthPaginator extends SimpleButton implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		// Get value early, so class can be set
		$filters = $form->getViewData($widget_conf['group_id']);
		$page_filter_name = isset($widget_conf['filter_name']) ? $widget_conf['filter_name'] : 'month';
		$min_date = $filters['_min_date'];
		$max_date = $filters['_max_date'];
		$current_page = isset($filters[$page_filter_name]) ? $filters[$page_filter_name] : null;

		$begin_t = strtotime($min_date);
		$end_t = strtotime($max_date);
		$active_t = empty($filters[$page_filter_name]) ? time() : strtotime($filters[$page_filter_name]);

		// Calculate bounds from dates
		$begin_year   = (int) strftime('%Y', $begin_t);
		$begin_month  = (int) strftime('%m', $begin_t);
		$end_year     = (int) strftime('%Y', $end_t);
		$end_month    = (int) strftime('%m', $end_t);
		$active_year  = (int) strftime('%Y', $active_t);
		$active_month = (int) strftime('%m', $active_t);
		$begin  = 12 * $begin_year  + ($begin_month  - 1);
		$end    = 12 * $end_year    + ($end_month    - 1);
		$active = 12 * $active_year + ($active_month - 1);

		echo "<div";
		static::renderClassAttr($form, $template_engine,
			isset($widget_conf['class']) ? $widget_conf['class'] : null,
			'filter_paginator', 'month_paginator');
		echo ">";

		$format = isset($widget_conf['format']) ? $widget_conf['format'] : _('%B %Y');

		// Previous page
		if ($active - 1 >= $begin) {
			$m = $active - 1;
			$month = $m % 12 + 1;
			$year = ($m - $month + 1) / 12;
			$iso_date = sprintf("%04d-%02d-01", $year, $month);
			static::renderPageButton($form, $template_engine, $filters, _('Previous'), 'page prev', array(
				$page_filter_name => $iso_date,
			));
		}

		// Pages
		for ($m = $begin; $m <= $end; $m++) {
			$month = $m % 12 + 1;
			$year = ($m - $month + 1) / 12;

			$iso_date = sprintf("%04d-%02d-01", $year, $month);
			static::renderPageButton($form, $template_engine, $filters,
				mb_convert_case(strftime($format, strtotime($iso_date)), MB_CASE_TITLE), 'page number', array(
				$page_filter_name => $iso_date,
			));
		}

		// Next page
		if ($active + 1 <= $end) {
			$m = $active + 1;
			$month = $m % 12 + 1;
			$year = ($m - $month + 1) / 12;
			$iso_date = sprintf("%04d-%02d-01", $year, $month);
			static::renderPageButton($form, $template_engine, $filters, _('Next'), 'page next', array(
				$page_filter_name => $iso_date,
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

