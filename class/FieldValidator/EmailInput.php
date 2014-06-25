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
 * Validate HTML5 `<input type="email">`.
 */
class EmailInput extends TextInput implements IFieldValidator
{
	public static function validateField(\Duf\Form $form, $group_id, $field_id, $field_def, $value)
	{
		if (parent::validateField($form, $group_id, $field_id, $field_def, $value) === false) {
			return false;
		}

		if (!preg_match('/.+@.+/', $value)) {
			$form->setFieldError($group_id, $field_id, \Duf\Form::E_FIELD_MALFORMED, array(
				'message' => _('Please enter valid e-mail address.'),
			));
		}
	}
}

