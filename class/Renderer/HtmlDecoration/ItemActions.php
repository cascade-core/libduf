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

namespace Duf\Renderer\HtmlDecoration;

/**
 * Render links to actions of group item.
 *
 * Expects 'item_actions' option specified in group configuration.
 *
 * Example -- fragment of field group configuration:
 *
 *     "item_actions": {
 *         "action": {
 *             "label": "Action label",
 *             "link": "/type/{id}!action"
 *         }
 *     }
 *
 */
class ItemActions implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		$group_id = $widget_conf['group_id'];
		$item = $form->getViewData($group_id);
		$actions = $form->getFieldGroupOption($group_id, 'item_actions');

		static::renderActions($widget_conf, $actions, $item, isset($widget_conf['actions']) ? $widget_conf['actions'] : null);
	}


	protected static function renderActions($widget_conf, $actions, $item, $action_names)
	{
		echo "<div";
		$class = isset($widget_conf['class']) ? (array) $widget_conf['class'] : array();
		$class[] = 'actions';
		echo " class=\"", htmlspecialchars(join(' ', $class)), "\"";
		echo ">";

		if ($action_names !== null) {
			foreach ($action_names as $a) {
				$action = $actions[$a];
				static::renderAction($a, $action, $item);
			}
		} else {
			foreach ($actions as $a => $action) {
				if ($action['hidden']) {
					continue;
				}
				static::renderAction($a, $action, $item);
			}
		}
		echo "</div>\n";
	}


	protected static function renderAction($action_name, $action, $item)
	{
		if (isset($action['is_allowed_callback'])) {
			$f = $action['is_allowed_callback'];
			if (!$f($item)) {
				return;
			}
		}

		echo "<a";

		// URL
		$link = filename_format($action['link'], $item);
		echo " href=\"", htmlspecialchars($link), "\"";

		// Class
		$class = isset($action['class']) ? (array) $action['class'] : array();
		$class[] = 'action';
		$class[] = 'action_'.$action_name;
		echo " class=\"", htmlspecialchars(join(' ', $class)), "\"";

		// Title (label is usually very short)
		echo " title=\"", htmlspecialchars(isset($action['description']) ? $action['description'] : $action['label']), "\"";

		echo ">";

		// Label
		echo "<span>", htmlspecialchars($action['label']), "</span>";

		echo "</a>\n";
	}

}
