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
 * Reference input renderer
 *
 * TODO: Suffix & prefix for a field -- simply wrap input in
 * `<label>` with specific class to make it part of the field and
 * prepend/append some string in span. For example units,
 * currency, ...
 */
class Reference extends Input implements \Duf\Renderer\IFieldWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IFieldWidgetRenderer::renderFieldWidget
	public static function renderFieldWidget(\Duf\Form $form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf)
	{
		if (!empty($field_conf['autocomplete'])) {
			// FIXME: Does not work for compound keys
			echo "<input type=\"text\"",
				" id=\"", $form->getHtmlFieldId($group_id, $field_id), "\"",
				" name=\"", $form->getHtmlFieldName($group_id, $field_id), "\"",
				" tabindex=\"", $form->base_tabindex + (isset($field_conf['tabindex']) ? $field_conf['tabindex'] : 0), "\"";
			static::commonAttributes($field_conf);
			$value = $form->getRawData($group_id, $field_id);
			if (is_array($value)) {
				echo 	" value='", json_encode($value, JSON_HEX_AMP | JSON_HEX_APOS | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES), "'";
			} else {
				echo 	" value=\"", htmlspecialchars($value), "\"";
			}
			echo ">\n";

		} else {
			// Lazy-load possible values, just like for <select>
			if (isset($field_conf['options_factory'])) {
				$of = $field_conf['options_factory'];
				$options = $of();
			} else if (isset($field_conf['options'])) {
				$options = $field_conf['options'];
			} else {
				throw new \InvalidArgumentException('Missing "options_factory".');
			}

			echo "<select",
				" id=\"", $form->getHtmlFieldId($group_id, $field_id), "\"",
				" name=\"", $form->getHtmlFieldName($group_id, $field_id), "\"",
				" tabindex=\"", $form->base_tabindex + (isset($field_conf['tabindex']) ? $field_conf['tabindex'] : 0), "\"";
			static::commonAttributes($field_conf);
			echo ">\n";

			$value = $form->getRawData($group_id, $field_id);

			echo "<option value=\"\"";
			if (empty($value)) {
				echo " selected";
			}
			if (!empty($field_conf['required'])) {
				echo " disabled style=\"display: none;\"";
			}
			echo ">";
			echo "</option>\n";

			foreach ($options as $opt_value => $opt_label) {
				echo "<option value=\"", htmlspecialchars($opt_value), "\"", ($value == $opt_value ? " selected":""), ">",
					htmlspecialchars($opt_label), "</option>\n";
			}

			echo "</select>\n";
		}
	}

}

