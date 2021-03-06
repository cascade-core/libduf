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

namespace Duf\Renderer\HtmlForm;

/**
 * Render error message related to a field.
 */
class Error implements \Duf\Renderer\IFieldWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IFieldWidgetRenderer::renderFieldWidget
	public static function renderFieldWidget(\Duf\Form $form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf)
	{
		$errors = $form->getFieldErrors($group_id, $field_id);

		//static public function error(Form $form, $group_id, $field_id, $field_def, $value, $errors, $template_engine = null)
		if (empty($errors)) {
			return;
		}
		echo "<ul class=\"errors\">\n";
		foreach ($errors as $error_code => $error) {
			$class = (array) @ $error['class'];
			$class[] = 'error_'.$error_code;
			echo "<li class=\"", htmlspecialchars(join(' ', $class)), "\">", htmlspecialchars($error['message']), "</li>\n";
		}
		echo "</ul>\n";
	}

}

