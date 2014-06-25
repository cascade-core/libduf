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
 * Render a form. Everything in a form is a widget, but form itself is not, 
 * because forms cannot be nested.
 *
 * $template_engine is external object usable for rendering which is passed 
 * by between widgets. Duf does not use it in any way but custom widgets can.
 *
 * To render content of the form, call this:
 *
 *     $form->renderRootWidget($template_engine);
 *
 * @see IWidgetRenderer
 */
interface IFormRenderer
{
	/**
	 * Render the form. This should call `$form->renderRootWidget($template_engine)`.
	 *
	 * @param $form is the Form.
	 * @param $template_engine is templating engine which may be used for rendering (optional).
	 */
	public static function renderForm(\Duf\Form $form, $template_engine);

}

