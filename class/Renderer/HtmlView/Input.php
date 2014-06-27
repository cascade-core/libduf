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
 * Default <input> field renderer.
 */
class Input implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		$type = $widget_conf['type'];
		$group_id = $widget_conf['group_id'];
		$field_id = $widget_conf['field_id'];

		echo "<span",
			" id=\"", $form->getHtmlFieldId($group_id, $field_id), "\"";

		static::commonAttributes($widget_conf);

		echo ">";

		// Value
		switch ($type) {
			case 'submit':
				// Read-only button ?  WTF ?
				throw new \Exception('Not supported.');
				break;

			case 'checkbox':
				if (isset($widget_conf['values'])) {
					echo htmlspecialchars($widget_conf['values'][$form->getRawData($group_id, $field_id, true)]);
				} else {
					echo $form->getRawData($group_id, $field_id, true) ? _('yes') : _('no');
				}
				break;

			case 'radio':
				// Radio cannot use this method, it needs different handling.
				throw new \Exception('Not supported.');

			default:
				echo htmlspecialchars($form->getRawData($group_id, $field_id, true));
				break;
		}

		echo "</span>\n";
	}


	/**
	 * Render common attributes, prefixed with space.
	 */
	static protected function commonAttributes($widget_conf)
	{
		foreach ($widget_conf as $k => $v) {
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

