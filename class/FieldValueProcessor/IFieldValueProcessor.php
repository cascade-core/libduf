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
 * Preprocess and postprocess field group value. Converts values between raw
 * and processed representation.
 */
interface IFieldValueProcessor
{

	/**
	 * Convert input/default value to raw. This method reads default
	 * values from `$group_values` and writes raw values into
	 * `$raw_values`.
	 *
	 * Minimal implementation:
	 *
	 *     if (isset($default_values[$field_id])) {
	 *         $raw_values[$field_id] = $default_values[$field_id];
	 *     }
	 *
	 * @see valuePostProcess()
	 */
	public static function valuePreProcess($default_values, & $raw_values, \Duf\Form $form, $group_id, $field_id, $field_conf);


	/**
	 * Convert raw value to output value. This method reads raw values from
	 * `$raw_values` and writes converted values into `$group_values`.
	 *
	 * Empty value, as not provided by user, should be converted to null.
	 * For example when processor converts value to int, it shoud return
	 * zero on '0', but null on empty string.
	 *
	 * Minimal implementation:
	 *
	 *     if (isset($raw_values[$field_id])) {
	 *         $group_values[$field_id] = $raw_values[$field_id];
	 *     }
	 *
	 * @see valuePreProcess()
	 */
	public static function valuePostProcess($raw_values, & $group_values, \Duf\Form $form, $group_id, $field_id, $field_conf);

}

