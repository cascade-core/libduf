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
 * CSS-only tabs controled by radio inputs - it is half-field, half-layout. It
 * makes tab using widget configuration, but a lot of other stuff is from field
 * configuration.
 */
class RadioTabs implements \Duf\Renderer\IFieldWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IFieldWidgetRenderer::renderFieldWidget
	public static function renderFieldWidget(\Duf\Form $form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf)
	{
		$id = $form->getHtmlFieldId($group_id, $field_id);
		$input_name = $form->getHtmlFieldName($group_id, $field_id);
                $field_value = $form->getRawData($group_id, $field_id);

		// Top-level wrapper
		echo "<div class=\"radiotabs\" id=\"", $id, "\"";
		if (isset($set['class'])) {
			if (is_array($set['class'])) {
				echo " class=\"", htmlspecialchars(join(' ', $set['class'])), "\"";
			} else {
				echo " class=\"", htmlspecialchars($set['class']), "\"";
			}
		}
		echo ">\n";

		// Tabs by widget configuration
		foreach ($widget_conf['tabs'] as $tab_value => $tab) {
			if (isset($field_conf['options'][$tab_value])) {
				$option = $field_conf['options'][$tab_value];
			} else {
				throw new \InvalidArgumentException('Tab key is not valid value for this field.');
			}
			$tab_id = $id.'__'.htmlspecialchars($tab_value);

			// Get tab label
			if (isset($tab['label'])) {
				$tab_label = $tab['label'];
			} else {
				if (is_array($option)) {
					$tab_label = isset($option['label']) ? $option['label'] : $tab_value;
				} else {
					$tab_label = $option;
				}
			}

			// Tab wrapper
			echo "<div class=\"radiotab\">\n";
	
			// The input
			echo "<input class=\"radiotab-input\" type=\"radio\"",
				" name=\"", $input_name, "\"",
				" id=\"", $tab_id, "\"",
				" value=\"", htmlspecialchars($tab_value), "\"";
			if ($tab_value == $field_value) {
				echo " checked";
			}
			echo "/>";
	
			// Tab label
			echo "<label class=\"radiotab-label\" for=\"", $tab_id, "\">", htmlspecialchars($tab_label), "</label>\n";

			// Tab content
			echo "<div class=\"radiotab-content\">\n";
			$form->renderWidgets($template_engine, $tab['widgets']);
			echo "</div>\n";
			echo "</div>\n";
		}

		echo "</div>\n";
	}

}

