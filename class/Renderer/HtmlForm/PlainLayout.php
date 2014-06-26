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
		echo "<div";
		if (isset($row['class'])) {
			if (is_array($row['class'])) {
				echo " class=\"", htmlspecialchars(join(' ', $row['class'])), "\"";
			} else {
				echo " class=\"", htmlspecialchars($row['class']), "\"";
			}
		}
		echo ">\n";

		foreach($widget_conf['rows'] as $row) {
			$row_holder = (isset($row['class']) || count($row['widgets']) > 1);
			if($row_holder) {
				echo "<div";
				if (isset($row['class'])) {
					if (is_array($row['class'])) {
						echo " class=\"", htmlspecialchars(join(' ', $row['class'])), "\"";
					} else {
						echo " class=\"", htmlspecialchars($row['class']), "\"";
					}
				}
				echo ">\n";
			}

			foreach ($row['widgets'] as $widget) {
				$form->renderWidget($template_engine, $widget);
			}

			if ($row_holder) {
				echo "</div>\n";
			}
		}
		echo "</div>\n";
	}

}

