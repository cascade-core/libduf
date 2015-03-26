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
 * Just render widgets. Optionaly with container around them.
 */
class NullLayout implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		if (isset($widget_conf['class'])) {
			// FIXME: Some smarter rendering here?
			echo "<div class=\"", htmlspecialchars($widget_conf['class']), "\">\n";
		}

		if (isset($widget_conf['widgets'])) {
			$form->renderWidgets($template_engine, $widget_conf['widgets']);
		}

		if (isset($widget_conf['class'])) {
			echo "</div>\n";
		}
	}

}

