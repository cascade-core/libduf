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
		if (isset($widget_conf['group_id'])) {
			// Load specified group config
			$group_id = $widget_conf['group_id'];
			$group_config = $form->getFieldGroup($group_id);
			$groups = array($group_id => $group_config);

			// Load specified fields within the group
			if (isset($widget_conf['field_list'])) {
				$fields = array();
				foreach ($widget_conf['field_list'] as $field_id) {
					if (isset($group_config['fields'][$field_id])) {
						$fields[$field_id] = $group_config['fields'][$field_id];
					} else {
						throw new \InvalidArgumentException('Invalid field ID: '.$field_id);
					}
				}
			} else {
				$fields = null;
			}
		} else {
			// Load all groups
			$groups = $form->getAllFieldgroups();
			$fields = null;
		}
		$skip_empty = !empty($widget_conf['skip_empty']);
		$required_field_option = empty($widget_conf['required_field_option']) ? null : $widget_conf['required_field_option'];
		foreach ($groups as $group_id => $group_config) {
			$group_readonly = !empty($group_config['readonly']);
			foreach ($fields === null ? $group_config['fields'] : $fields as $field_id => $field_def) {
				if ($required_field_option !== null && empty($field_def[$required_field_option])) {
					continue;
				}
				if (!empty($field_def['hidden']) || (!$form->readonly && !empty($field_def['calculated']))) {
					continue;
				}
				if (!empty($skip_empty) && ($group_readonly || $form->readonly) && $form->getViewData($group_id, $field_id) == '') {
					continue;
				}
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

