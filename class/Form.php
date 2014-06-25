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

	public $id;				///< Global form ID (HTML attribute)
	public $form_ttl = 750;			///< XSRF protection window (15 minutes by default)
	public $action_url = '';		///< Form target URL (empty = the same page)
	public $http_method = 'post';		///< Form submit method
	protected $toolbox;			///< Listing of all available tools to build forms. Fields, layouts, helpers, etc.
	
	protected $form_def;			///< Definition of the form. What fields in what layouts.
	protected $field_defaults = array();	///< Default values used if form is not submitted.
	protected $field_values = null;		///< Current value of the field (array, but it will be created very late).
	
	public $field_errors = array();		///< Errors from all fields; 2D structure (group, field).
	public $form_errors = array();		///< Global errors; simple list.

	protected $raw_input = null;		///< Submitted input from user. Data are not modified in any way.
	protected $raw_defaults = null;		///< Preprocessed default values. These data go directly to HTML form.
	protected $use_defaults = false;	///< Use default (true) or submitted (false) values.

	/**
	 * @name Errors
	 * @{
	 */
	const E_FORM_EXPIRED = 'form_expired';		// Error: The XSRF token has expired.
	const E_FIELD_REQUIRED = 'field_required';	// Error: The empty field is required.
	const E_FIELD_MALFORMED = 'field_malformed';	// Error: The field value is malformed (does not match pattern or so).
	/// @}

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
	 * Generate form token for partial XSRF protection and form 
	 * identification.
	 *
	 * @return String value suitable for hidden `<input>`.
	 * @see validateFormToken()
	 */
	public static function createFormToken($form_id)
	{
		$t = time();
		$salt = mt_rand();

		// Get form URL (empty string when not in web server). When 
		// form is submitted, this URL will be same or contained in 
		// referer header (both is checked).
		$url = @$_SERVER['SERVER_NAME'] . @$_SERVER['REQUEST_URI'];

		$extras = join(':', static::getFormTokenExtras());
		$hash = sha1("$t:$salt:$form_id:$url:$extras");
		return "$t:$salt:$hash";
	}


	/**
	 * Get some additional client-specific values to make hash more secure and bound to client.
	 *
	 * Returned value must be constant as long as token should be valid.
	 *
	 * TODO: Add some secret and session-specific token to make it really hard to guess.
	 *
	 * @return Array of values, keys does not matter.
	 */
	protected static function getFormTokenExtras()
	{
		return array(
			@ $_SERVER['REMOTE_ADDR'],
			@ $_SERVER['HTTP_USER_AGENT'],
			@ $_SERVER['SERVER_NAME'],	// easy to guess
			@ $_SERVER['DOCUMENT_ROOT'],	// easy to guess
		);
	}


	/**
	 * Validate form token.
	 *
	 * @return If validation is successful, returns time when token has 
	 * 	been generated. Otherwise returns FALSE.
	 * @see createFormToken()
	 */
	public static function validateFormToken($token, $form_id)
	{
		@ list($t, $salt, $token_hash) = explode(':', $token);
		$extras = join(':', static::getFormTokenExtras());

		// First try using current URL (empty string when not in web server)
		$url = @$_SERVER['SERVER_NAME'].@$_SERVER['REQUEST_URI'];
		if ($token_hash === sha1("$t:$salt:$form_id:$url:$extras")) {
			return (int) $t;
		}

		// Second try using referer URL
		$referer = parse_url($_SERVER['HTTP_REFERER']);
		$url = $referer['host'].$referer['path'];
		if ($token_hash === sha1("$t:$salt:$form_id:$url:$extras")) {
			return (int) $t;
		}

		return FALSE;
	}


	/**
	 * Get token for this form (simple helper).
	 */
	public function getToken()
	{
		return static::createFormToken($this->id);
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
		foreach ($this->form_def['field_groups'] as $group_id => $group_config) {
			foreach ($group_config['fields'] as $field_id => $field) {
				if (isset($field['default'])) {
					$def_defaults[$group_id][$field_id] = $field['default'];
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
		foreach ((array) @ $this->raw_input['__'] as $token => $x) {
			$t = static::validateFormToken($token, $this->id);
			if ($t !== FALSE) {
				// Submitted
				if ($this->form_ttl !== null && time() - $t >$this->form_ttl) {
					// TODO: Set expiration error
					$this->form_errors[self::E_FORM_EXPIRED] = array(
						'message' => _('The form has expired, please check entered data and submit it again.')
					);
				}
				return TRUE;
			}
		}
		return FALSE;
	}


	/**
	 * Returns true when all data are valid. The form may not be submitted.
	 */
	public function isValid()
	{
		// TODO: Validate $this->field_values (post-processed values, second stage of validation).


		// TODO: http://www.the-art-of-web.com/html/html5-form-validation/
		// TODO: http://cz2.php.net/manual/en/book.filter.php
		
		$values = $this->getValues();

		foreach ($this->form_def['field_groups'] as $group_id => $group_config) {
			foreach ($group_config['fields'] as $field_id => $field_def) {
				$validators = $this->toolbox->getFieldValidators($field_def['type']);
				$value = @ $values[$group_id][$field_id];
				foreach ($validators as $v => $validator) {
					$validator::validateField($this, $group_id, $field_id, $field_def, $value);
				}
			}
		}

		return empty($this->field_errors);
	}


	/**
	 * Assign error to field
	 */
	public function setFieldError($group_id, $field_id, $error, $args = true)
	{
		$this->field_errors[$group_id][$field_id][$error] = $args;
	}

	/**
	 * Returns values submitted by user.
	 */
	public function getValues()
	{
		if ($this->use_defaults) {
			return $this->field_defaults;
		} else {
			if ($this->field_values === null) {
				// TODO: Call post-process functions, populate $this->field_errors (first stage of validation).
				$this->field_values = $this->raw_input;
			}

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
	 * Helper method to get correct HTML form field ID.
	 */
	public function getHtmlFieldId($group, $field, $field_component = null)
	{
		if ($field_component) {
			return htmlspecialchars("{$this->id}__{$group}__{$field}__{$field_component}");
		} else {
			return htmlspecialchars("{$this->id}__{$group}__{$field}");
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
		$this->common_field_renderers = $this->toolbox->getFormCommonFieldRenderers();
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
		$errors = @ $this->field_errors[$group_id][$field_id];

		$renderers = $this->toolbox->getFieldRenderers($type) + $this->common_field_renderers;

		if ($use_renderers === null) {
			// Use all renderers
			foreach ($renderers as $renderer => $renderer_fn) {
				if ($renderer_fn && ($exclude_renderers === null || !in_array($renderer, $exclude_renderers))) {
					call_user_func($renderer_fn, $this, $group_id, $field_id, $field_def, $value, $errors, $template_engine);
				}
			}
		} else if (is_array($use_renderers)) {
			// Use selected renderers
			foreach ($use_renderers as $r) {
				$renderer_fn = @ $renderers[$r];
				if ($renderer_fn && ($exclude_renderers === null || !in_array($r, $exclude_renderers))) {
					call_user_func($renderer_fn, $this, $group_id, $field_id, $field_def, $value, $errors, $template_engine);
				}
			}
		} else {
			// Use specific renderer
			$renderer_fn = @ $renderers[$use_renderers];
			if ($renderer_fn) {
				call_user_func($renderer_fn, $this, $group_id, $field_id, $field_def, $value, $errors, $template_engine);
			}
		}
	}

}


