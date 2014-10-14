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

namespace Duf\Cascade;

/**
 * Interpreter for Cascade::Core::JsonBlockStorage using shebang feature.
 */
class FormBlock extends \Cascade\Core\Block implements \Cascade\Core\IShebangHandler
{

	/**
	 * List of inputs and their default values
	 */
	protected $inputs = array(
	);

	/**
	 * List of outputs (no default values)
	 */
	protected $outputs = array(
	);

	/**
	 * Always execute this block.
	 */
	const force_exec = true;


	/**
	 * The Form.
	 */
	protected $form;


	/**
	 * Setup block using configuration from a block storage.
	 */
	public function __construct($config, $context)
	{
		// Create form
		$this->form = new \Duf\Form(null, $config, $context->duf_toolbox);

		// Setup inputs and outputs using form field groups
		$this->inputs = array();
		$this->outputs = array(
			'duf_form' => true,
		);
		foreach ($this->form->getFieldGroups() as $group => $group_config) {
			if (empty($group_config['explode_inputs'])) {
				$this->inputs[$group] = null;
			} else {
				foreach ($group_config['fields'] as $field => $field_conf) {
					$this->inputs[$group.'_'.$field] = null;
				}
			}

			if (empty($group_config['explode_outputs'])) {
				$this->outputs[$group] = true;
			} else {
				foreach ($group_config['fields'] as $field => $field_conf) {
					$this->outputs[$group.'_'.$field] = true;
				}
			}
		}
		$this->inputs['action_url'] = '';
		$this->inputs['target_form_id'] = null;
		$this->inputs['class'] = null;
		$this->outputs['submitted'] = true;
		$this->outputs['done'] = true;
	}


	/**
	 * Create block proxy.
	 */
	public static function createFromShebang($block_config, $shebang_config, \Cascade\Core\Context $context, $block_type)
	{
		$block = new self($block_config, $context);
		return $block;
	}


	/**
	 * Main of the Block.
	 */
	public function main()
	{
		$this->form->setId($this->fullId());
		$this->form->action_url = $this->in('action_url');

		$class = $this->in('class');
		if ($class) {
			$this->form->class = $class;
		}

		$input_values = $this->inAll();
		foreach ($this->form->getFieldGroups() as $group => $group_config) {
			if (!empty($group_config['explode_inputs'])) {
				foreach ($group_config['fields'] as $field => $field_conf) {
					$k = $group.'_'.$field;
					if (isset($input_values[$k])) {
						$input_values[$group][$field] = $input_values[$k];
						unset($input_values[$k]);
					}
				}
			}
		}

		$this->form->setDefaults($input_values);
		$this->form->loadInput();

		$is_submitted = $this->form->isSubmitted();
		$is_valid = $is_submitted && $this->form->isValid();	// Validate only submitted form

		if ($is_submitted) {
			$this->form->useInput();
		} else {
			$this->form->useDefaults();
		}

		if ($is_valid) {
			$values = $this->form->getValues();
			foreach ($this->form->getFieldGroups() as $group => $group_config) {
				if (!empty($group_config['explode_outputs'])) {
					foreach ($group_config['fields'] as $field => $field_conf) {
						if (isset($values[$group][$field])) {
							$values[$group.'_'.$field] = $values[$group][$field];
						}
					}
					unset($values[$group]);
				}
			}
			$this->outAll($values);
		}

		$this->out('duf_form', $this->form);
		$this->out('submitted', $is_submitted);
		$this->out('done', $is_valid);
	}
}

