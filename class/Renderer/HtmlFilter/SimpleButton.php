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
 * Simple button to modify filters
 */
class SimpleButton implements \Duf\Renderer\IWidgetRenderer
{
	use \Duf\Renderer\TagUtils;

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		// Get value early, so class can be set
		$filters = $form->getViewData($widget_conf['group_id']);

		echo "<a href=\"", htmlspecialchars(static::buildFilterLink($filters, $widget_conf['filters'])), "\"";
		static::renderClassAttr($form, $template_engine,
			isset($widget_conf['class']) ? $widget_conf['class'] : null,
			'button filter',
			static::isFilterActive($filters, $widget_conf['filters']) ? 'active' : null);
		echo ">";
		if (isset($widget_conf['widgets'])) {
			$form->renderWidgets($template_engine, $widget_conf['widgets']);
		} else if (isset($widget_conf['label'])) {
			echo htmlspecialchars($widget_conf['label']);
		}
		echo "</a>\n";
	}


	/**
	 * Build link containing current filters overriden by $overrides. This
	 * function accepts more than one $overrides parameter.
	 *
	 * @param $filters Current filters.
	 * @param $overrides New filter setting. More than one argument may be specified.
	 * @return Query part of the URL, which should be just fine to use in
	 * 	`href` attribute. Filters are sorted to produce uniform links.
	 */
	protected static function buildFilterLink($filters, $overrides)
	{
		$f = array();
		$link_filters = call_user_func_array('array_merge', func_get_args());
		ksort($link_filters);
		array_walk($link_filters, function($v, $k) use (& $f) {
			if ($v !== null && $k[0] != '_') {
				$f[] = urlencode($k).'='.urlencode($v === false ? '0' : $v);
			}
		});
		return '?'.join('&', $f);

	}


	protected static function isFilterActive($filters, $overrides)
	{
		if (empty($overrides)) {
			return false;
		}

		$args = func_get_args();
		$current_filters = array_shift($args);
		$required_filters = call_user_func_array('array_merge', $args);

		foreach ($required_filters as $k => $v) {
			$cv = isset($current_filters[$k]) ? $current_filters[$k] : null;
			if ($v === null ? $cv !== null : $v != $cv) {
				return false;
			}
		}
		return true;
	}

}

