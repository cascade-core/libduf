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
 * Convert URL from local form to absolute URL. If URL starts with slash,
 * current hostname will be prepended before displaying it to user. And if
 * hostname matches current server hostname, it will be removed before passing
 * to the rest of the application.
 */
class LocalUrl
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
			// Add hostname and protocol
			$local = trim($default_values[$field_id]);
			if ($local !== '' && $local[0] == '/') {
				$absolute = static::getScheme() . '://' . static::getServerName() . $local;
			} else {
				$absolute = $local;
			}
			$raw_values[$field_id] = $absolute;
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
			// Remove hostname and protocol
			$absolute = trim($raw_values[$field_id]);
			$abs_parsed = parse_url($absolute);

			if (isset($abs_parsed['user']) || isset($abs_parsed['pass'])) {
				$local = $absolute;
			} else if (isset($abs_parsed['host']) && isset($abs_parsed['scheme'])
				&& $abs_parsed['host'] == static::getServerName()
				&& $abs_parsed['scheme'] == static::getScheme())
			{
				unset($abs_parsed['scheme']);
				unset($abs_parsed['host']);
				$local = static::unparse_url($abs_parsed);
			} else {
				$local = $absolute;
			}

			$group_values[$field_id] = $local;
		}
	}


	/**
	 * Retrieve server name
	 */
	private static function getServerName()
	{
		return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
	}


	/**
	 * Retrieve scheme - 'http' or 'https'
	 */
	private static function getScheme()
	{
		return (isset($_SERVER['HTTPS']) ? 'https' : 'http');
	}


	/**
	 * Invert function to parse_url
	 *
	 * @see http://php.net/manual/en/function.parse-url.php#106731
	 */
	private static function unparse_url($parsed_url) { 
		$scheme   = isset($parsed_url['scheme'])   ? $parsed_url['scheme'] . '://' : ''; 
		$host     = isset($parsed_url['host'])     ? $parsed_url['host'] : ''; 
		$port     = isset($parsed_url['port'])     ? ':' . $parsed_url['port'] : ''; 
		$user     = isset($parsed_url['user'])     ? $parsed_url['user'] : ''; 
		$pass     = isset($parsed_url['pass'])     ? ':' . $parsed_url['pass']  : ''; 
		$pass     = ($user || $pass)               ? "$pass@" : ''; 
		$path     = isset($parsed_url['path'])     ? $parsed_url['path'] : ''; 
		$query    = isset($parsed_url['query'])    ? '?' . $parsed_url['query'] : ''; 
		$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : ''; 
		return "$scheme$user$pass$host$port$path$query$fragment"; 
	} 

}

