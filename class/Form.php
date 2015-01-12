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
 * 
 * @note Please note that [isSubmitted](@ref Duf\Form::isSubmitted)() does not
 * 	mean that form data are valid. And also [isValid](@ref Duf\Form::isValid)()
 * 	does not mean that form has been submitted. It is possible to get
 * 	submitted form with invalid data as well as non-submitted form with
 * 	valid data (for example filtering form).
 *
 * TODO Both field value preprocessor and postprocessor must be methods of
 * 	single class, because they must match together.
 */
class Form
{

	public $id;				///< Global form ID (HTML attribute)
	public $form_ttl = 750;			///< XSRF protection window (15 minutes by default)
	public $action_url = '';		///< Form target URL (empty = the same page)
	public $target_form_id = null;		///< Specify target form when action_url is specified (this form may have different ID on another URL).
	public $http_method = 'post';		///< Form submit method
	public $readonly = false;		///< Is form a read-only view ?
	public $html_class;			///< HTML class attribute (array of 'form', 'view', or anything else)
	protected $toolbox;			///< Listing of all available tools to build forms. Fields, layouts, helpers, etc.
	
	protected $form_def;			///< Definition of the form. What fields in what layouts.
	protected $field_defaults = array();	///< Default values used if form is not submitted. (single item dimension)
	protected $field_values = null;		///< Current value of the field (array, but it will be created very late).
	
	public $field_errors = array();		///< Errors from all fields; 2D structure (group, field).
	public $form_errors = array();		///< Global errors; simple list.

	public $base_tabindex = null;		///< Base tabindex of all inputs (input's tabindex is increased by this value).
						///< If null, it will be choosen by renderer.

	protected $raw_input = null;		///< Submitted input from user. Data are not modified in any way.
	protected $raw_defaults = array();	///< Preprocessed default values. These data go directly to HTML form.
	protected $use_defaults = false;	///< Use default (true) or submitted (false) values.
	protected $group_keys = array();	///< Group keys used for accessing fields in collections.

	/**
	 * @name Errors
	 * @{
	 */
	const E_FORM_EXPIRED = 'form_expired';		///< Error: The XSRF token has expired.
	const E_FORM_FIELD_ERROR = 'form_field_error';	///< Error: Form contains field with error.
	const E_FIELD_REQUIRED = 'field_required';	///< Error: The empty field is required.
	const E_FIELD_MALFORMED = 'field_malformed';	///< Error: The field value is malformed (does not match pattern or so).
	/// @}

	/**
	 * @name Form flags
	 * @{
	 */
	const READ_ONLY = 0x0001;		///< Form must be read only
	/// @}


	/**
	 * Create form described by $form_def using $toolbox.
	 *
	 * @param $id is form id. You can set $id to null here and set it later using setId().
	 * @param $form_def is the form definition.
	 * @param $toolbox is Toolbox used for form rendering.
	 * @param $form_flags is bitmap of [@ref Form flags].
	 */
	public function __construct($id, $form_def, Toolbox $toolbox, $form_flags = 0)
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

		// Set http method to GET and disable XSRF protection
		if (isset($this->form_def['form']['http_method']) && strtolower($this->form_def['form']['http_method']) == 'get') {
			$this->http_method = 'get';
		}

		// Set form TTL (XSRF token timeout)
		if (isset($this->form_def['form']['ttl'])) {
			$this->form_ttl = $this->form_def['form']['ttl'];
		}


		// Detect read only form
		if ($form_flags & self::READ_ONLY) {
			$this->readonly = true;
		} else if (isset($this->form_def['form']['readonly'])) {
			$this->readonly = $this->form_def['form']['readonly'];
		} else {
			$this->readonly = true;
			foreach ($this->form_def['field_groups'] as $group_def) {
				if (empty($group_def['readonly'])) {
					$this->readonly = false;
					break;
				}
			}
		}

		// Read-only form has different class
		if ($this->readonly) {
			$this->html_class = array('view');
		} else {
			$this->html_class = array('form');
		}

