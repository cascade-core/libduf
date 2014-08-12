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
 * Render generic value list textarea.
 */
class ValueList extends TextArea implements \Duf\Renderer\IFieldWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IFieldWidgetRenderer::renderFieldWidget
	public static function renderFieldWidget(\Duf\Form $form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf)
	{
		echo "<div";
		if (isset($field_conf['class'])) {
			$class = $field_conf['class'];
			echo " class=\"", htmlspecialchars(is_array($class) ? join(' ', $class) : $class), "\"";
		}
		echo ">";

		parent::renderFieldWidget($form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf);

		echo "<div class=\"textarea_note\">";
		static::renderNoteText();
		echo "</div>\n";

		echo "</div>\n";
	}


	/**
	 * Render note message under the textarea (just echo).
	 */
	protected static function renderNoteText()
	{
		echo _('Enter list of values, each value on its own line.');
	}

}

