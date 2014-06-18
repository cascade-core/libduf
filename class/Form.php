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
 * HTML form logic
 *
 * Form lifetime:
 *
 *   A. Form is not submitted:
 *       1.  Initialize form -- load definition.
 *       2.  Process input (empty for now).
 *       3.  Load default values.
 *       4.  Set form to use default values.
 *       5.  Show form.
 *
 *   B. Form is submitted:
 *       1.  Initialize form -- load definition.
 *       2.  Process input (submission is detected).
 *       3.  Validate user input.
 *       4a. If input is not valid, set form to use submitted values and show the form.
 *       4b. Otherwise pass values to application.
 *
 */
class Form
{

	/**
	 * Global form ID (HTML attribute)
	 */
	public $id;

	/**
	 * Form target URL (empty = the same page)
	 */
	public $action_url = '';

	/**
	 * Form submit method
	 */
	public $http_method = 'post';

	/**
	 * Definition of the form. What fields in what layouts.
	 */
	protected $form_def;

	/**
	 * Default values used if form is not submitted.
	 */
	protected $field_defaults = array();

	/**
	 * Current value of the field.
	 */
	protected $field_values = array();

	/**
	 * All collected errors from all fields.
	 */
	protected $field_errors = array();

	/**
	 * Submitted input from user. Data are not modified in any way.
	 */
	protected $raw_input = null;

	/**
	 * Preprocessed default values. These data go directly to HTML form.
	 */
	protected $raw_defaults = null;

	/**
	 * Which value set should be used ?
	 *
	 * true = use default values
	 * false = use submitted values
	 */
	protected $use_defaults = false;

	/**
	 * Listing of all available tools to build forms. Fields, layouts, 
	 * helpers, etc.
	 */
	protected $toolbox;


	/**
	 * Create form described by $form_def using $toolbox.
	 *
	 * You can set $id to null here and set it later using setId().
	 */
	public function __construct($id, $form_def, Toolbox $toolbox)
	{
		$this->id = $id;
		$this->form_def = $form_def;
		$this->toolbox = $toolbox;

		if (empty($this->form_def)) {
			throw new \InvalidArgumentException('Missing form definition!');
		}
		if (empty($this->form_def['field_groups'])) {
			throw new \InvalidArgumentException('Missing "field_groups" section in configuration.');
		}

		foreach ($this->form_def['field_groups'] as $group => & $group_config) {
			$field_source = @ $group_config['field_source'];
			if ($field_source !== null) {
				$group_config['fields'] = $this->toolbox->getFieldsFromSource($field_source, $group_config);
			}
		}
	}


	/**
	 * Set form ID. Use if ID cannot be set in constructor.
	 */
	public function setId($id)
	{
		if (isset($this->id)) {
			throw new \RuntimeException('ID is already set, you cannot change it once it is set!');
		}
		$this->id = $id;
	}


	/**
	 * Hash of form ID for submit detection
	 *
	 * This hashed ID is per-form specific constant and does not change 
	 * over time. Hash is used to hide implementational details from API 
	 * and to make nice alphanumeric string.
	 */
	public function hashId()
	{
		return md5($this->id);
	}


	/**
	 * Retrieve field groups
	 */
	public function getFieldGroups()
	{
		return $this->form_def['field_groups'];
	}


	/**
	 * Collect default values from form definition. Used when user has not submitted anything else. 
	 */
	public function loadDefaults()
	{
		// Collect default values from the form definition
		$def_defaults = array();
		foreach ($this->form_def['field_groups'] as $group_name => $group_config) {
			foreach ($group_config['fields'] as $field_name => $field) {
				if (isset($field['default'])) {
					$def_defaults[$group_name][$field_name] = $field['default'];
				}
			}
		}

		$this->field_defaults = $def_defaults;
	}


	/**
	 * Set custom default values.
	 *
	 * Does array_merge() definition defaults with custom defaults.
	 * 
	 * $custom_defaults has the same structure as values returned by 
	 * getValues(), if $group is null. Otherwise $custom_defaults is only 
	 * fragment for specified field group.
	 */
	public function setDefaults($custom_defaults, $group = null)
	{
		// Merge definition defaults with custom defaults -- custom defaults win
		if ($group === null) {
			foreach ($custom_defaults as $k => $v) {
				$this->field_defaults[$k] = array_merge((array) @ $this->field_defaults[$k], (array) @ $custom_defaults[$k]);
			}
		} else {
			$this->field_defaults[$group] = array_merge((array) @ $this->field_defaults[$group], (array) $custom_defaults);
		}
	}


