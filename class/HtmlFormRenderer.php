<?php
/*
 * Copyright (c) 2013, Josef Kufner  <jk@frozen-doe.net>
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

namespace Duf;

/**
 * Collection of form renderers to create HTML5 form.
 */
class HtmlFormRenderer
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
		echo "<input type=\"hidden\" name=\"__[", htmlspecialchars($form->getToken()), "]\" value=\"1\">\n";
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
		foreach ($form->getAllFieldgroups() as $group_id => $group_config) {
			foreach ($group_config['fields'] as $field_id => $field_def) {
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

