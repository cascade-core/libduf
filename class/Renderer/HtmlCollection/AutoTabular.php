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
 * Render collection into HTML `<table>` with additional control elements 
 * around. Multi-dimensional collections are flattened into simple list of 
 * table rows.
 */
class AutoTabular implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		$group_id = $widget_conf['group_id'];
		$group = $form->getFieldGroup($group_id);

		if (empty($group['fields'])) {
			return;
		}
		$fields = $group['fields'];

		$column_weight_key  = isset($widget_conf['column_weight_key'])  ? $widget_conf['column_weight_key']  : 'tabular_column_weight';
		$column_enabled_key = isset($widget_conf['column_enabled_key']) ? $widget_conf['column_enabled_key'] : 'tabular_column_enabled';
		$column_width_key   = isset($widget_conf['column_width_key'])   ? $widget_conf['column_width_key']   : 'tabular_column_width';
		$column_link_key    = isset($widget_conf['column_link_key'])    ? $widget_conf['column_link_key']    : 'tabular_column_link';

		// Get column list
		$columns = array();
		foreach ($fields as $fi => $f) {
			if (isset($f[$column_enabled_key]) && !$f[$column_enabled_key]) {
				// columns are enabled by default
				continue;
			}
			$columns[$fi] = $f;
		}
		uasort($columns, function ($a, $b) use ($column_weight_key) {
			return (isset($a[$column_weight_key]) ? $a[$column_weight_key] : 50)
				- (isset($b[$column_weight_key]) ? $b[$column_weight_key] : 50);
		});

		// Begin
		echo "<table";
		$class = isset($widget_conf['class']) ? (array) $widget_conf['class'] : array();
		$class[] = 'duf_table';
		echo " class=\"", htmlspecialchars(join(' ', $class)), "\"";
		echo ">\n";

		if (!empty($widget_conf['caption'])) {
			echo "<caption>", htmlspecialchars($widget_conf['caption']), "</caption>\n";
		}

		// Header
		echo "<thead>\n";
		echo "<tr>\n";
		foreach ($columns as $fi => $field) {
			echo "<th";
			if (isset($field[$column_width_key])) {
				echo " width=\"", htmlspecialchars($field[$column_width_key]), "\"";
			}
			echo ">";
			if (isset($field['label'])) {
				echo htmlspecialchars($field['label']);
				//$form->renderWidgets($template_engine, $col['thead']['widgets']);
			}
			echo "</th>\n";
		}
		echo "</tr>\n";
		echo "</thead>\n";

		// Collection - table body
		echo "<tbody>\n";
		\Duf\CollectionWalker::walkCollection($form->getRawData($group_id), $group['collection_dimensions'],
			function($collection_key) use ($form, $template_engine, $group_id, $columns) {
				$form->setCollectionKey($group_id, $collection_key);
				echo "<tr>\n";
				foreach ($columns as $fi => $field) {
					echo "<td>";
					$form->renderField($template_engine, $group_id, $fi, '@view');
					//if (isset($col['tbody']['widgets'])) {
					//	$form->renderWidgets($template_engine, $col['tbody']['widgets']);
					//}
					echo "</td>\n";
				}
				echo "</tr>\n";
			});
		$form->unsetCollectionKey($group_id);
		echo "</tbody>\n";

		// End
		echo "</table>\n";
	}

}

