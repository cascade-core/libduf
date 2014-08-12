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
 * Process list of values, one item per line. Useful with textarea and simple
 * data, like e-mail addresses, phone numbers or URLs.
 */
class LineList
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
		$raw_values[$field_id] = join("\n", array_map(function($x) { return str_replace("\n", " ", $x); }, (array) $default_values[$field_id]));
	}


	/**
	 * Convert raw value to output value. This method reads raw values from
	 * `$raw_values` and writes converted values into `$group_values`.
	 *
	 * @see valuePreProcess()
	 */
	public static function valuePostProcess($raw_values, & $group_values, \Duf\Form $form, $group_id, $field_id, $field_conf)
	{
		$group_values[$field_id] = array_filter(array_map('trim', explode("\n", $raw_values[$field_id])), function ($x) { return $x !== ''; });
	}

}

