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
 * Render collection into HTML `&lt;table>` with additional control elements 
 * around. Multi-dimensional collections are flattened into simple list of 
 * table rows.
 *
 * @see Tabular.
 */
class Tabular implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		$group_id = $widget_conf['group_id'];
		$group = $form->getFieldGroup($group_id);
		$columns = array();

		// Load fields
		if (empty($group['fields'])) {
			return;
		}
		$fields = $group['fields'];

		// Calculate prefixed tabular keys
		$key_prefix = isset($widget_conf['option_prefix']) ? $widget_conf['option_prefix'] : 'tabular';
		$k_hidden   = $key_prefix.'_hidden';
		$k_weight   = $key_prefix.'_weight';
		$k_width    = $key_prefix.'_width';
		$k_label    = $key_prefix.'_label';
		$k_indent   = $key_prefix.'_indent_key';

		// Get column list from group fields
		if (!empty($widget_conf['columns_from_fields'])) {
			foreach ($fields as $field_id => $f) {
				if (!empty($f['hidden']) || !empty($f[$k_hidden])) {
					// columns are enabled by default
					continue;
				}
				$columns[$field_id] = array(
					'weight'     => isset($f[$k_weight])   ? $f[$k_weight]   : (isset($f['weight'])     ? $f['weight']     : 50),
					'width'      => isset($f[$k_width])    ? $f[$k_width]    : (isset($f['width'])      ? $f['width']      : null),
					'label'      => isset($f[$k_label])    ? $f[$k_label]    : (isset($f['label'])      ? $f['label']      :
					                                                           (isset($f['name'])       ? $f['name']       : $field_id)),
					'indent_key' => isset($f[$k_indent])   ? $f[$k_indent]   : (isset($f['indent_key']) ? $f['indent_key'] : null),
				);
			}
		}

		// Merge it with widget configuration
		if (isset($widget_conf['columns'])) {
			$columns = array_replace_recursive($columns, (array) $widget_conf['columns']);
		}

		// Make sure weights are set and add fractions to stabilize unstable uasort().
		$fraction_step = 1 / (2 + count($columns));
		$fraction = 0;
		foreach ($columns as & $c) {
			if (isset($c['weight'])) {
				$c['weight'] += $fraction;
			} else {
				$c['weight'] = 50 + $fraction;
			}
			$fraction += $fraction_step;
		}

		// Sort columns by weight (light on top/left).
		// This MUST be a stable sort!
		uasort($columns, function ($a, $b) {
			return $a['weight'] > $b['weight'] ? 1 : -1;	// never equal because of fractions
		});

		// Begin
		echo "<table";
		$class = isset($widget_conf['class']) ? (array) $widget_conf['class'] : array();
		$class[] = 'table';
		echo " class=\"", htmlspecialchars(join(' ', $class)), "\"";
		echo ">\n";

		if (!empty($widget_conf['caption'])) {
			echo "<caption>", htmlspecialchars($widget_conf['caption']), "</caption>\n";
		}

		// Header
		if (empty($widget_conf['thead']['hidden'])) {
			echo "<thead>\n";
			echo "<tr>\n";
			foreach ($columns as $field_id => $col) {
				echo "<th";
				if (isset($col['width'])) {
					echo " width=\"", htmlspecialchars($col['width']), "\"";
				}
				echo ">";
				if (isset($col['label'])) {
					echo htmlspecialchars($col['label']);
				}
				if (isset($col['thead_widgets'])) {
					$form->renderWidgets($template_engine, $col['thead_widgets']);
				}
				echo "</th>\n";
			}
			echo "</tr>\n";
			echo "</thead>\n";
		}

		// Header
		if (empty($widget_conf['tfoot']['hidden']) && !empty($widget_conf['tfoot']['widgets'])) {
			echo "<tfoot>\n";
			echo "<tr>\n";
			echo "<th colspan=\"", count($columns), "\">\n";
			$form->renderWidgets($template_engine, $widget_conf['tfoot']['widgets']);
			echo "</th>\n";
			echo "</tr>\n";
			echo "</tfoot>\n";
		}

		// Collection - table body
		echo "<tbody>\n";
		$collection = $form->getRawData($group_id);
		$is_row_even = true;
		if (empty($collection)) {
			echo "<tr>\n";
			echo "<td colspan=\"", count($columns), "\" class=\"empty_collection\">\n";
			if (isset($widget_conf['empty_tbody_widgets'])) {
				$form->renderWidgets($template_engine, $widget_conf['empty_tbody_widgets']);
			} else {
				echo "<em>",
					isset($widget_conf['empty_tbody_message'])
						? $widget_conf['empty_tbody_message']
						: _('(No items.)'),
					"</em>";
			}
			echo "</td>\n";
			echo "</tr>\n";
		} else {
			\Duf\CollectionWalker::walkCollection($collection, $group['collection_dimensions'],
				function($collection_key, $item) use ($form, $template_engine, $group_id, $columns, & $is_row_even) {
					$form->setCollectionKey($group_id, $collection_key);
					echo "<tr class=\"", $is_row_even ? 'even':'odd', "\">\n";
					foreach ($columns as $field_id => $col) {
						echo "<td";
						if (isset($col['indent_key']) && !empty($item[$col['indent_key']])) {
							echo " style=\"padding-left: ", 2 * $item[$col['indent_key']], "em;\"";
						}
						echo ">";
						if (isset($col['tbody_widgets']) || $form->getRawData($group_id, $field_id) !== null) {
							if (isset($col['tbody_widgets'])) {
								$form->renderWidgets($template_engine, $col['tbody_widgets']);
							} else {
								$form->renderField($template_engine, $group_id, $field_id, '@view');
							}
						}
						echo "</td>\n";
					}
					echo "</tr>\n";
					$is_row_even = ! $is_row_even;
				});
			$form->unsetCollectionKey($group_id);
		}
		echo "</tbody>\n";

		// End
		echo "</table>\n";
	}

}

