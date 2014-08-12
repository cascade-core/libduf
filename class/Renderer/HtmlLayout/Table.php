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
 * Render `<table>` layout, no magic, just rows and cells.
 */
class Table implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		echo "<table";
		if (isset($widget_conf['class'])) {
			if (is_array($widget_conf['class'])) {
				echo " class=\"", htmlspecialchars(join(' ', $widget_conf['class'])), "\"";
			} else {
				echo " class=\"", htmlspecialchars($widget_conf['class']), "\"";
			}
		}
		echo ">\n";

		foreach($widget_conf['rows'] as $row) {

			// Skip row, if required value is missing/empty
			if (isset($row['require_value'])) {
				$rq = $row['require_value'];
				if (!$form->getViewData($rq['group_id'], $rq['field_id'])) {
					continue;
				}
			}

			echo "<tr";
			if (isset($row['class'])) {
				if (is_array($row['class'])) {
					echo " class=\"", htmlspecialchars(join(' ', $row['class'])), "\"";
				} else {
					echo " class=\"", htmlspecialchars($row['class']), "\"";
				}
			}
			echo ">\n";

			foreach ($row['cells'] as $cell) {
				$cell_tag = empty($cell['is_header']) ? 'td' : 'th';
				echo "<$cell_tag";
				if (isset($cell['class'])) {
					if (is_array($cell['class'])) {
						echo " class=\"", htmlspecialchars(join(' ', $cell['class'])), "\"";
					} else {
						echo " class=\"", htmlspecialchars($cell['class']), "\"";
					}
				}
				echo ">\n";

				foreach ($cell['widgets'] as $widget) {
					$form->renderWidget($template_engine, $widget);
				}

				echo "</$cell_tag>\n";
			}

			echo "</tr>\n";
		}

		echo "</table>\n";
	}

}

