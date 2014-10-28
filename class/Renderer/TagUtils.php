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

namespace Duf\Renderer;

/**
 * Helper functions to render common features.
 *
 * Attribute renderers always start with a space and never end with it. Tags
 * end with line breaks.
 */
trait TagUtils {

	/**
	 * Render class attribute.
	 *
	 * @param $form The Form.
	 * @param $template_engine The template engine.
	 * @param $class A class, list of classes, or list of objects which
	 * 	should be interpreted, or mix of any of this.
	 */
	protected static function renderClassAttr(\Duf\Form $form, $template_engine, $class)
	{
		$no_class = true;
		$sep = false;
		$args = func_get_args();
		$argc = func_num_args();

		for ($a = 2; $a < $argc; $a++) {
			$class = $args[$a];
			if (empty($class)) {
				continue;
			}
			if ($no_class) {
				echo " class=\"";
				$no_class = false;
			} else {
				echo ' ';
			}

			if (is_array($class)) {
				foreach ($class as $c) {
					if (is_array($c)) {
						if (static::calculateValue($form, $template_engine, $c)) {
							echo ' ', htmlspecialchars($c['class']);
						}
					} else {
						echo ' ', htmlspecialchars($c);
					}
				}
			} else {
				echo htmlspecialchars($class);
			}
		}

		if (!$no_class) {
			echo "\"";
		}
	}


	/**
	 * Calculate value using simple rules and conversions.
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
	protected static function calculateValue(\Duf\Form $form, $template_engine, $rules)
	{
		if (isset($rules['group_id'])) {
			$group_id = $rules['group_id'];
		} else {
			throw new \InvalidArgumentException('Missing group_id.');
		}

		// Get value
		$field_id = isset($rules['field_id']) ? $rules['field_id'] : null;
		$value = $form->getRawData($group_id, $field_id);

		// Apply map function if specified
		if (isset($rules['map_function'])) {
			$map_function = $rules['map_function'];
			$value = call_user_func($map_function, $value, $rules);
		}

		// Cast result
		if (isset($rules['cast_value'])) {
			switch($rules['cast_value']) {
				case 'bool':     $value = (bool)   $value; break;
				case 'int':      $value = (int)    $value; break;
				case 'float':    $value = (float)  $value; break;
				case 'string':   $value = (string) $value; break;
				case 'is_null':  $value = ($value === null); break;
				case 'not_null': $value = ($value !== null); break;
			}
		}

		return $value;
	}

}

