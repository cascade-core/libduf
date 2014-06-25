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
 * Validate HTML5 `<input type="text">`.
 */
class TextInput implements IFieldValidator
{

	/**
	 * Check simple text input.
	 */
	public static function validateField(\Duf\Form $form, $group_id, $field_id, $field_def, $value)
	{
		if ($value == '') {
			// HTML5 'required' attribute
			if (@ $field_def['required']) {
				$form->setFieldError($group_id, $field_id, \Duf\Form::E_FIELD_REQUIRED, array(
					'message' => _('Please fill this field.'),
				));
			}
			// Other validations require some value
			return false;
		}

		// HTML5 'pattern' attribute
		if (($pattern = @ $field_def['pattern']) && !preg_match("\xFF$pattern\$\xFFADmsu", $value)) {
			$form->setFieldError($group_id, $field_id, \Duf\Form::E_FIELD_MALFORMED, array(
				'message' => sprintf(_('Field does not match pattern: %s'), $pattern),
				'pattern' => $pattern,
			));
		}

		return true;
	}
}

