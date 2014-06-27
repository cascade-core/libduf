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
 * The root element of a view.
 */
class Form implements \Duf\Renderer\IFormRenderer
{

	/// @copydoc \Duf\Renderer\IFormRenderer::renderForm
	public static function renderForm(\Duf\Form $form, $template_engine)
	{
		echo "<div",
			" id=\"", htmlspecialchars($form->id), "\"",
			" class=\"duf_view\"",
			">\n";

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
		}

		$form->renderRootWidget($template_engine);
		
		echo "</div>\n";
	}

}
