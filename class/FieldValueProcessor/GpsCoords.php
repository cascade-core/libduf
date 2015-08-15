<?php
/*
 * Copyright (c) 2015, Josef Kufner  <jk@frozen-doe.net>
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
 * Encode/decode GPS coordinates to object with lat & lon properties.
 */
class GpsCoords
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
		if (isset($default_values[$field_id])) {
			$v = $default_values[$field_id];
			if (isset($v['lat']) && isset($v['lon'])) {
				$raw_values[$field_id] = $v['lat'].','.$v['lon'];
			} else {
				$raw_values[$field_id] = null;
			}
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
		if (isset($raw_values[$field_id])) {
			$ll = array_map('trim', explode(',', $raw_values[$field_id]));
			if (count($ll) == 2) {
				list($lat, $lon) = $ll;
				$group_values[$field_id] = array(
					'lat' => $lat,
					'lon' => $lon,
				);
			}
		}
	}

}

