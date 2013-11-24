<?php
/*
 * Copyright (c) 2013, Josef Kufner  <jk@frozen-doe.net>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. Neither the name of the author nor the names of its contributors
 *    may be used to endorse or promote products derived from this software
 *    without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE REGENTS AND CONTRIBUTORS ``AS IS'' AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED.  IN NO EVENT SHALL THE REGENTS OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
 * OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
 * SUCH DAMAGE.
 */

namespace Duf;

class Renderer
{

	/**
	 * Default form renderer
	 */
	static function form(Form $form, $template_engine = null)
	{
		echo "<form id=\"", htmlspecialchars($form->id), "\" class=\"duf_form\" ",
			"action=\"", htmlspecialchars($form->action_url), "\" ",
			"method=\"", htmlspecialchars($form->http_method), "\">\n";
		$form->renderRootLayout($template_engine);
		echo "<input type=\"hidden\" name=\"__[", htmlspecialchars($form->hashId()), "]\" value=\"1\">\n";
		echo "<!--[if IE]><input type=\"text\" disabled style=\"display:none!important;\" size=\"1\"><![endif]-->\n"; // IE bug: single text input
		echo "</form>\n";
	}


	/**
	 * Default label renderer
	 */
	static function label(Form $form, $group_id, $field_id, $field_def, $template_engine = null)
	{
		echo "<label type=\"", htmlspecialchars($field_def['type']), "\">",
			htmlspecialchars(sprintf(_('%s:'), $field_def['label'])),
			"</label>\n";
	}


	/**
	 * Default field renderer
	 */
	static function input(Form $form, $group_id, $field_id, $field_def, $value, $template_engine = null)
	{
		echo "<input type=\"", htmlspecialchars($field_def['type']), "\" ",
			"name=\"", $form->getHtmlFieldName($group_id, $field_id), "\" ",
			"value=\"", htmlspecialchars($form->getRawData($group_id, $field_id)), "\">\n";
	}


	/**
	 * Default textarea renderer
	 */
	static function textarea(Form $form, $group_id, $field_id, $field_def, $value, $template_engine = null)
	{
		echo "<textarea class=\"", htmlspecialchars($field_def['type']), "\" ",
			"name=\"", $form->getHtmlFieldName($group_id, $field_id), "\">",
			htmlspecialchars($form->getRawData($group_id, $field_id)),
			"</textarea>\n";
	}


	/**
	 * Default textarea renderer
	 */
	static function select(Form $form, $group_id, $field_id, $field_def, $value, $template_engine = null)
	{
		echo "<select name=\"", $form->getHtmlFieldName($group_id, $field_id), "\" ",
			"class=\"", htmlspecialchars($field_def['type']), "\">\n";
		$value = $form->getRawData($group_id, $field_id);
		foreach ($field_def['options'] as $key => $option) {
			if (is_array($option)) {
				$opt_label = $option['label'];
				$opt_value = $option['value'];
			} else {
				$opt_label = $option;
				$opt_value = $key;
			}
			echo "<option value=\"", htmlspecialchars($opt_value), "\"", ($value == $opt_value ? " selected":""), ">",
				htmlspecialchars($opt_label), "</option>\n";
		}
		echo "</select>\n";
	}


	/**
	 * Submit field renderer
	 */
	static function submit(Form $form, $group_id, $field_id, $field_def, $value, $template_engine = null)
	{
		echo "<input type=\"", htmlspecialchars($field_def['type']), "\" ",
			"name=\"", $form->getHtmlFieldName($group_id, $field_id), "\" ",
			"value=\"", htmlspecialchars($field_def['label']), "\">\n";
	}


	/**
	 * Default layout renderer
	 */
	static function layoutDefault(Form $form, $layout_def, $template_engine = null)
	{
		echo "<table class=\"duf_form\">\n";
		foreach ($form->getAllFields() as $group_id => $fields) {
			foreach ($fields as $field_id => $field_def) {
				echo "<tr>\n";
				echo "<th>\n";
				$form->renderField($group_id, $field_id, 'label', null, $template_engine);
				echo "</th>\n";
				echo "<td>\n";
				$form->renderField($group_id, $field_id, null, array('label'), $template_engine);
				echo "</td>\n";
				echo "</tr>\n";
			}
		}
		echo "</table>\n";
	}

}

