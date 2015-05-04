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

namespace Duf\Compiler\HtmlForm;

/**
 * HTML5 `<form>`, the root element.
 */
class Form implements \Duf\Compiler\IFormCompiler
{
	static private $base_tabindex = 100;
	static private $base_tabindex_increment = 100;

	/// @copydoc \Duf\Compiler\IFormCompiler::compileForm
	public static function compileForm(\Duf\FormCompiler $form, \Duf\PhpBuffer $buf)
	{
		// Start a form
		$buf->writeEcho('<form');
		$buf->writeEchoAttr('id',     '$form->id');
		$buf->writeEchoAttr('action', '$form->action_url');
		$buf->writeEchoAttr('method', '$form->http_method');
		$buf->beginBlock('if (isset($form->html_class))');
		$buf->write('static::renderClassAttr($form, $template_engine, $form->html_class);');
		$buf->endBlock();
		$buf->writeEchoLn('>');

		// Render errors
		$buf->beginBlock('if (!empty($form->form_errors))');
		$buf->writeEcho('<ul class="errors">');
		$buf->beginForeach('$form->form_errors', $error_type_var, $error_var);
		{
			$buf->writeEcho('<li');
			$class_var = $buf->getVar('class');
			$buf->write("{$class_var} = isset({$error_var}['class']) ? (array) {$error_var}['class'] : array();");
			$buf->write("{$class_var}[] = 'error_'.{$error_type_var};");
			$buf->writeEchoAttr('class', "join(' ', {$class_var})");
			$buf->writeEcho('>');
			$buf->writeHtml($error_var.'[\'message\']');
			$buf->writeEchoLn('</li>');
		}
		$buf->endBlock();
		$buf->writeEchoLn('</ul>');
		$buf->endBlock();

		//$form->compileRootWidget($buf);
		$buf->writeComment('Widgets go here.');
		$buf->writeEchoLn('Widgets go here.');

		$buf->beginBlock('if ($form->http_method == \'post\')');
		$buf->writeEcho("<input type=\"hidden\" name=\"__[");
		$buf->writeHtml('$form->getToken()');
		$buf->writeEcho("]\" value=\"1\">");
		$buf->endBlock();

		$buf->writeEcho("<!--[if IE]><input type=\"text\" disabled style=\"display:none!important;\" size=\"1\"><![endif]-->"); // IE bug: single text input
		$buf->writeEcho("</form>");
	}

}
