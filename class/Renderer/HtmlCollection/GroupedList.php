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
 * Render collection using grouped `<ul>` list. Collection is rendered in
 * groups, but otherwise flat. Groups can have headings.
 */
class GroupedList implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		$group_id = $widget_conf['group_id'];
		$group = $form->getFieldGroup($group_id);
		$dimensions = isset($group['collection_dimensions']) ? (int) $group['collection_dimensions'] : 0;

		$collection_key = array();
		$dimensions = $group['collection_dimensions'];

		$last_list_group_id = false;
		$list_group_field_id = isset($widget_conf['list_group_field_id']) ? $widget_conf['list_group_field_id'] : null;
		$list_group_class_fmt = isset($widget_conf['list_group_class_fmt']) ? $widget_conf['list_group_class_fmt'] : null;
		$list_group_heading_fmt = isset($widget_conf['list_group_heading_fmt']) ? $widget_conf['list_group_heading_fmt'] : null;
		$list_group_heading_level = (int) (isset($widget_conf['list_group_heading_level']) ? $widget_conf['list_group_heading_level'] : 3);

		if (isset($widget_conf['class'])) {
			echo "<div";
			$class = $widget_conf['class'];
			if (is_array($class)) {
				echo " class=\"", htmlspecialchars(join(' ', $class)), "\"";
			} else {
				echo " class=\"", htmlspecialchars($class), "\"";
			}
			echo ">\n";
		}

		\Duf\CollectionWalker::walkCollection($form->getViewData($group_id), $dimensions,
			function($collection_key) use ($form, $template_engine, $widget_conf, $group_id, $dimensions,
				& $last_list_group_id, $list_group_field_id, $list_group_class_fmt, $list_group_heading_fmt, $list_group_heading_level)
			{
				$form->setCollectionKey($group_id, $collection_key);

				// Start new group if $list_group_id changed
				$values = $form->getViewData($group_id);
				$list_group_id = $list_group_field_id && isset($values[$list_group_field_id]) ? $values[$list_group_field_id] : null;
				if ($last_list_group_id !== $list_group_id) {
					if ($last_list_group_id !== false) {
						echo "</ul>\n";
					}
					echo "<ul";
					if ($list_group_class_fmt) {
						echo " class=\"", template_format($list_group_class_fmt, $values), "\"";
					}
					echo ">\n";
					if ($list_group_heading_fmt) {
						echo "<h$list_group_heading_level>", template_format($list_group_heading_fmt, $values),
							"</h$list_group_heading_level>\n";
					}
					$last_list_group_id = $list_group_id;
				}

				// Render item
				echo "<li";
				if (isset($widget_conf['dimensions'][$dimensions]['class'])) {
					$class = $widget_conf['dimensions'][$dimensions]['class'];
					if (is_array($class)) {
						echo " class=\"", htmlspecialchars(join(' ', $class)), "\"";
					} else {
						echo " class=\"", htmlspecialchars($class), "\"";
					}
				}
				echo ">\n";
				$form->renderWidgets($template_engine, $widget_conf['widgets']);
				echo "</li>\n";
			});
		$form->unsetCollectionKey($group_id);

		if ($last_list_group_id !== false) {
			echo "</ul>\n";
		}

		if (isset($widget_conf['class'])) {
			echo "</div>\n";
		}
	}

}

