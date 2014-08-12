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
		$type = $field_conf['type'];

		if (isset($field_conf['link'])) {
			$tag = 'a';
		} else {
			$tag = 'span';
		}

		// Get value early, so class can be set
		$raw_value = $form->getViewData($group_id, $field_id);

		// add value-specific classes
		switch ($type) {
			case 'checkbox':
				$field_conf['class'][] = $raw_value ? 'true' : 'false';
				break;
		}

		echo "<$tag";

		if (isset($field_conf['link'])) {
			echo " href=\"", htmlspecialchars(filename_format($field_conf['link'], $form->getViewData($group_id))), "\"";
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
				if (isset($field_conf['values'])) {
					echo htmlspecialchars($field_conf['values'][$raw_value]);
				} else {
					echo $raw_value ? _('yes') : _('no');
				}
				break;

			case 'radio':
				// Radio cannot use this method, it needs different handling.
				throw new \Exception('Not supported.');

			case 'email':
				echo str_replace(array('@', '.'), array('<span>&#64;</span>', '<span>&#46;</span>'),
					htmlspecialchars($raw_value));
				break;

			case 'datetime':
			case 'datetime-local':
				if ($raw_value !== null) {
					echo strftime(
						isset($field_conf['format']) ? $field_conf['format'] : _('%d.&nbsp;%m.&nbsp;%Y,&nbsp;%H:%M'),
						strtotime($raw_value));
				}
				break;

			case 'time':
				if ($raw_value !== null) {
					echo strftime(
						isset($field_conf['format']) ? $field_conf['format'] : _('%H:%M'),
						strtotime($raw_value));
				}
				break;

			case 'date':
				if ($raw_value !== null) {
					echo strftime(
						isset($field_conf['format']) ? $field_conf['format'] : _('%d.&nbsp;%m.&nbsp;%Y'),
						strtotime($raw_value));
				}
				break;

			case 'week':
				if ($raw_value !== null) {
					echo strftime(
						isset($field_conf['format']) ? $field_conf['format'] : _('%V/%Y'),
						strtotime($raw_value));
				}
				break;

			case 'month':
				if ($raw_value !== null) {
					echo strftime(
						isset($field_conf['format']) ? $field_conf['format'] : _('%B&nbsp;%Y'),
						strtotime($raw_value));
				}
				break;


			default:
				echo htmlspecialchars($raw_value);
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
				// HTML5 boolean attributes
				case 'hidden':
				case 'itemscope':
					if ($v) {
						echo " $k";
					}
					break;

				// HTML5 string attributes
				case 'accesskey':
				case 'contenteditable':
				case 'contextmenu':
				case 'dir':
				case 'form':
				case 'itemid':
				case 'lang':
				case 'style':
				case 'tabindex':
				case 'title':
					echo " $k=\"", htmlspecialchars($v), "\"";
					break;

				// HTML5 tri-state boolean attributes
				case 'autocomplete':
					echo " $k=\"", $v ? 'on' : 'off', "\"";
					break;

				// HTML5 true/false/string attributes
				case 'draggable':
				case 'spellcheck':
					if ($v === true) {
						echo " $k=\"true\"";
					} else if ($v === false) {
						echo " $k=\"false\"";
					} else {
						echo " $k=\"", htmlspecialchars($v), "\"";
					}
					break;

				// HTML5 yes/no/string attributes
				case 'translate':
					if ($v === true) {
						echo " $k=\"yes\"";
					} else if ($v === false) {
						echo " $k=\"no\"";
					} else {
						echo " $k=\"", htmlspecialchars($v), "\"";
					}
					break;

				// HTML5 unordered set of unique space-separated tokens
				case 'class':
				case 'dropzone':
				case 'itemprop':
				case 'itemref':
				case 'itemtype':
					if (is_array($v)) {
						echo " $k=\"", htmlspecialchars(join(' ', $v)), "\"";
					} else {
						echo " $k=\"", htmlspecialchars($v), "\"";
					}
					break;

				// 'data-' prefixed attributes
				case 'data':
					foreach ($v as $dk => $dv) {
						if ($dv === null) {
							continue;
						}
						$dk = preg_replace('/["\'>\\/=\\0 \s]/', '_', $dk);
						if ($dv === true) {
							echo " data-$dk=\"true\"";
						} else if ($dv === false) {
							echo " data-$dk=\"false\"";
						} else if (is_array($dv) || is_object($dv)) {
							echo " data-$dk='", json_encode($dv, JSON_HEX_APOS | JSON_HEX_AMP 
								| JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "'";
						} else {
							echo " data-$dk=\"", htmlspecialchars($dv), "\"";
						}
					}
					break;
			}
		}
	}

}

