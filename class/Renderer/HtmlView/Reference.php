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

namespace Duf\Renderer\HtmlView;

/**
 * Render reference field value.
 */
class Reference extends Input implements \Duf\Renderer\IFieldWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IFieldWidgetRenderer::renderFieldWidget
	public static function renderFieldWidget(\Duf\Form $form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf)
	{
		$raw_values = $form->getViewData($group_id);

		// Detect null refs (this should be done by the reference, but it is better to avoid creating a lot of useless objects)
		$is_null = false;
		foreach ($field_conf['machine_id'] as $m_id_key) {
			if (empty($raw_values[$m_id_key])) {
				$is_null = true;
				break;
			}
		}

		// Get view configuration
		if ($is_null) {
			$link = isset($field_conf['null_link']) ? $field_conf['null_link'] : null;
			$value_fmt = isset($field_conf['null_value_fmt']) ? $field_conf['null_value_fmt'] : null;
		} else {
			$link = isset($field_conf['link']) ? $field_conf['link'] : null;
			$value_fmt = isset($field_conf['value_fmt']) ? $field_conf['value_fmt'] : null;
		}

		// Render reference element
		$tag = ($link === null ? 'span' : 'a');
		echo "<$tag";
		if ($link !== null) {
			echo " href=\"", htmlspecialchars(filename_format($link, $raw_values)), "\"";
		}
		static::commonAttributes($field_conf);
		echo ">";

		// Value
		echo template_format($value_fmt, $raw_values);

		echo "</$tag>\n";
	}

}

