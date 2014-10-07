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

namespace Duf\Renderer\HtmlLayout;

/**
 * Render plain layout made of `<div>`s.
 *
 * @see Fieldsets layout is usually more useful.
 */
class Plain implements \Duf\Renderer\IWidgetRenderer
{
	use \Duf\Renderer\TagUtils;

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		$layout_has_holder = !empty($widget_conf['has_holder']) || isset($widget_conf['class']);
		if ($layout_has_holder) {
			echo "<div";
			if (isset($widget_conf['class'])) {
				static::renderClassAttr($form, $template_engine, $widget_conf['class']);
			}
			echo ">\n";
		}

		foreach($widget_conf['rows'] as $row) {
			$row_has_holder = !empty($row['has_holder']) || isset($row['class']);
			if ($row_has_holder) {
				echo "<div";
				if (isset($row['class'])) {
					static::renderClassAttr($form, $template_engine, $row['class']);
				}
				echo ">\n";
			}

			foreach ($row['widgets'] as $widget) {
				$form->renderWidget($template_engine, $widget);
			}

			if ($row_has_holder) {
				echo "</div>\n";
			}
		}

		if ($layout_has_holder) {
			echo "</div>\n";
		}
	}

}

