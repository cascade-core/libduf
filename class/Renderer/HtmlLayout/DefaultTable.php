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

namespace Duf\Renderer\HtmlLayout;

/**
 * Render simple tabular layout containing all fields.
 */
class DefaultTable implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		echo "<table class=\"form\">\n";
		if (isset($widget_conf['field_group'])) {
			$groups = array($widget_conf['field_group'] => $form->getFieldGroup($widget_conf['field_group']));
		} else {
			$groups = $form->getAllFieldgroups();
		}
		foreach ($groups as $group_id => $group_config) {
			foreach ($group_config['fields'] as $field_id => $field_def) {
				echo "<tr>\n";
				echo "<th>\n";
				$form->renderField($template_engine, $group_id, $field_id, '@label');
				echo "</th>\n";
				echo "<td>\n";
				$form->renderField($template_engine, $group_id, $field_id, '@edit');
				$form->renderField($template_engine, $group_id, $field_id, '@error');
				echo "</td>\n";
				echo "</tr>\n";
			}
		}
		echo "</table>\n";
	}

}

