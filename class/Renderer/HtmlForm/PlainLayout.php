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

namespace Duf\Renderer\HtmlForm;

/**
 * Render plain layout made of divs.
 */
class PlainLayout implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		echo "<div class=\"duf_form\">\n";
		static::renderRow($form, $template_engine, $widget_conf['rows']);
		echo "</div>\n";
	}


	private static function renderRow(\Duf\Form $form, $template_engine, $rows)
	{
		foreach ($rows as $row_conf) {
			if (is_array($row_conf)) {
				if (isset($row_conf['#!'])) {
					$form->renderWidget($template_engine, $row_conf);
				} else {
					echo "<div>\n";
					static::renderRow($form, $template_engine, $row_conf);
					echo "</div>\n";
				}
			}
		}
	}

}

