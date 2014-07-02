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
 * CSS-only tabs controled by radio inputs.
 *
 * FIXME: Is this field or layout ?
 */
class RadioTabs implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		$group_id = $widget_conf['group_id'];
		$field_id = $widget_conf['field_id'];

		$id = $form->getHtmlFieldId($group_id, $field_id);
		$input_name = $form->getHtmlFieldName($group_id, $field_id);
                $value = $form->getRawData($group_id, $field_id);

		// Top-level wrapper
		echo "<div class=\"duf-radiotabs\" id=\"", $id, "\"";
		if (isset($set['class'])) {
			if (is_array($set['class'])) {
				echo " class=\"", htmlspecialchars(join(' ', $set['class'])), "\"";
			} else {
				echo " class=\"", htmlspecialchars($set['class']), "\"";
			}
		}
		echo ">\n";

		// Tabs
		foreach ($widget_conf['tabs'] as $key => $tab) {
			$tab_id = $id.'__'.htmlspecialchars($key);
			$tab_value = isset($tab['value']) ? $tab['value'] : $key;
			$tab_label = isset($tab['label']) ? $tab['label'] : $tab_value;
			$tab_widgets = $tab['widgets'];

			// Tab wrapper
			echo "<div class=\"duf-radiotab\">\n";
	
			// The input
			echo "<input class=\"duf-radiotab-input\" type=\"radio\" name=\"", $input_name, "\" id=\"", $tab_id, "\"";
			if ($tab_value == $value) {
				echo " checked";
			}
			echo "/>";
	
			// Tab label
			echo "<label class=\"duf-radiotab-label\" for=\"", $tab_id, "\">", htmlspecialchars($tab_label), "</label>\n";

			// Tab content
			echo "<div class=\"duf-radiotab-content\">\n";
			$form->renderWidgets($template_engine, $tab_widgets);
			echo "</div>\n";
			echo "</div>\n";
		}

		echo "</div>\n";
	}

}