		// Add custom class
		if (!empty($form_def['form']['class'])) {
			if (is_array($form_def['form']['class'])) {
				$this->html_class = array_merge($this->html_class, $form_def['form']['class']);
			} else {
				$this->html_class[] = $form_def['form']['class'];
			}
		}

		// Apply field group generators
		foreach ($this->form_def['field_groups'] as $group => & $group_def) {
			if (isset($group_def['field_group_generator'])) {
				$this->toolbox->updateFieldGroup($group_def['field_group_generator'], $group_def);
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
		if (isset($_SERVER['SERVER_NAME']) && isset($_SERVER['REQUEST_URI'])) {
			$url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		} else {
			$url = '';
		}

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
		// All this is relatively easy to guess, but at least this will 
		// be the same accross mutliple server instances.
		return array(
			isset($_SERVER['REMOTE_ADDR'])     ? $_SERVER['REMOTE_ADDR']     : '',
			isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
			isset($_SERVER['SERVER_NAME'])     ? $_SERVER['SERVER_NAME']     : '',
			isset($_SERVER['DOCUMENT_ROOT'])   ? $_SERVER['DOCUMENT_ROOT']   : '',
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
		if (isset($_SERVER['SERVER_NAME']) && isset($_SERVER['REQUEST_URI'])) {
			$url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		} else {
			$url = '';
		}

		if ($token_hash === sha1("$t:$salt:$form_id:$url:$extras")) {
			return (int) $t;
		}

		// Second try using referer URL
		if (isset($_SERVER['HTTP_REFERER'])) {
			$referer = parse_url($_SERVER['HTTP_REFERER']);
			$url = $referer['host'].$referer['path'];
			if ($token_hash === sha1("$t:$salt:$form_id:$url:$extras")) {
				return (int) $t;
			}
		}

		return FALSE;
	}


	/**
	 * Get token for this form (simple helper).
	 */
	public function getToken()
	{
		return static::createFormToken($this->target_form_id ? $this->target_form_id : $this->id);
	}


	/**
	 * Retrieve field groups
	 */
	public function getFieldGroups()
	{
		return $this->form_def['field_groups'];
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
			$this->raw_defaults = null; // Reset cache (just to make sure it is empty)
			$this->field_defaults = $custom_defaults;
		} else {
			$this->field_defaults[$group] = $custom_defaults;
		}
	}


	/**
	 * Get group/field default value
	 */
	public function getDefaultValue($group, $field = null)
	{
		if (!isset($this->form_def['field_groups'][$group])) {
			throw new \InvalidArgumentException('Group does not exist: '.$group);
		}
		return $field !== null
			? (isset($this->field_defaults[$group][$field]) ? $this->field_defaults[$group][$field] : null)
			: (isset($this->field_defaults[$group]) ? $this->field_defaults[$group] : null);
	}


	/**
	 * Returns values submitted by user.
	 */
	public function getValues()
	{
		if ($this->use_defaults) {
			foreach ($this->form_def['field_groups'] as $gi => $g) {
				// Read-only values are input-only
				if ($this->readonly || !empty($g['readonly'])) {
					continue;
				}

				// $this->field_defaults should contain all defaults by now, but maybe some of values are missing.
				if (!isset($this->field_defaults[$gi])) {
					$this->field_defaults[$gi] = array();
					if (empty($g['collection_dimensions'])) {
						// Values for the group are missing, use defaults from the form definition.
						foreach ($g['fields'] as $fi => $f) {
							if (isset($f['default'])) {
								$this->field_defaults[$gi][$fi] = $f['default'];
							}
						}
					} else {
						// Empty collection by default.
						$this->field_defaults[$gi] = array();
					}
				}
			}

			return $this->field_defaults;
		} else {
			if ($this->field_values === null) {
				$this->field_values = array();
				//$this->field_values = $this->raw_input;

				foreach ($this->form_def['field_groups'] as $gi => $g) {
					if ($this->readonly || !empty($g['readonly'])) {
						// Read-only values are input-only
						continue;
					}
					if (empty($g['collection_dimensions'])) {
						// Simple group
						if (isset($this->raw_input[$gi])) {
							$this->getValues_processCollection($this->raw_input[$gi], 0, $gi, $g['fields'],
								$this->field_values[$gi]);
						} else {
							// No data, use empty "object"
							$this->field_values[$gi] = array();
						}
					} else if ($g['collection_dimensions'] >= 1) {
						// Validate fields for each item in collection.
						if (isset($this->raw_input[$gi])) {
							// Non-empty collection, walk it recursively.
							$this->getValues_processCollection($this->raw_input[$gi], $g['collection_dimensions'], $gi, $g['fields'],
								$this->field_values[$gi]);
						} else {
							// Missing data, it should not happen, but whatever ... use empty collection instead.
							$this->field_values[$gi] = array();
						}
					}
				}
			}
			return $this->field_values;
		}
	}


	/**
	 * Recursively walk the collections in raw input and populate field values.
	 *
	 * TODO: Replace this with CollectionWalker::walkCollection() or CollectionWalker::walkCollectionWithTarget().
	 */
	private function getValues_processCollection($raw_input, $remaining_depth, $group_id, $group_fields,
			& $field_values, & $path = null, $depth = 0)
	{
		if ($remaining_depth > 0) {
			// We need to go deeper ...
			if ($path === null) {
				$path = array();
			}
			foreach($raw_input as $i => $raw_input_subtree) {
				$path[$depth] = $i;
				$this->getValues_processCollection($raw_input_subtree, $remaining_depth - 1, $group_id, $group_fields,
					$field_values[$i], $path, $depth + 1);
			}
		} else {
			// Deep enough.
			foreach ($group_fields as $fi => $f) {
				// Is field present in the form ?
				if (!isset($raw_input[$fi])) {
					// Ignore missing fields
					continue;
				}

				// Validate value
				$validators = $this->toolbox->getFieldValidators($f['type']);
				if (!empty($validators)) {
					$this->setCollectionKey($group_id, $path);
					foreach ($validators as $vi => $v) {
						$v::validateField($this, $group_id, $fi, $f, $raw_input[$fi]);
					}
				}

				// Call post-processor, if set
				$processor_class = $this->toolbox->getFieldValueProcessor($f['type']);
				if ($processor_class) {
					$processor_class::valuePostProcess($raw_input, $field_values, $this, $group_id, $fi, $f);
				} else {
					$field_values[$fi] = $raw_input[$fi] === '' ? null : $raw_input[$fi];
				}
			}
			$this->unsetCollectionKey($group_id);
		}
	}


	/**
	 * Set collection key. This key will be used to access particular item 
	 * when accessing group field.
	 *
	 * @param $group is field group id which is beiing iterated.
	 * @param $key is collection key (current index). Use array for 
	 * 	multidimensional collections.
	 *
	 * @see unsetCollectionKey(), getCollectionKey(), getRawData()
	 */
	public function setCollectionKey($group, $key)
	{
		$this->group_keys[$group] = (array) $key;
	}


	/**
	 * Retrieve collection key for given group.
	 *
	 * @param $group is field group id which is beiing iterated.
	 *
	 * @see setCollectionKey(), unsetCollectionKey(), getRawData()
	 */
	public function getCollectionKey($group)
	{
		return $this->group_keys[$group];
	}

	/**
	 * Unset collection key. It is a good idea to unset the key after group 
	 * is rendered to allow error detection.
	 *
	 * @param $group is field group id which is beiing iterated.
	 *
	 * @see setCollectionKey(), getCollectionKey(), getRawData()
	 */
	public function unsetCollectionKey($group)
	{
		unset($this->group_keys[$group]);
	}


	/**
	 * Get data for a view. Like getRawData(), but without processing,
	 * because view needs to display the real data.
	 *
	 * Default values specified in field configuration are ignored.
	 *
	 * Use this in `@view` renderer.
	 */
	public function getViewData($group, $field = null)
	{
		if (!isset($this->form_def['field_groups'][$group])) {
			throw new \InvalidArgumentException('Group does not exist: '.$group);
		}
		return $this->getArrayItemByPath($this->field_defaults[$group],
			isset($this->group_keys[$group]) ? $this->group_keys[$group] : null,
			$field);
	}


	/**
	 * Get raw data for HTML form field.
	 *
	 * Use this in `@edit` renderer.
	 */
	public function getRawData($group, $field = null)
	{
		if ($this->readonly || $this->use_defaults || !empty($this->form_def['field_groups'][$group]['readonly'])) {
			// Default values need to be converted to raw form data.
			if (!isset($this->raw_defaults[$group])) {
				foreach ($this->form_def['field_groups'] as $gi => $g) {
					if (!empty($g['wild'])) {
						// Wild group has no default data, but group still exists
						$this->raw_defaults[$gi] = array();
					} else if (isset($this->field_defaults[$gi])) {
						// Values for the group are set, use them.
						$this->preProcessGroup($gi, $g, $this->field_defaults[$gi], $this->raw_defaults[$gi]);
					} else {
						if (empty($g['collection_dimensions'])) {
							// Values for the group are missing, use defaults from the form definition.
							$group_defaults = array();
							foreach ($g['fields'] as $fi => $f) {
								if (isset($f['default'])) {
									$group_defaults[$fi] = $f['default'];
								}
							}
							$this->preProcessGroup($gi, $g, $group_defaults, $this->raw_defaults[$gi]);
						} else {
							// Empty collection by default.
							$this->raw_defaults[$gi] = array();
						}
					}
				}
			}

			if (isset($this->raw_defaults[$group])) {
				return $this->getArrayItemByPath($this->raw_defaults[$group],
					isset($this->group_keys[$group]) ? $this->group_keys[$group] : null,
					$field);
			} else {
				return null;
			}
		} else {
			if (isset($this->raw_input[$group])) {
				return $this->getArrayItemByPath($this->raw_input[$group],
					isset($this->group_keys[$group]) ? $this->group_keys[$group] : null,
					$field);
			} else {
				return null;
			}
		}
	}


	/**
	 * Apply preprocessor on all fields in given group.
	 */
	private function preProcessGroup($gi, $g, $default_values, & $raw_values)
	{
		$form = $this;
		$dimensions = isset($g['collection_dimensions']) ? $g['collection_dimensions'] : 0;

		CollectionWalker::walkCollectionWithTarget($default_values, $raw_values, $dimensions,
			function($collection_key, $default_values, & $raw_values) use ($gi, $g, $form) {
				foreach ($g['fields'] as $fi => $f) {
					$processor_class = $this->toolbox->getFieldValueProcessor($f['type']);
					if ($processor_class) {
						$processor_class::valuePreProcess($default_values, $raw_values, $form, $gi, $fi, $f);
					} else if (isset($default_values[$fi])) {
						$raw_values[$fi] = $default_values[$fi];
					} else {
						$raw_values[$fi] = null;
					}
				}
			}
		);
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
			$method = isset($this->form_def['form']['http_method']) ? $this->form_def['form']['http_method'] : 'post';
			switch ($method) {
				case 'get':
				case 'GET':
				case 'Get':
					$this->raw_input = $_GET;
					break;
				case 'post':
				case 'POST':
				case 'Post':
					$this->raw_input = $_POST;
					break;
				default:
					throw new \InvalidArgumentException('Unknown HTTP method: '.$method);
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
		if ($this->http_method == 'get') {
			// GET form is always submitted
			return TRUE;
		} else if (isset($this->raw_input['__'])) {
			$__ = $this->raw_input['__'];
		} else {
			return FALSE;
		}
		foreach ((array) $__ as $token => $x) {
			$t = static::validateFormToken($token, $this->id);
			if ($t !== FALSE) {
				// Submitted
				if ($this->form_ttl > 0 && time() - $t > $this->form_ttl) {
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
	 *
	 * Validation is done by getValues(), which is called automatically by 
	 * this method.
	 *
	 * @see
	 * 	- http://www.the-art-of-web.com/html/html5-form-validation/
	 * 	- http://cz2.php.net/manual/en/book.filter.php
	 */
	public function isValid()
	{
		// Make sure values are processed and validated.
		$this->getValues();

		if (!empty($this->field_errors)) {
			$this->form_errors[self::E_FORM_FIELD_ERROR] = array(
				'message' => _('The form is not correctly filled. Please check marked fields.'),
			);
		}

		return empty($this->form_errors) && empty($this->field_errors);
	}


	/**
	 * Assign error to field
	 */
	public function setFieldError($group_id, $field_id, $error, $args = true)
	{
		if (!$group_id || !$field_id) {
			throw new \InvalidArgumentException('Field not specified.');
		}
		$e = & $this->refArrayItemByPath($this->field_errors, $group_id,
			isset($this->group_keys[$group_id]) ? $this->group_keys[$group_id] : null, $field_id);
		$e[$error] = $args;
	}

	/**
	 * Get all errors assigned to a field
	 */
	public function getFieldErrors($group_id, $field_id)
	{
		return $this->getArrayItemByPath($this->field_errors, $group_id,
			isset($this->group_keys[$group_id]) ? $this->group_keys[$group_id] : null, $field_id);
	}


	/**
	 * Helper method to get correct HTML form field ID.
	 */
	public function getHtmlFieldId($group, $field, $field_component = null)
	{
		$group_keys = isset($this->group_keys[$group]) ? '__'.join('__', $this->group_keys[$group]) : '';

		if ($field_component) {
			return htmlspecialchars("{$this->id}__{$group}{$group_keys}__{$field}__{$field_component}");
		} else {
			return htmlspecialchars("{$this->id}__{$group}{$group_keys}__{$field}");
		}
	}


	/**
	 * Helper method to get correct HTML form field name.
	 */
	public function getHtmlFieldName($group, $field, $field_component = null)
	{
		$group_keys = isset($this->group_keys[$group]) ? '['.join('][', $this->group_keys[$group]).']' : '';

		if ($field_component) {
			return htmlspecialchars("${group}{$group_keys}[$field][$field_component]");
		} else {
			return htmlspecialchars("${group}{$group_keys}[$field]");
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
	 * Get field group definition.
	 */
	public function getFieldGroup($group)
	{
		return $this->form_def['field_groups'][$group];
	}


	/**
	 * Get field group option.
	 */
	public function getFieldGroupOption($group, $option)
	{
		if (isset($this->form_def['field_groups'][$group][$option])) {
			return $this->form_def['field_groups'][$group][$option];
		} else if (!isset($this->form_def['field_groups'][$group])) {
			throw new \InvalidArgumentException('Unknown field group: '.$group);
		} else {
			return null;
		}
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
		// Get form renderer
		$form_renderer = $this->toolbox->getFormRenderer($this->readonly ? '@view' : '@edit');

		// Render
		$this->common_field_renderers = $this->toolbox->getFormCommonFieldRenderers();
		if (is_a($form_renderer, 'Duf\\Renderer\\IFormRenderer', TRUE)) {
			$form_renderer::renderForm($this, $template_engine);
		} else {
			throw new RendererException('Form renderer '.$form_renderer.' must implement Duf\\Renderer\\IFormRenderer inteface.');
		}
	}


	/**
	 * Start widget tree rendering.
	 */
	public function renderRootWidget($template_engine)
	{
		$this->renderWidget($template_engine, $this->form_def['layout']);
	}


	/**
	 * Render a widget using given configuration. Key `#!` determines 
	 * renderer which will render the widget.
	 */
	public function renderWidget($template_engine, $widget_conf)
	{
		// Lookup renderer in toolbox
		if (isset($widget_conf['#!'])) {
			$renderer_name = $widget_conf['#!'];
		} else {
			throw new \InvalidArgumentException('Shebang is missing in widget configuration.');
		}

		if ($renderer_name[0] == '@') {
			// Field renderer: Lookup field type and select correct renderer
			if (empty($widget_conf['group_id']) || empty($widget_conf['field_id'])) {
				throw new RendererException('No field specified.');
			}
			
			$group_id = $widget_conf['group_id'];
			$field_id = $widget_conf['field_id'];

			// Get group definition
			if (!isset($this->form_def['field_groups'][$group_id])) {
				throw new RendererException('Unknown field group: '.$group_id);
			}
			$group_def = $this->form_def['field_groups'][$group_id];

			// Get field definition
			if (!isset($group_def['fields'][$field_id])) {
				throw new RendererException('Unknown field: '.$field_id);
			}
			$field_conf = $group_def['fields'][$field_id];

			// Renderer substitution for read-only groups
			if ($this->readonly || !empty($group_def['readonly'])) {
				switch ($renderer_name) {
					case '@edit':
						$renderer_name = '@view';
						break;
				}
			}

			// Get type of the field (widget_conf may override field_conf here)
			if (isset($widget_conf['type'])) {
				$type = $widget_conf['type'];
			} else {
				$type = $field_conf['type'];
			}

			// Get renderer class
			$renderer_class = $this->toolbox->getFieldRenderer($type, $renderer_name);
			if (!isset($renderer_class)) {
				throw new RendererException('Renderer class not specified for renderer "'.$renderer_name.'" of field type "'.$type.'".');
			}

			//debug_msg('Field: %s, %s, %s: %s', $group_id, $field_id, $renderer_name, var_export($widget_conf, TRUE));

			// Execute renderer
			if ($renderer_class !== false) {
				if (class_exists($renderer_class)) {
					$renderer_class::renderFieldWidget($this, $template_engine, $widget_conf, $group_id, $field_id, $field_conf);
				} else {
					throw new RendererException('Field renderer class '.$renderer_class.' does not exist.');
				}
			}
		} else {
			// Widget
			$renderer_class = $this->toolbox->getWidgetRenderer($renderer_name);
			if (!isset($renderer_class)) {
				throw new RendererException('Renderer class not specified for renderer "'.$renderer_name.'".');
			}

			// Execute renderer
			if ($renderer_class !== false) {
				if (class_exists($renderer_class)) {
					$renderer_class::renderWidget($this, $template_engine, $widget_conf);
				} else {
					throw new RendererException('Widget renderer class '.$renderer_class.' does not exist.');
				}
			}
		}
	}


	/**
	 * Helper method to render list of widgets.
	 */
	public function renderWidgets($template_engine, $widget_conf_list)
	{
		if (isset($widget_conf_list)) {
			foreach ($widget_conf_list as $widget_conf) {
				$this->renderWidget($template_engine, $widget_conf);
			}
		}
	}


	/**
	 * Helper method to render a field widget.
	 *
	 * TODO: Refactor renderWidget since fields are not ordinary widgets.
	 */
	public function renderField($template_engine, $group_id, $field_id, $renderer)
	{
		$this->renderWidget($template_engine, array(
				'#!' => $renderer,
				'group_id' => $group_id,
				'field_id' => $field_id,
			));
	}


	/**
	 * Returns item of multidimensional $array specified by keys, or null when item is not found.
	 *
	 * @param $array is an array to walk.
	 * @param ... Additional parameters are keys. If key is array, all its 
	 * 	items are used as keys. `null` values are skipped.
	 */
	private function getArrayItemByPath($array)
	{
		$p = $array;
		$argc = func_num_args();
		for ($i = 1; $i < $argc; $i++) {
			$k = func_get_arg($i);
			if ($k === null) {
				continue;
			}
			if (is_array($k)) {
				foreach ($k as $kk) {
					if (isset($p[$kk])) {
						$p = $p[$kk];
					} else {
						return null;
					}
				}
			} else {
				if (isset($p[$k])) {
					$p = $p[$k];
				} else {
					return null;
				}
			}
		}

		return $p;
	}


	/**
	 * Returns reference to item of multidimensional $array specified by 
	 * keys. If item is missing, it is created. Created values are empty 
	 * arrays.
	 *
	 * @param $array is an array to walk.
	 * @param ... Additional parameters are keys. If key is array, all its 
	 * 	items are used as keys. `null` values are skipped.
	 */
	private function & refArrayItemByPath(& $array)
	{
		$p = & $array;
		$argc = func_num_args();
		for ($i = 1; $i < $argc; $i++) {
			$k = func_get_arg($i);
			if ($k === null) {
				continue;
			}
			if (is_array($k)) {
				foreach ($k as $kk) {
					if (!isset($p[$kk])) {
						$p[$kk] = array();
					}
					$p = & $p[$kk];
				}
			} else {
				if (!isset($p[$k])) {
					$p[$k] = array();
				}
				$p = & $p[$k];
			}
		}

		return $p;
	}

}


