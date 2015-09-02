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

namespace Duf\Renderer\HtmlView;

/**
 * Render `<input>` field value using `<span>`.
 */
class Input implements \Duf\Renderer\IFieldWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IFieldWidgetRenderer::renderFieldWidget
	public static function renderFieldWidget(\Duf\Form $form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf)
	{
		// FIXME: This should not be here
		$type = isset($widget_conf['type']) ? $widget_conf['type'] : $field_conf['type'];

		// Get value early, so class can be set
		$raw_value = $form->getViewData($group_id, $field_id);
		$view_data = $form->getViewData($group_id);

		// Detect special null values
		switch ($type) {
			case 'gps_coords':
				if (empty($raw_value['lat']) || empty($raw_value['lon'])) {
					$raw_value = null;
				}
				break;
		}

		// Handle null value and null format
		if ($raw_value !== null) {
			$link = isset($widget_conf['link']) ? $widget_conf['link'] : (isset($field_conf['link']) ? $field_conf['link'] : null);
			$format = isset($widget_conf['format']) ? $widget_conf['format'] : (isset($field_conf['format']) ? $field_conf['format'] : null);
			$target = isset($widget_conf['target']) ? $widget_conf['target'] : (isset($field_conf['target']) ? $field_conf['target'] : null);
			$value = $raw_value;
			$tooltip = isset($widget_conf['tooltip_format']) ? $widget_conf['tooltip_format']
				: (isset($field_conf['tooltip_format']) ? $field_conf['tooltip_format'] : null);
		} else {
			$link = isset($widget_conf['null_link']) ? $widget_conf['null_link'] : (isset($field_conf['null_link']) ? $field_conf['null_link'] : null);
			$format = isset($widget_conf['null_format']) ? $widget_conf['null_format']
				: (isset($field_conf['null_format']) ? $field_conf['null_format']
				: (isset($widget_conf['format']) ? $widget_conf['format']
				: (isset($field_conf['format']) ? $field_conf['format']
				: null)));
			$target = isset($widget_conf['null_target']) ? $widget_conf['null_target'] : (isset($field_conf['null_target']) ? $field_conf['null_target'] : null);
			$value = isset($widget_conf['null_value']) ? $widget_conf['null_value'] : (isset($field_conf['null_value']) ? $field_conf['null_value'] : null);
			$tooltip = isset($widget_conf['null_tooltip_format']) ? $widget_conf['null_tooltip_format']
				: (isset($field_conf['null_tooltip_format']) ? $field_conf['null_tooltip_format'] : null);
		}

		// Link default value (can be overriden with false)
		if ($link === null) {
			switch ($type) {
				case 'url':
				case 'relative_url':
					$link = $raw_value;
					break;
				case 'gps_coords':
					$link = (isset($raw_value['lat']) && isset($raw_value['lon'])
						? sprintf('geo:%s,%s', $raw_value['lat'], $raw_value['lon'])
						: null);
					break;
				default:
					$link = null;
					break;
			}
		}
		$tag = ($link ? 'a' : 'span');

		// add value-specific classes
		switch ($type) {
			case 'checkbox':
				if ($raw_value !== null) {
					$field_conf['class'][] = $raw_value ? 'true' : 'false';
				} else {
					$field_conf['class'][] = 'null_value';
				}
				break;
		}

		echo "<$tag";

		if ($raw_value === null && isset($field_conf['null_link'])) {
			echo " href=\"", htmlspecialchars(filename_format($field_conf['null_link'], $view_data)), "\"";
		} else if ($link !== null) {
			echo " href=\"", htmlspecialchars(filename_format($link, $view_data)), "\"";
		}

		if ($target !== null) {
			echo " target=\"", htmlspecialchars(filename_format($target, $view_data)), "\"";
		}

		if ($tooltip !== null) {
			echo " title=\"", htmlspecialchars(filename_format($tooltip, $view_data)), "\"";
		}

		static::commonAttributes($field_conf);

		echo ">";

		// Value
		switch ($type) {
			case 'submit':
				// Read-only button ?  WTF ?
				throw new \Exception('Not supported.');
				break;

			case 'checkbox':
				if ($raw_value !== null) {
					if (isset($field_conf['values'])) {
						echo htmlspecialchars($field_conf['values'][$raw_value]);
					} else {
						echo $raw_value ? _('yes') : _('no');
					}
				} else if (isset($field_conf['null_value'])) {
					echo htmlspecialchars($field_conf['null_value']);
				}
				break;

			case 'radio':
				// Radio cannot use this method, it needs different handling.
				throw new \Exception('Not supported.');

			case 'email':
				if ($raw_value !== null) {
					echo str_replace(array('@', '.'), array('<span>&#64;</span>', '<span>&#46;</span>'),
						htmlspecialchars($value));
				} else if (isset($field_conf['null_value'])) {
					echo htmlspecialchars($field_conf['null_value']);
				}
				break;

			case 'datetime':
			case 'datetime-local':
				if ($value !== null) {
					echo htmlspecialchars(strftime($format !== null ? $format
						: (empty($field_conf['precision']) ? _("%d.\302\240%m.\302\240%Y,\302\240%H:%M") : _("%d.\302\240%m.\302\240%Y,\302\240%H:%M:%S")),
							strtotime($value)));
				}
				break;

			case 'time':
				if ($value !== null) {
					echo htmlspecialchars(strftime($format !== null ? $format : _("%H:%M"), strtotime($value)));
				}
				break;

			case 'date':
				if ($value !== null) {
					echo htmlspecialchars(strftime($format !== null ? $format : _("%d.\302\240%m.\302\240%Y"), strtotime($value)));
				}
				break;

			case 'week':
				if ($value !== null) {
					echo htmlspecialchars(strftime($format !== null ? $format : _("%V/%Y"), strtotime($value)));
				}
				break;

			case 'month':
				if ($value !== null) {
					echo htmlspecialchars(strftime($format !== null ? $format : _("%B\302\240%Y"), strtotime($value)));
				}
				break;

			case 'select':
				if (isset($field_conf['options'][$value])) {
					$option = $field_conf['options'][$value];
					if (is_array($option)) {
						$opt_val = $option['label'];
					} else {
						$opt_val = $option;
					}
				} else {
					$opt_val = $value;
				}
				if ($format !== null) {
					echo htmlspecialchars(sprintf($format, $opt_val));
				} else {
					echo htmlspecialchars($opt_val);
				}
				break;

			case 'gps_coords':
				if (isset($value['lat']) && isset($value['lon'])) {
					if ($format !== null) {
						echo htmlspecialchars(sprintf($format, $value['lat'], $value['lon']));
					} else {
						echo htmlspecialchars(sprintf("%s,\302\240%s", $value['lat'], $value['lon']));
					}
				} else if (is_string($value)) {
					echo htmlspecialchars($value);
				}
				break;

			default:
				if ($format !== null) {
					echo htmlspecialchars(sprintf($format, $value));
				} else {
					echo htmlspecialchars($value);
				}
				break;
		}

		echo "</$tag>\n";
	}


	/**
	 * Render common attributes, prefixed with space.
	 */
	static protected function commonAttributes($field_conf)
	{
		foreach ($field_conf as $k => $v) {
			if ($v === null) {
				// null means 'not specified'
				continue;
			}
			switch ($k) {
				// HTML5 string attributes
				case 'dir':
				case 'lang':
				case 'style':
				case 'title':
					echo " $k=\"", htmlspecialchars($v), "\"";
					break;


				// HTML5 unordered set of unique space-separated tokens
				case 'class':
					if (is_array($v)) {
						echo " $k=\"", htmlspecialchars(join(' ', $v)), "\"";
					} else {
						echo " $k=\"", htmlspecialchars($v), "\"";
					}
					break;
			}
		}
	}

}

