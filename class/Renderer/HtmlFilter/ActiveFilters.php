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
 * Render list of active filters.
 */
class ActiveFilters extends SimpleButton implements \Duf\Renderer\IWidgetRenderer
{

	/**
	 * Map common operators to unicode symbols
	 */
	protected static $operator_map = array(
		'<=' => '≤',
		'>=' => '≥',
		'<<=' => '<',
		'>>=' => '>',
		'!=' => '≠',
		'~=' => '~=',
		'~!=' => '~≠',
		'%=' => '%=',
		'%!=' => '%≠',
	);

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		// Get value early, so class can be set
		$filters = $form->getViewData($widget_conf['group_id']);

		//debug_dump($filters, 'Filters');

		echo "<div";
		static::renderClassAttr($form, $template_engine, isset($widget_conf['class']) ? $widget_conf['class'] : null, 'filter_active');
		echo ">";

		foreach ($filters as $k => $v) {
			if (preg_match('/^(.*[^[:punct:]])([[:punct:]]*)$/', $k, $m)) {
				$f = $m[1];
				$op = $m[2].'=';
				if (isset(static::$operator_map[$op])) {
					$op = static::$operator_map[$op];
				}
			} else {
				$f = $k;
				$op = '=';
			}
			$remove_url = '?';
			echo "<span>",
				"<i>",   htmlspecialchars($f),  "</i>",
				" <tt>", htmlspecialchars($op), "</tt>",
				" <b>",  htmlspecialchars($v),  "</b>";
			if ($k[0] != '_') {
				echo " <a href=\"", htmlspecialchars(static::buildFilterLink($filters, array($k => null))), "\"",
					" class=\"remove\"><span>&times</span></a>";
			}
			echo "</span>";
		}

		echo "</div>\n";
	}

}

