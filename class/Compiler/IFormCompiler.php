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

namespace Duf\Compiler;

/**
 * Compile a form.
 *
 * To compile content of the form, call this:
 *
 *     $form->compileRootWidget($buf);
 *
 * @see IWidgetCompiler
 */
interface IFormCompiler
{
	/**
	 * Compile the form. This should call `$form->renderRootWidget($template_engine)`.
	 *
	 * @param $form is the Form.
	 */
	public static function compileForm(\Duf\FormCompiler $form, \Duf\PhpBuffer $buf);

}

