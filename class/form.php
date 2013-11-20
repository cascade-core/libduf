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

/**
 * Form.
 */
class Form
{

	/**
	 * Global form ID (HTML attribute)
	 */
	public $id;

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
	 * Listing of all available tools to build forms. Fields, layouts, 
	 * helpers, etc.
	 */
	protected $toolbox;


	/**
	 * Create form described by $form_def using $toolbox.
	 */
	public function __construct($id, $form_def, $toolbox)
	{
		$this->id = $id;
		$this->form_def = $form_def;
		$this->toolbox = $toolbox;
	}


	/**
	 * Set default values used when user has not submitted anything else.
	 */
	public function setDefaults($defaults)
	{
		$this->field_defaults = $defaults;
	}


	/**
	 * Returns true when form is submitted. Data may not be valid.
	 */
	public function isSubmitted()
	{
	}


	/**
	 * Returns true when all data are valid. The form may not be submitted.
	 */
	public function isValid()
	{
	}


	/**
	 * Returns values submitted by user.
	 */
	public function getValues()
	{
		return $this->field_values;
	}


	/**
	 * Get field definitions.
	 */
	public function getAllFields()
	{
		return $this->form_def['fields'];
	}


	/**
	 * Get field definitions by group.
	 */
	public function getFieldGroup($group)
	{
		return $this->form_def['fields'][$group];
	}


	/**
	 * Get field definition.
	 */
	public function getField($group, $field)
	{
		return $this->form_def['fields'][$group][$field];
	}


	/**
	 * Render form using specified renderers.
	 */
	public function render($template_engine = null)
	{
		call_user_func($this->toolbox['form']['renderer'], $this, $template_engine);
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

		call_user_func($this->toolbox['layouts'][$type]['renderer'], $this, $layout_def, $template_engine);
	}


	/**
	 * Render field using specified renderer.
	 */
	public function renderField($group_id, $field_id, $use_renderers, $exclude_renderers, $template_engine = null)
	{
		$field_def = $this->form_def['fields'][$group_id][$field_id];
		$type = $field_def['type'];
		$value = @ $this->field_values[$group_id][$field_id];

		$renderers = $this->toolbox['field_types'][$type]['renderers'];

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


