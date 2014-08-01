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
 * Reference input renderer
 *
 * TODO: Suffix & prefix for a field -- simply wrap input in
 * `<label>` with specific class to make it part of the field and
 * prepend/append some string in span. For example units,
 * currency, ...
 */
class Reference extends Input implements \Duf\Renderer\IFieldWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IFieldWidgetRenderer::renderFieldWidget
	public static function renderFieldWidget(\Duf\Form $form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf)
	{
		// TODO
		foreach ($field_conf['machine_id'] as $field_id) {
			echo "<input",
				" id=\"", $form->getHtmlFieldId($group_id, $field_id), "\"",
				" name=\"", $form->getHtmlFieldName($group_id, $field_id), "\"";

			// Value
			echo " value=\"", htmlspecialchars($form->getRawData($group_id, $field_id)), "\"";

			static::commonAttributes($field_conf);

			echo ">\n";
		}
	}

}

