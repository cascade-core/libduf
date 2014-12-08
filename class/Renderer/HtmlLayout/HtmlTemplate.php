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

namespace Duf\Renderer\HtmlLayout;

/**
 * Render template_format()-like template. Useful for small and complicated fragments.
 *
 * To add raw value from field use '!': {!group_id.field_id}
 *
 * Example: "Hello <em>{group_id.field_id}</em>!"
 */
class HtmlTemplate implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		// Choose holder tag
		if (!empty($widget_conf['holder_tag'])) {
			$holder_tag = $widget_conf['holder_tag'];
		} else if (isset($widget_conf['class'])) {
			$holder_tag = 'div';
		} else {
			$holder_tag = null;
		}

		// Begin
		if ($holder_tag) {
			echo "<$holder_tag";
			if (isset($widget_conf['class'])) {
				if (is_array($widget_conf['class'])) {
					echo " class=\"", htmlspecialchars(join(' ', $widget_confow['class'])), "\"";
				} else {
					echo " class=\"", htmlspecialchars($widget_conf['class']), "\"";
				}
			}
			echo ">\n";
		}

		// Template
		if (isset($widget_conf['template'])) {
			static::processTemplate($widget_conf['template'], function($key, $raw) use ($form, $template_engine, $widget_conf) {
				switch (count($key)) {
					case 1:
						// Simple key points to 'widget_map'
						if (isset($widget_conf['widget_map'][$key[0]])) {
							$form->renderWidget($template_engine, $widget_conf['widget_map'][$key[0]]);
						} else {
							throw new \RuntimeException('Unknown widget: '.$key[0]);
						}
						break;

					case 2:
						// Dual key points to field
						if ($raw) {
							echo htmlspecialchars($form->getViewData($key[0], $key[1]));
						} else {
							$form->renderField($template_engine, $key[0], $key[1], '@edit');
						}
						break;

					default:
						// No idea what to do with more parts
						throw new \RuntimeException('Too many dots: '.join('.', $key));
				}
			});
		} else {
			throw new \InvalidArgumentException('Missing template.');
		}

		// End
		if ($holder_tag) {
			echo "</$holder_tag>\n";
		}
	}


	/**
	 * Modified version of template_format(). Calls callback instead
	 * of loading data from array, and uses echo instead of string
	 * concatenation.
	 */
	private static function processTemplate($template, $value_callback)
	{
		$tokens = preg_split('/(?:({)'
					."(!?)"
					."(\\/?[a-zA-Z0-9_-]+)"			// first symbol name part
					."(?:\.(\\/?[a-zA-Z0-9_-]+))*"		// another symbol name part
					.'(})'
					.'|(\\\\[{}\\\\]))/',
				$template, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

		$status = 0;		// Current status of parser
		$append = 0;		// Append value to output (call $value_callback) after token is processed ?
		$raw = false;		// raw parameter to callback

		foreach($tokens as $token) {
			switch ($status) {
				// text around
				case 0:
					if ($token === '{') {
						$status = 10;
						$fmt = null;
						$raw = false;
					} else if ($token[0] === '\\') {
						echo substr($token, 1);
					} else {
						echo $token;
					}
					break;

				// first symbol part
				case 10:
					if ($token == '!') {
						$raw = true;
					} else {
						$key = array($token);
						$status = 20;
					}
					break;

				// another symbol part
				case 20:
					if ($token === '}') {
						// end
						$append = true;
						$status = 0;
					} else {
						$key[] = $token;
					}
					break;

				// end
				case 90:
					if ($token === '}') {
						$append = true;
						$status = 0;
					} else {
						return FALSE;
					}
					break;
			}

			if ($append) {
				$value_callback($key, $raw);
				$append = false;
			}
		}
	}

}