	/**
	 * Load submitted input
	 *
	 * It is possible to use different input than $_GET or $_POST to make 
	 * testing easy. If $raw_input is null, appropriate superglobal variable 
	 * is used.
	 */
	public function loadInput($raw_input = null)
	{
		if ($raw_input !== null) {
			$this->raw_input = $raw_input;
		} else {
			switch (@ $this->form_def['form']['http_method']) {
				case 'get':
				case 'GET':
				case 'Get':
					$this->raw_input = $_GET;
					break;
				case null:
				case 'post':
				case 'POST':
				case 'Post':
					$this->raw_input = $_POST;
					break;
				default:
					throw new \InvalidArgumentException('Unknown HTTP method: '
						.$this->form_def['form']['http_method']);
			}
		}
	}


	/**
	 * Sets form to use default values.
	 */
	public function useDefaults()
	{
		$this->use_defaults = true;
	}


	/**
	 * Sets form to use user submitted values.
	 */
	public function useInput()
	{
		$this->use_defaults = false;
	}


	/**
	 * Returns true when form is submitted. Data may not be valid.
	 */
	public function isSubmitted()
	{
		return isset($this->raw_input['__'][$this->hashId()]);
	}


	/**
	 * Returns true when all data are valid. The form may not be submitted.
	 */
	public function isValid()
	{
		// TODO: Validate $this->field_values (post-processed values, second stage of validation).


		// TODO: http://www.the-art-of-web.com/html/html5-form-validation/
		// TODO: http://cz2.php.net/manual/en/book.filter.php

		return empty($this->field_errors);
	}


	/**
	 * Returns values submitted by user.
	 */
	public function getValues()
	{
		if ($this->use_defaults) {
			return $this->field_defaults;
		} else {
			// TODO: Call post-process functions, populate $this->field_errors (first stage of validation).
			$this->field_values = $this->raw_input;

			return $this->field_values;
		}
	}


	/**
	 * Get raw data for HTML form field.
	 */
	public function getRawData($group, $field)
	{
		if ($this->use_defaults) {
			if ($this->raw_defaults === null) {
				// TODO: Call pre-process functions to produce raw form data
				$this->raw_defaults = $this->field_defaults;
			}
			return $this->raw_defaults[$group][$field];
		} else {
			return $this->raw_input[$group][$field];
		}
	}

	/**
	 * Helper method to get correct HTML form field name.
	 */
	public function getHtmlFieldName($group, $field, $field_component = null)
	{
		if ($field_component) {
			return htmlspecialchars("${group}[$field][$field_component]");
		} else {
			return htmlspecialchars("${group}[$field]");
		}
	}


	/**
	 * Get field definitions.
	 */
	public function getAllFieldGroups()
	{
		return $this->form_def['field_groups'];
	}


	/**
	 * Get field definitions by group.
	 */
	public function getFieldGroup($group)
	{
		return $this->form_def['field_groups'][$group];
	}


	/**
	 * Get field definition.
	 */
	public function getField($group, $field)
	{
		return $this->form_def['field_groups'][$group]['fields'][$field];
	}


	/**
	 * Render form using specified renderers.
	 */
	public function render($template_engine = null)
	{
		call_user_func($this->toolbox->getFormRenderer(), $this, $template_engine);
	}


	/**
	 * Start layout tree rendering.
	 */
	public function renderRootLayout($template_engine = null)
	{
		$this->renderLayout($this->form_def['layout'], $template_engine);
	}


	/**
	 * Render layout subtree using specified renderer.
	 */
	public function renderLayout($layout_def, $template_engine = null)
	{
		$type = $layout_def['type'];

		call_user_func($this->toolbox->getLayoutRenderer($type), $this, $layout_def, $template_engine);
	}


	/**
	 * Render field using specified renderer.
	 */
	public function renderField($group_id, $field_id, $use_renderers, $exclude_renderers, $template_engine = null)
	{
		$field_def = $this->form_def['field_groups'][$group_id]['fields'][$field_id];
		$type = $field_def['type'];
		$value = @ $this->field_values[$group_id][$field_id];

		$renderers = $this->toolbox->getFieldRenderers($type);

		if ($use_renderers === null) {
			// Use all renderers
			foreach ($renderers as $renderer => $renderer_fn) {
				if ($renderer_fn && ($exclude_renderers === null || !in_array($renderer, $exclude_renderers))) {
					call_user_func($renderer_fn, $this, $group_id, $field_id, $field_def, $value, $template_engine);
				}
			}
		} else if (is_array($use_renderers)) {
			// Use selected renderers
			foreach ($use_renderers as $r) {
				$renderer_fn = @ $renderers[$r];
				if ($renderer_fn && ($exclude_renderers === null || !in_array($r, $exclude_renderers))) {
					call_user_func($renderer_fn, $this, $group_id, $field_id, $field_def, $value, $template_engine);
				}
			}
		} else {
			// Use specific renderer
			$renderer_fn = @ $renderers[$use_renderers];
			if ($renderer_fn) {
				call_user_func($renderer_fn, $this, $group_id, $field_id, $field_def, $value, $template_engine);
			}
		}
	}

}


