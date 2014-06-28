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

namespace Duf\Renderer\HtmlCollection;

/**
 * Render `<input type="checkbox">` for selecting an item in collection.
 *
 * TODO: Unfinished. What to do with `__selected` key?
 */
class SelectionCheckbox implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		$group_id = $widget_conf['group_id'];
		$field_id = $widget_conf['field_id'];

		echo "<label class=\"selection\">",
			"<input type=\"checkbox\"",
				" name=\"", $form->getHtmlFieldName($group_id, '__selected'), "\"",
				" value=\"1\"",
				$form->getRawData($group_id, '__selected') ? ' checked' : '',
			">",
			"<i></i>",
			"</label>\n";
	}

}
