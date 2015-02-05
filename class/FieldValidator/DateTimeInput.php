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
 * Validate HTML5 `<input type="datetime">` and similar fields.
 */
class DateTimeInput extends TextInput implements IFieldValidator
{
	/**
	 * Expected format.
	 */
	protected static $format = '%Y-%m-%d %H:%M:%S';


	/// @copydoc IFieldValidator::validateField
	public static function validateField(\Duf\Form $form, $group_id, $field_id, $field_def, $value)
	{
		if (parent::validateField($form, $group_id, $field_id, $field_def, $value) === false) {
			return false;
		}

		$ts = strptime($value, static::$format);

		if ($ts === false) {
			$form->setFieldError($group_id, $field_id, \Duf\Form::E_FIELD_MALFORMED, array(
				'message' => static::getDateTimeMalformedMessage(),
			));
		}

		return true;
	}


	/**
	 * Return message to tell user how field should be formatted.
	 */
	protected static function getDateTimeMalformedMessage()
	{
		return _('Please use "YYYY-MM-DD HH:MM:SS" format (ISO 8601).');
	}

}

