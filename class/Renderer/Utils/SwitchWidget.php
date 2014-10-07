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

namespace Duf\Renderer\Utils;

/**
 * Switch - determine which child widgets to use by given value, optionaly
 * processed by map function. This switch can be used for displaying values in
 * a pretty way or to show/hide part of the form.
 *
 * Field value is used to select widgets from `widgets_map`. These widgets are
 * rendered as usual. Invalid values (not present in `widgets_map`) are silently
 * ignored and `default_widgets` are used instead.
 *
 * @see Duf\Renderer\TagUtils::calculateValue()
 */
class SwitchWidget implements \Duf\Renderer\IWidgetRenderer
{
	use \Duf\Renderer\TagUtils;

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		$value = static::calculateValue($form, $template_engine, $widget_conf);

		// Render children
		if (isset($widget_conf['widgets_map'][$value])) {
			// Render selected widgets
			$form->renderWidgets($template_engine, $widget_conf['widgets_map'][$value]);
		} else if (isset($widget_conf['default_widgets'])) {
			// Fallback to default_widgets
			$form->renderWidgets($template_engine, $widget_conf['default_widgets']);
		}
	}

}

