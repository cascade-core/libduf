<?php
/*
 * Copyright (c) 2015, Josef Kufner  <jk@frozen-doe.net>
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
 * Wrapper around some external widget.
 *
 * Renderer function: $renderer($form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf, $value, $id, $name, $tabindex);
 */
class ExternalWidget implements \Duf\Renderer\IFieldWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IFieldWidgetRenderer::renderFieldWidget
	public static function renderFieldWidget(\Duf\Form $form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf)
	{
		if (isset($widget_conf['renderer'])) {
			$renderer = $widget_conf['renderer'];
		} else if (isset($field_conf['renderer'])) {
			$renderer = $field_conf['renderer'];
		} else {
			throw new \InvalidArgumentException('Missing renderer function.');
		}

		$value = $form->getRawData($group_id, $field_id);
		$id = $form->getHtmlFieldId($group_id, $field_id);
		$name = $form->getHtmlFieldName($group_id, $field_id);
		$tabindex = $form->base_tabindex + (isset($field_conf['tabindex']) ? $field_conf['tabindex'] : 0);

		$renderer($form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf, $value, $id, $name, $tabindex);

		if (isset($field_conf['field_note'])) {
			echo "<span class=\"field_note\">", htmlspecialchars($field_conf['field_note']), "</span>";
		}

		echo "\n";
	}

}

