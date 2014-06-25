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

namespace Duf\Renderer;

/**
 * Render a widget. Everything in a form is a widget. Widgets can render 
 * recursively nested widgets. Widgets can be form field, its label, error 
 * messages, static text, or layout.
 *
 * $template_engine is external object usable for rendering which is passed 
 * by between widgets. Duf does not use it in any way but custom widgets can.
 *
 * To render nested widget, call this:
 *
 *     $form->renderWidget($template_engine, $child_widget_conf);
 *
 * Where `$child_widget_conf` is subtree of `$widget_conf`.
 *
 * @see IFormRenderer
 */
interface IWidgetRenderer
{

	/**
	 * Render the widget.
	 *
	 * @param $form is the Form.
	 * @param $template_engine is templating engine which may be used for rendering (optional).
	 * @param $widget_conf is configuration of the widget. It must contain 
	 * 	`'#!'` key to determine next renderer.
	 */
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf);

}

