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

namespace Duf\Renderer\Utils;

/**
 * Switch - determine which child widgets to use by given value, optionaly
 * processed by map function. This switch can be used for displaying values in
 * a pretty way or to show/hide part of the form.
 *
 * Field value is used to select widgets from `widgets_map`. These widgets are
 * rendered as usual. Invalid values (not present in `widgets_map`) are silently
 * ignored and `default_widgets` are used instead.
 *
 * The value is used as array index without conversion. PHP will map true to
 * "1", false to "0".
 *
 * If `map_function` is specified, value is processed by this function before
 * use. The map function is called with two arguments: the value and
 * `$widget_conf`.
 *
 * If `cast_value` option is specified, result value (after applying
 * `map_function`) is casted to `bool`, `int`, `float` or `string. Additionaly
 * it can be compared to `null` using `is_null` and `not_null` (these produce
 * bool values).
 *
 * If `field_id` is null or missing, the map function receives values of entire
 * group.
 */
class SwitchWidget implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		if (isset($widget_conf['group_id'])) {
			$group_id = $widget_conf['group_id'];
		} else {
			throw new \InvalidArgumentException('Missing group_id.');
		}

		// Get value
		$field_id = isset($widget_conf['field_id']) ? $widget_conf['field_id'] : null;
		$value = $form->getRawData($group_id, $field_id);

		// Apply map function if specified
		if (isset($widget_conf['map_function'])) {
			$map_function = $widget_conf['map_function'];
			$value = call_user_func($map_function, $value, $widget_conf);
		}

		// Cast result
		if (isset($widget_conf['cast_value'])) {
			switch($widget_conf['cast_value']) {
				case 'bool':     $value = (bool)   $value; break;
				case 'int':      $value = (int)    $value; break;
				case 'float':    $value = (float)  $value; break;
				case 'string':   $value = (string) $value; break;
				case 'is_null':  $value = ($value === null); break;
				case 'not_null': $value = ($value !== null); break;
			}
		}

		// Render children
		if (isset($widget_conf['widgets_map'][$value])) {
			// Render selected widgets
			$form->renderWidgets($template_engine, $widget_conf['widgets_map'][$value]);
		} else if (isset($widget_conf['default_widgets'])) {
			// Fallback to default_widgets
			$form->renderWidgets($template_engine, $widget_conf['default_widgets']);
		}
	}

}

