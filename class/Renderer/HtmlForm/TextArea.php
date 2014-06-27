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
 * Default `<textarea>` field renderer.
 */
class TextArea extends Input implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		$group_id = $widget_conf['group_id'];
		$field_id = $widget_conf['field_id'];

		echo "<textarea",
			" id=\"", $form->getHtmlFieldId($group_id, $field_id), "\"",
			" name=\"", $form->getHtmlFieldName($group_id, $field_id), "\"";

		// TODO: Add class by type

		static::commonAttributes($widget_conf);

		// Other HTML attributes
		foreach ($widget_conf as $k => $v) {
			if ($v === null) {
				// null means 'not specified'
				continue;
			}
			switch ($k) {
				// HTML5 string attributes
				case 'cols':
				case 'dirname':
				case 'inputmode':
				case 'maxlength':
				case 'minlength':
				case 'placeholder':
				case 'rows':
				case 'wrap':
					echo " $k=\"", htmlspecialchars($v), "\"";
					break;
			}
		}
		echo ">",
			htmlspecialchars($form->getRawData($group_id, $field_id)),
			"</textarea>\n";
	}

}

