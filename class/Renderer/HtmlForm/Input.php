<?php
/*
 * Copyright (c) 2014-2015, Josef Kufner  <jk@frozen-doe.net>
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

namespace Duf\Renderer\HtmlForm;

/**
 * Default `<input>` field renderer.
 *
 * TODO: Suffix & prefix for a field -- simply wrap input in
 * `<label>` with specific class to make it part of the field and
 * prepend/append some string in span. For example units,
 * currency, ...
 */
class Input implements \Duf\Renderer\IFieldWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IFieldWidgetRenderer::renderFieldWidget
	public static function renderFieldWidget(\Duf\Form $form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf)
	{
		$type = $field_conf['type'];

		if ($type == 'checkbox' && empty($field_conf['readonly']) && empty($field_conf['disabled'])) {
			// FIXME: Little hack to get false when unchecked checkbox is submitted.
			echo "<input type=\"hidden\" name=\"", $form->getHtmlFieldName($group_id, $field_id), "\" value=\"0\">";
		}

		// relative URL may be anything
		// FIXME: Any restrictions ?
		if ($type == 'relative_url') {
			$type = 'url';
		}

		// Change datetime to datetime-local because browser support
		if ($type == 'datetime') {
			$type = 'datetime-local';
		}

		echo "<input",
			" type=\"", htmlspecialchars($type), "\"",
			" id=\"", $form->getHtmlFieldId($group_id, $field_id), "\"",
			" tabindex=\"", $form->base_tabindex + (isset($field_conf['tabindex']) ? $field_conf['tabindex'] : 0), "\"";

		if (empty($field_conf['unnamed'])) {
			echo " name=\"", $form->getHtmlFieldName($group_id, $field_id), "\"";
		}

		// Value
		$raw_data = $form->getRawData($group_id, $field_id);
		switch ($type) {
			case 'submit':
				echo " value=\"", htmlspecialchars(isset($field_conf['label']) ? $field_conf['label'] : $field_conf['name']), "\"";
				break;

			case 'checkbox':
				echo " value=\"1\"",
					$form->getRawData($group_id, $field_id) ? 'checked ' : '';
				break;

			case 'radio':
				// Radio cannot use this method, it needs different handling.
				throw new \Exception('Not supported.');

			case 'datetime':
			case 'datetime-local':
				if ($raw_data) {
					echo " value=\"", htmlspecialchars(strftime(empty($field_conf['precision']) ? '%Y-%m-%dT%H:%M:00' : '%Y-%m-%dT%H:%M:%S',
						strtotime($raw_data))), "\"";
				}
				break;
			case 'date':
				if ($raw_data) {
					echo " value=\"", htmlspecialchars(strftime('%Y-%m-%d', strtotime($raw_data))), "\"";
				}
				break;
			case 'time':
				if ($raw_data) {
					echo " value=\"", htmlspecialchars(strftime('%H:%M:%S', strtotime($raw_data))), "\"";
				}
				break;
			default:
				echo " value=\"", htmlspecialchars($raw_data), "\"";
				break;
		}

		static::commonAttributes($field_conf);

		// Specific HTML attributes
		foreach ($field_conf as $k => $v) {
			if ($v === null) {
				// null means 'not specified'
				continue;
			}

			// Handle floats
			if (is_float($v)) {
				$v = sprintf('%F', $v);
			}

			switch ($k) {
				// HTML5 boolean attributes
				case 'formnovalidate':
				case 'multiple':
					if ($v) {
						echo " $k";
					}
					break;

				// HTML5 string attributes
				case 'dirname':
				case 'formaction':
				case 'formenctype':
				case 'formmethod':
				case 'formtarget':
				case 'height':
				case 'width':
				case 'inputmode':
				case 'list':
				case 'maxlength':
				case 'minlength':
				case 'pattern':
				case 'placeholder':
				case 'size':
				case 'src':
				case 'step':
				case 'alt':
					echo " $k=\"", htmlspecialchars($v), "\"";
					break;

				case 'max':
				case 'min':
					switch ($type) {
						case 'datetime':
						case 'datetime-local':
							echo " $k=\"", htmlspecialchars(strftime('%Y-%m-%dT%H:%M:%S', strtotime($v))), "\"";
							break;
						case 'date':
							echo " $k=\"", htmlspecialchars(strftime('%Y-%m-%d', strtotime($v))), "\"";
							break;
						case 'time':
							echo " $k=\"", htmlspecialchars(strftime('%H:%M:%S', strtotime($v))), "\"";
							break;
						default:
							echo " $k=\"", htmlspecialchars($v), "\"";
							break;
					}
					break;

				// HTML5 set of comma-separated tokens
				case 'accept':
					if (is_array($v)) {
						echo " $k=\"", htmlspecialchars(join(',', $v)), "\"";
					} else {
						echo " $k=\"", htmlspecialchars($v), "\"";
					}
					break;

				// Confirm button click
				case 'confirm_query':
					if ($type == 'submit') {
						echo "onclick=\"return confirm('", htmlspecialchars(addcslashes($v, "'")), "');\"";
					}
					break;
			}
		}

		echo ">";

		if (isset($field_conf['field_note'])) {
			echo "<span class=\"field_note\">", htmlspecialchars($field_conf['field_note']), "</span>";
		}

		echo "\n";
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

			// Handle floats
			if (is_float($v)) {
				$v = sprintf('%F', $v);
			}

			switch ($k) {
				// HTML5 boolean attributes
				case 'hidden':
				case 'itemscope':
				case 'autofocus':	// only for: input, textarea, select
				case 'disabled':	// only for: input, textarea, select
				case 'readonly':	// only for: input, textarea
				case 'required':	// only for: input, textarea, select
					if ($v) {
						echo " $k";
					}
					break;

				// HTML5 string attributes
				case 'accesskey':
				case 'contextmenu':
				case 'dir':
				case 'form':
				case 'itemid':
				case 'lang':
				case 'maxlength':
				case 'style':
				//case 'tabindex':
				case 'title':
				case 'contenteditable':
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

