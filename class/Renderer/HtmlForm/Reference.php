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
			$value = $form->getRawData($group_id, $field_id);
			// Lazy-load possible values, just like for <select>
			if (isset($field_conf['options_factory'])) {
				$of = $field_conf['options_factory'];
				$options = $of($field_conf, $value);
			} else if (isset($field_conf['options'])) {
				$options = $field_conf['options'];
			} else {
				throw new \InvalidArgumentException('Missing "options_factory".');
			}

			echo "<select",
				" id=\"", $form->getHtmlFieldId($group_id, $field_id), "\"",
				" name=\"", $form->getHtmlFieldName($group_id, $field_id), "\"",
				" data-ref=\"", htmlspecialchars($field_conf['machine_type']), "\"",
				" tabindex=\"", $form->base_tabindex + (isset($field_conf['tabindex']) ? $field_conf['tabindex'] : 0), "\"";
			static::commonAttributes($field_conf);
			echo ">\n";

			echo "<option value=\"\"";
			if (empty($value)) {
				echo " selected";
			}
			if (!empty($field_conf['required'])) {
				echo " disabled style=\"display: none;\"";
			}
			echo "</option>\n";

			$opt_key = isset($field_conf['options_value_keys']) ? reset($field_conf['options_value_keys']) : null;

			foreach ($options as $opt_value => $opt) {
				if (is_scalar($opt)) {
					$opt_label = $opt;
				} else {
					// Reference value format is defined using referred properties included into
					// original entity. Since $opt iterates over listing of referred entities,
					// properties must be mapped.
					if (isset($field_conf['properties'])) {
						$p = array();
						foreach ($field_conf['properties'] as $pk => $pv) {
							$p[$pk] = $opt[$pv];
						}
					} else {
						$p = $opt;
					}
					$opt_label = filename_format($field_conf['value_fmt'], $p);

					// Use one of properties as a key
					if ($opt_key !== null) {
						$opt_value = $opt[$opt_key];
					}
				}

				echo "<option value=\"", htmlspecialchars($opt_value), "\"", ($value == $opt_value ? " selected":"");
				if (!empty($field_conf['options_data_fields'])) {
					foreach ($field_conf['options_data_fields'] as $dk => $df) {
						if (empty($df)) {
							continue;
						}
						echo " data-", preg_replace('/["\'>\\/=\\0 \s]/', '_', $dk), "=\"", htmlspecialchars($opt[$df]), "\"";
					}
				}
				echo ">";
				echo htmlspecialchars($opt_label);
				echo "</option>\n";
			}

			echo "</select>\n";
		}

		if (isset($field_conf['field_note'])) {
			echo "<span class=\"field_note\">", htmlspecialchars($field_conf['field_note']), "</span>";
		}

	}

}

