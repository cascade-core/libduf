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

namespace Duf\Renderer\HtmlForm;

/**
 * HTML5 `<form>`, the root element.
 */
class Form implements \Duf\Renderer\IFormRenderer
{
	use \Duf\Renderer\TagUtils;

	static private $base_tabindex = 100;
	static private $base_tabindex_increment = 100;

	/// @copydoc \Duf\Renderer\IFormRenderer::renderForm
	public static function renderForm(\Duf\Form $form, $template_engine)
	{
		// Set tabindex by element order in page
		if ($form->base_tabindex === null) {
			$form->base_tabindex = static::$base_tabindex;
			static::$base_tabindex += static::$base_tabindex_increment;
		}

		echo "<form",
			" id=\"", htmlspecialchars($form->id), "\"",
			" action=\"", htmlspecialchars($form->action_url), "\"",
			" method=\"", htmlspecialchars($form->http_method), "\"";
		if (isset($form->html_class)) {
			static::renderClassAttr($form, $template_engine, $form->html_class);
		}
		echo ">\n";

		if (!empty($form->form_errors)) {
			echo "<ul class=\"errors\">\n";
			foreach ($form->form_errors as $error_type => $error) {
				echo "<li";
				$class = (array) @ $error['class'];
				$class[] = 'error_'.$error_type;
				echo " class=\"", htmlspecialchars(join(' ', $class)), "\"";
				echo ">", htmlspecialchars($error['message']), "</li>\n";
			}
			echo "</ul>\n";

			//debug_dump($form->field_errors, 'Field errors');
		}

		$form->renderRootWidget($template_engine);
		
		echo "<input type=\"hidden\" name=\"__[", htmlspecialchars($form->getToken()), "]\" value=\"1\">\n";
		echo "<!--[if IE]><input type=\"text\" disabled style=\"display:none!important;\" size=\"1\"><![endif]-->\n"; // IE bug: single text input
		echo "</form>\n";
	}

}
