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

namespace Duf\FieldValidator;

/**
 * Validate HTML5 `<input type="time">`.
 */
class TimeInput extends DateTimeInput implements IFieldValidator
{
	/**
	 * Expected format.
	 */
	protected static $format = '%H:%M:%S';


	/**
	 * Return message to tell user how field should be formatted.
	 */
	protected static function getDateTimeMalformedMessage()
	{
		return _('Please use "HH:MM:SS" format (ISO 8601, time only).');
	}

}

