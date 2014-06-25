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
	static public function form(Form $form, $template_engine = null)
	{
		echo "<form ",
			"id=\"", htmlspecialchars($form->id), "\" class=\"duf_form\" ",
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
	static public function label(Form $form, $group_id, $field_id, $field_def, $value, $errors, $template_engine = null)
	{
		echo "<label ",
			"for=\"", $form->getHtmlFieldId($group_id, $field_id), "\">",
			htmlspecialchars(sprintf(_('%s:'), $field_def['label'])),
			"</label>\n";
	}


	/**
	 * Render common attributes, prefixed with space.
	 */
	static private function commonAttributes($field_def)
	{
		foreach ($field_def as $k => $v) {
			if ($v === null) {
				// null means 'not specified'
				continue;
			}
			switch ($k) {
				// HTML5 boolean attributes
				case 'hidden':
				case 'itemscope':
				case 'autofocus':	// only for: input, textarea, select
				case 'disabled':	// only for: input, textarea, select
				case 'readonly':	// only for: input, textarea
				case 'required':	// only for: input, textarea, select
					if ($v) {
						echo " $k";
					}
					break;

				// HTML5 string attributes
				case 'accesskey':
				case 'contextmenu':
				case 'dir':
				case 'form':
				case 'itemid':
				case 'lang':
				case 'style':
				case 'tabindex':
				case 'title':
				case 'contenteditable':
					echo " $k=\"", htmlspecialchars($v), "\"";
					break;

				// HTML5 tri-state boolean attributes
				case 'autocomplete':
					echo " $k=\"", $v ? 'on' : 'off', "\"";
					break;

				// HTML5 true/false/string attributes
				case 'draggable':
				case 'spellcheck':
					if ($v === true) {
						echo " $k=\"true\"";
					} else if ($v === false) {
						echo " $k=\"false\"";
					} else {
						echo " $k=\"", htmlspecialchars($v), "\"";
					}
					break;

				// HTML5 yes/no/string attributes
				case 'translate':
					if ($v === true) {
						echo " $k=\"yes\"";
					} else if ($v === false) {
						echo " $k=\"no\"";
					} else {
						echo " $k=\"", htmlspecialchars($v), "\"";
					}
					break;

				// HTML5 unordered set of unique space-separated tokens
				case 'class':
				case 'dropzone':
				case 'itemprop':
				case 'itemref':
				case 'itemtype':
					if (is_array($v)) {
						echo " $k=\"", htmlspecialchars(join(' ', $v)), "\"";
					} else {
						echo " $k=\"", htmlspecialchars($v), "\"";
					}
					break;

				// 'data-' prefixed attributes
				case 'data':
					foreach ($v as $dk => $dv) {
						if ($dv === null) {
							continue;
						}
						$dk = preg_replace('/["\'>\\/=\\0 \s]/', '_', $dk);
						if ($dv === true) {
							echo " data-$dk=\"true\"";
						} else if ($dv === false) {
							echo " data-$dk=\"false\"";
						} else if (is_array($dv) || is_object($dv)) {
							echo " data-$dk='", json_encode($dv, JSON_HEX_APOS | JSON_HEX_AMP 
								| JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "'";
						} else {
							echo " data-$dk=\"", htmlspecialchars($dv), "\"";
						}
					}
					break;
			}
		}
	}


	/**
	 * Default field renderer which assumes the field has only one input.
	 */
	static public function input(Form $form, $group_id, $field_id, $field_def, $value, $errors, $template_engine = null)
	{
		$type = $field_def['type'];

		echo "<input",
			" type=\"", htmlspecialchars($type), "\"",
			" id=\"", $form->getHtmlFieldId($group_id, $field_id), "\"",
			" name=\"", $form->getHtmlFieldName($group_id, $field_id), "\"";

		// Value
		switch ($type) {
			case 'submit':
				echo " value=\"", htmlspecialchars($field_def['label']), "\"";
				break;

			case 'checkbox':
				echo " value=\"1\"",
					$form->getRawData($group_id, $field_id) ? 'checked ' : '';
				break;

			case 'radio':
				// Radio cannot use this method, it needs different handling.
				throw new \Exception('Not supported.');

			default:
				echo " value=\"", htmlspecialchars($form->getRawData($group_id, $field_id)), "\"";
				break;
		}

		static::commonAttributes($field_def);

		// Specific HTML attributes
		foreach ($field_def as $k => $v) {
			if ($v === null) {
				// null means 'not specified'
				continue;
			}
			switch ($k) {
				// HTML5 boolean attributes
				case 'formnovalidate':
				case 'multiple':
					if ($v) {
						echo " $k";
					}
					break;

				// HTML5 string attributes
				case 'dirname':
				case 'formaction':
				case 'formenctype':
				case 'formmethod':
				case 'formtarget':
				case 'height':
				case 'width':
				case 'inputmode':
				case 'list':
				case 'max':
				case 'maxlength':
				case 'min':
				case 'minlength':
				case 'pattern':
				case 'placeholder':
				case 'size':
				case 'src':
				case 'step':
				case 'alt':
					echo " $k=\"", htmlspecialchars($v), "\"";
					break;

				// HTML5 set of comma-separated tokens
				case 'accept':
					if (is_array($v)) {
						echo " $k=\"", htmlspecialchars(join(',', $v)), "\"";
					} else {
						echo " $k=\"", htmlspecialchars($v), "\"";
					}
					break;
			}
		}
		echo ">\n";
	}


	/**
	 * Default textarea renderer which assumes the field has only one input.
	 */
	static public function textarea(Form $form, $group_id, $field_id, $field_def, $value, $errors, $template_engine = null)
	{
		echo "<textarea",
			" id=\"", $form->getHtmlFieldId($group_id, $field_id), "\"",
			" name=\"", $form->getHtmlFieldName($group_id, $field_id), "\"";

		// TODO: Add class by type

		static::commonAttributes($field_def);

		// Other HTML attributes
		foreach ($field_def as $k => $v) {
			if ($v === null) {
				// null means 'not specified'
				continue;
			}
			switch ($k) {
				// HTML5 string attributes
				case 'cols':
				case 'dirname':
				case 'inputmode':
				case 'maxlength':
				case 'minlength':
				case 'placeholder':
				case 'rows':
				case 'wrap':
					echo " $k=\"", htmlspecialchars($v), "\"";
					break;
			}
		}
		echo ">",
			htmlspecialchars($form->getRawData($group_id, $field_id)),
			"</textarea>\n";
	}


	/**
	 * Default textarea renderer which assumes the field has only one input.
	 */
	static public function select(Form $form, $group_id, $field_id, $field_def, $value, $errors, $template_engine = null)
	{
		// TODO: Add class by type

		echo "<select",
			" id=\"", $form->getHtmlFieldId($group_id, $field_id), "\"",
			" name=\"", $form->getHtmlFieldName($group_id, $field_id), "\"";

		static::commonAttributes($field_def);
		
		// Other HTML attributes
		foreach ($field_def as $k => $v) {
			if ($v === null) {
				// null means 'not specified'
				continue;
			}
			switch ($k) {
				// HTML5 string attributes
				case 'cols':
				case 'dirname':
				case 'inputmode':
				case 'maxlength':
				case 'minlength':
				case 'placeholder':
				case 'rows':
				case 'wrap':
					echo " $k=\"", htmlspecialchars($v), "\"";
					break;
			}
		}

		echo ">\n";
		$value = $form->getRawData($group_id, $field_id);
		if (!isset($field_def['options'][''])) {
			echo "<option value=\"\" ", $value == '' ? ' selected':'',
				!empty($field_def['required']) ? " disabled class=\"placeholder\" style=\"display: none;\"" : '',
				">",
				isset($field_def['placeholder']) ? htmlspecialchars($field_def['placeholder']) : '',
				"</option>";
		}
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
	 * Default error renderer
	 */
	static public function error(Form $form, $group_id, $field_id, $field_def, $value, $errors, $template_engine = null)
	{
		if (empty($errors)) {
			return;
		}
		echo "<ul class=\"errors\">\n";
		foreach ($errors as $error_code => $error) {
			$class = (array) @ $error['class'];
			$class[] = 'error_'.$error_code;
			echo "<li class=\"", htmlspecialchars(join(' ', $class)), "\">", htmlspecialchars($error['message']), "</li>\n";
		}
		echo "</ul>\n";
	}


	/**
	 * Default layout renderer
	 */
	static public function layoutDefault(Form $form, $layout_def, $template_engine = null)
	{
		echo "<table class=\"duf_form\">\n";
		if (!empty($form->form_errors)) {
			echo "<tr>\n",
				"<td colspan=\"2\">\n",
				"<ul class=\"errors\">\n";
			foreach ($form->form_errors as $error_type => $error) {
				echo "<li";
				$class = (array) @ $error['class'];
				$class[] = 'error_'.$error_type;
				echo " class=\"", htmlspecialchars(join(' ', $class)), "\"";
				echo ">", htmlspecialchars($error['message']), "</li>\n";
			}
			echo "</ul>\n",
				"</td>\n",
				"</tr>\n";
		}

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

