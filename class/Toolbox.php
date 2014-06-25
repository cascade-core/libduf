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
 * Form gets everything it needs from this toolbox.
 */
class Toolbox
{
	/**
	 * Toolbox configuration
	 */
	protected $config;

	/**
	 * Context
	 */
	protected $context;


	/**
	 * Create a toolbox.
	 */
	public function __construct($config, $context)
	{
		$this->config = $config;
		$this->context = $context;
	}


	/**
	 * Create Toolbox and initialize it using context.
	 *
	 * @warning This loads configuration using config_loader from 
	 * 	duf_toolbox.
	 */
	public static function createFromContext($config, $context)
	{
		// TODO: Use proper factory. This should not be here.
		$toolbox_config = $context->config_loader->load('duf_toolbox');
		return new self($toolbox_config, $context);
	}


	/**
	 * Generate fileds (one field group) for an entity type.
	 */
	public function getFieldsFromSource($source_name, $group_config)
	{
		$source_class = @ $this->config['field_sources'][$source_name];
		if ($source_class === null) {
			throw new \RuntimeException('Unknown field source: '.$source_name);
		}

		return $source_class::generateFieldGroup($group_config, $this->context);
	}

	/**
	 * Retrieve validator for given field type.
	 *
	 * @return `array('validator_name' => 'Duf\FieldValidator\IFieldValidator class name'))`
	 */
	public function getFieldValidators($field_type)
	{
		$validators = @ $this->config['field_types'][$field_type]['validators'];
		if ($validators === null) {
			throw new ValidatorException('Undefined field validators. Field type: '.$field_type);
		}
		return $validators;
	}



	/**
	 * Retrieve form renderer.
	 *
	 * @return `function($form, $template_engine)`
	 */
	public function getFormRenderer()
	{
		$renderer = @ $this->config['form']['renderer'];
		if ($renderer === null) {
			throw new RendererException('Undefined form renderer.');
		}
		return $renderer;
	}


	/**
	 * Retrieve widget renderer.
	 *
	 * @return `function($form, $template_engine, $widget_conf)`
	 */
	public function getWidgetRenderer($widget_shebang)
	{
		$renderer = @ $this->config['widgets'][$widget_shebang]['renderer'];
		if ($renderer === null) {
			throw new RendererException('Undefined widget renderer: '.$widget_shebang);
		}
		return $renderer;
	}


	/**
	 * Get common renderers for all fields in form. These should be added 
	 * to field-specific renderes of each field. Exact ordering of 
	 * renderers after merge is not specified.
	 */
	public function getFormCommonFieldRenderers()
	{
		return @ $this->config['form']['common_field_renderers'] ? : array();
	}


	/**
	 * Retrieve layout renderer by layout type.
	 *
	 * @return `function($form, $layout_def, $template_engine)`
	 */
	public function getLayoutRenderer($layout_type)
	{
		$renderer = @ $this->config['layouts'][$layout_type]['renderer'];
		if ($renderer === null) {
			throw new RendererException('Undefined layout renderer. Layout type: '.$layout_type);
		}
		return $renderer;
	}


	/**
	 * Retrieve field renderers. Each field has multiple renderers, i.e. 
	 * one for label, another for input or errors.
	 *
	 * @return `array($renderer_name => function($form, $group_id, 
	 *		$field_id, $field_def, $value, $errors, $template_engine)))`
	 */
	public function getFieldRenderers($field_type)
	{
		$renderers = @ $this->config['field_types'][$field_type]['renderers'];
		if ($renderers === null) {
			throw new RendererException('Undefined field renderers. Field type: '.$field_type);
		}
		return $renderers;
	}


	/**
	 * Retrieve field renderer.
	 * 
	 * @return `array($renderer_name => function($form, $group_id, 
	 *		$field_id, $field_def, $value, $errors, $template_engine)))`
	 */
	public function getFieldRenderer($field_type, $renderer_name)
	{
		$renderer = @ $this->config['field_types'][$field_type]['renderers'][$renderer_name];
		if ($renderer === null) {
			$renderer = @ $this->config['form']['common_field_renderers'][$renderer_name];
		}
		if ($renderer === null) {
			throw new RendererException('Undefined field renderer "'.$renderer_name.'". Field type: '.$field_type);
		}
		return $renderer;
	}

}

