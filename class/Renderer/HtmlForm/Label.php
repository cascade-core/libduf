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
 * Render `<label>` for a field.
 */
class Label implements \Duf\Renderer\IFieldWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IFieldWidgetRenderer::renderFieldWidget
	public static function renderFieldWidget(\Duf\Form $form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf)
	{
		echo "<label",
			" for=\"", $form->getHtmlFieldId($group_id, $field_id), "\"";
		if (!empty($field_conf['title'])) {
			echo " title=\"", htmlspecialchars($field_conf['title']), "\"";
		}
		echo ">";
		$label = isset($field_conf['label']) ? $field_conf['label'] : $field_conf['name'];
		if (!empty($field_conf['label_undecorated'])) {
			echo htmlspecialchars($label);
		} else {
			echo htmlspecialchars(sprintf(_('%s:'), $label));
		}
		echo "</label>\n";
	}

}
