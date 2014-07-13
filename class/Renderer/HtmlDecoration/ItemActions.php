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
		$item = $form->getRawData($group_id);
		$actions = $form->getFieldGroupOption($group_id, 'item_actions');

		static::renderActions($actions, $item);
	}


	protected static function renderActions($actions, $item)
	{
		echo "<div class=\"actions\">\n";
		foreach ($actions as $a => $action) {
			echo "<a";

			// URL
			$link = filename_format($action['link'], $item);
			echo " href=\"", htmlspecialchars($link), "\"";

			// Class
			$class = isset($action['class']) ? (array) $action['class'] : array();
			$class[] = 'action_'.$a;
			echo " class=\"", htmlspecialchars(join(' ', $class)), "\"";

			// Title (label is usually very short)
			echo " title=\"", htmlspecialchars($action['description']), "\"";

			echo ">";

			// Label
			echo "<span>", htmlspecialchars($action['label']), "</span>";

			echo "</a>\n";
		}
		echo "</div>\n";
	}

}
