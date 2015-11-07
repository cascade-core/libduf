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
		$filters_group_id = isset($widget_conf['filters_group_id']) ? $widget_conf['filters_group_id'] : null; 
		$group = $form->getFieldGroup($group_id);
		$columns = array();

		// Load fields
		if (empty($group['fields'])) {
			return;
		}
		$fields = $group['fields'];

		// Calculate prefixed tabular keys
		$key_prefix    = isset($widget_conf['option_prefix']) ? $widget_conf['option_prefix'] : 'tabular';
		$k_hidden      = $key_prefix.'_hidden';
		$k_weight      = $key_prefix.'_weight';
		$k_width       = $key_prefix.'_width';
		$k_label       = $key_prefix.'_label';
		$k_indent      = $key_prefix.'_indent_key';
		$k_order_by    = $key_prefix.'_order_by';
		$k_order_asc   = $key_prefix.'_order_asc';
		$k_filter_name = $key_prefix.'_filter_name';
		$k_rotated     = $key_prefix.'_rotated_header';
		$k_irf_name    = $key_prefix.'_indent_rq_filter_name';
		$k_irf_value   = $key_prefix.'_indent_rq_filter_value';

		// Get column list from group fields
		if (!empty($widget_conf['columns_from_fields'])) {
			foreach ($fields as $field_id => $f) {
				if (isset($f[$k_hidden])) {		// Allow false to override 'hidden' option
					// columns are enabled by default
					if (!empty($f[$k_hidden])) {
						continue;
					}
				} else if (!empty($f['hidden'])) {
					continue;
				}
				$columns[$field_id] = array(
					'weight'      => isset($f[$k_weight])      ? $f[$k_weight]      : (isset($f['weight'])          ? $f['weight']          : 50),
					'width'       => isset($f[$k_width])       ? $f[$k_width]       : (isset($f['width'])           ? $f['width']           : null),
					'label'       => isset($f[$k_label])       ? $f[$k_label]       : (isset($f['label'])           ? $f['label']           :
					                                                                  (isset($f['name'])            ? $f['name']            : $field_id)),
					'order_by'    => isset($f[$k_order_by])    ? $f[$k_order_by]    : (isset($f['order_by'])        ? $f['order_by']        : null),
					'order_asc'   => isset($f[$k_order_asc])   ? $f[$k_order_asc]   : (isset($f['order_asc'])       ? $f['order_asc']       : true),
					'filter_name' => isset($f[$k_filter_name]) ? $f[$k_filter_name] : (isset($f['filter_name'])     ? $f['filter_name']     : $field_id),
					'indent_key'  => isset($f[$k_indent])      ? $f[$k_indent]      : (isset($f['indent_key'])      ? $f['indent_key']      : null),
					'rotated'     => isset($f[$k_rotated])     ? $f[$k_rotated]     : (isset($f['rotated_header'])  ? $f['rotated_header']  : null),
				);

				$irf_name  = isset($f[$k_irf_name])  ? $f[$k_irf_name]  : (isset($f['indent_rq_filter_name'])  ? $f['indent_rq_filter_name']   : null);
				$irf_value = isset($f[$k_irf_value]) ? $f[$k_irf_value] : (isset($f['indent_rq_filter_value']) ? $f['indent_rq_filter_value']  : null);

				// If required filter is not set, do not indent the tree. Typically
				// this filter is "order_by = tree_left", which says "tree in a list".
				if ($filters_group_id !== null && $irf_name !== null && $irf_value !== null) {
					if ($form->getViewData($filters_group_id, $irf_name) != $irf_value) {
						$columns[$field_id]['indent_key'] = null;
					}
				}
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

		// Filter form
		$filters_enabled = $form->readonly && $filters_group_id;
		if ($filters_enabled) {
			$unrendered_filters = $form->getViewData($filters_group_id);
			echo "<form action=\"\" method=\"get\">\n";
		}

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

			// Heading
			echo "<tr>\n";
			foreach ($columns as $field_id => $col) {
				if (!empty($col['tabular_hidden']) || !empty($col['hidden'])) {
					continue;
				}
				echo "<th";
				if (isset($col['width'])) {
					echo " width=\"", htmlspecialchars($col['width']), "\"";
				}
				if (!empty($col['rotated'])) {
					echo " class=\"rotated\"";
				}
				echo ">";
				if (!empty($col['rotated'])) {
					echo "<span class=\"rotated\">";
				}

				if (!empty($col['order_by']) && $filters_group_id !== null) {
					\Duf\Renderer\HtmlFilter\OrderButton::renderWidget($form, $template_engine, array(
						'group_id' => $filters_group_id,
						'order_by' => $col['order_by'],
						'order_asc' => isset($col['order_asc']) ? $col['order_asc'] : true,
						'label' => isset($col['label']) ? $col['label'] : null,
						'widgets' => isset($col['thead_widgets']) ? $col['thead_widgets'] : null,
					));
				} else {
					if (isset($col['label'])) {
						echo htmlspecialchars($col['label']);
					}

					if (isset($col['thead_widgets'])) {
						$form->renderWidgets($template_engine, $col['thead_widgets']);
					}
				}

				if (!empty($col['rotated'])) {
					echo "</span>";
				}
				echo "</th>\n";
			}
			echo "</tr>\n";

			// Filters
			if ($filters_enabled) {
				echo "<tr>\n";
				foreach ($columns as $field_id => $col) {
					if (!empty($col['tabular_hidden']) || !empty($col['hidden'])) {
						continue;
					}
					echo "<th class=\"filter\">";
					if (!empty($col['filter_name'])) {
						unset($unrendered_filters[$col['filter_name']]);
						// TODO: Use field filtering renderer
						echo "<input name=\"", htmlspecialchars($field_id), "\"",
							" value=\"", htmlspecialchars($form->getViewData($filters_group_id, $col['filter_name'])), "\"",
							" style=\"display: block; width: 100%;\">\n";
					} else if (!$field_id) {
						echo "<input type=\"submit\" value=\"", _('Filter'), "\">\n";
					}
					echo "</th>\n";
				}
				echo "</tr>\n";
			}

			echo "</thead>\n";
		}

		// Footer
		if (isset($widget_conf['tfoot']) && empty($widget_conf['tfoot']['hidden'])) {
			echo "<tfoot>\n";
			echo "<tr>\n";
			if (isset($widget_conf['tfoot']['columns'])) {
				self::renderColumns($form, $template_engine, $widget_conf['tfoot']['columns']);
			} else {
				echo "<th colspan=\"", count($columns), "\">\n";
				$form->renderWidgets($template_engine, $widget_conf['tfoot']['widgets']);
				echo "</th>\n";
			}
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
			// Prepare group headings configuration
			$last_group_heading_value = null;
			if (isset($widget_conf['group_heading']['field_id'])) {
				$group_heading_field_id = $widget_conf['group_heading']['field_id'];
				$group_heading_columns = $widget_conf['group_heading']['columns'];
			} else {
				$group_heading_field_id = null;
				$group_heading_columns = null;
			}

			// For each row ...
			$prefix = isset($widget_conf['collection_key_prefix']) ? $form->setCollectionKeyPrefix($group_id, $widget_conf['collection_key_prefix']) : null;
			\Duf\CollectionWalker::walkCollection($collection, $group['collection_dimensions'], $prefix,
				function($collection_key, $item) use ($form, $template_engine, $group_id, $columns, & $is_row_even,
					& $last_group_heading_value, $group_heading_field_id, $group_heading_columns)
				{
					$form->setCollectionKey($group_id, $collection_key);

					// Group heading
					if ($group_heading_field_id !== null) {
						$group_heading_value = $form->getViewData($group_id, $group_heading_field_id);
						if ($group_heading_value !== $last_group_heading_value) {
							$last_group_heading_value = $group_heading_value;
							echo "<tr class=\"group_heading\">\n";
							self::renderColumns($form, $template_engine, $group_heading_columns);
							echo "</tr>\n";
						}
					}

					// Row cells
					echo "<tr class=\"", $is_row_even ? 'even':'odd', "\">\n";
					foreach ($columns as $field_id => $col) {
						if (!empty($col['tabular_hidden']) || !empty($col['hidden'])) {
							continue;
						}
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

		if ($filters_enabled) {
			foreach ($unrendered_filters as $filter_name => $filter_value) {
				echo "<input type=\"hidden\" name=\"", htmlspecialchars($filter_name), "\" value=\"", htmlspecialchars($filter_value), "\">\n";
			}
			echo "</form>\n";
		}
	}

	/**
	 * Render `<td>` elements using given columns configuration
	 */
	private static function renderColumns(\Duf\Form $form, $template_engine, $columns, $tag = 'td')
	{
		foreach ($columns as $col) {
			if (!empty($col['tabular_hidden']) || !empty($col['hidden'])) {
				continue;
			}
			echo "<$tag";
			if (!empty($col['colspan'])) {
				echo " colspan=\"", htmlspecialchars($col['colspan']), "\"";
			}
			if (!empty($col['class'])) {
				echo " class=\"", htmlspecialchars(join(' ', (array) $col['class'])), "\"";
			}
			echo ">\n";
			if (isset($col['label'])) {
				echo htmlspecialchars($col['label']);
			}
			if (isset($col['widgets'])) {
				$form->renderWidgets($template_engine, $col['widgets']);
			}
			echo "</$tag>\n";
		}
	}

}

