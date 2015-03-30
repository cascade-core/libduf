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
 * Render `<fieldset>` layout.
 */
class Fieldsets implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		foreach($widget_conf['fieldsets'] as $set) {
			if (isset($set['require_value'])) {
				// calculate required value, skip row if it is false
				if (!static::calculateValue($form, $template_engine, $set['require_value'])) {
					continue;
				}
			}

			echo "<fieldset";
			if (isset($set['class'])) {
				if (is_array($set['class'])) {
					echo " class=\"", htmlspecialchars(join(' ', $set['class'])), "\"";
				} else {
					echo " class=\"", htmlspecialchars($set['class']), "\"";
				}
			}
			echo ">\n";

			if (isset($set['label'])) {
				echo "<legend>", htmlspecialchars($set['label']), "</legend>\n";
			}

			$form->renderWidgets($template_engine, $set['widgets']);

			echo "</fieldset>\n";
		}
	}

}

