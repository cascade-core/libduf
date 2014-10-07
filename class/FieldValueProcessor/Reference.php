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

namespace Duf\FieldValueProcessor;

/**
 * Process reference values, to extract machine properties into reference
 * value.
 */
class Reference
{

	/**
	 * Convert input/default value to raw. This method reads default
	 * values from `$group_values` and writes raw values into
	 * `$raw_values`.
	 *
	 * @see valuePostProcess()
	 */
	public static function valuePreProcess($default_values, & $raw_values, \Duf\Form $form, $group_id, $field_id, $field_conf)
	{
		// Copy ID properties to field
		if (count($field_conf['machine_id']) == 1) {
			$p = reset($field_conf['machine_id']);
			$raw_values[$field_id] = isset($default_values[$p]) ? $default_values[$p] : null;
		} else {
			$id_value = array();
			foreach ($field_conf['machine_id'] as $p) {
				$id_value[] = isset($default_values[$p]) ? $default_values[$p] : null;
			}
			$raw_values[$field_id] = json_encode($id_value, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		}

		// Copy additional values which are not field data, but they are important for `@view` renderers.
		foreach ($field_conf['properties'] as $pi => $p) {
			$raw_values[$pi] = isset($default_values[$pi]) ? $default_values[$pi] : null;
		}
	}


	/**
	 * Convert raw value to output value. This method reads raw values from
	 * `$raw_values` and writes converted values into `$group_values`.
	 *
	 * @see valuePreProcess()
	 */
	public static function valuePostProcess($raw_values, & $group_values, \Duf\Form $form, $group_id, $field_id, $field_conf)
	{
		if (count($field_conf['machine_id']) == 1) {
			if ($raw_values[$field_id] !== '') {
				$group_values[reset($field_conf['machine_id'])] = $raw_values[$field_id];
			} else {
				$group_values = null;
			}
		} else {
			if (is_array($raw_values[$field_id])) {
				ksort($raw_values[$field_id]);
				$id_value = array_values($raw_values[$field_id]);
			} else {
				$id_value = array_values(json_decode($raw_values[$field_id], TRUE));
			}
			$i = 0;
			$all_null = true;
			foreach ($field_conf['machine_id'] as $p) {
				if ($id_value[$i] !== '') {
					$all_null = false;
				}
				$group_values[$p] = $id_value[$i++];
			}
			if ($all_null) {
				$group_values = null;
			}
		}
	}

}

