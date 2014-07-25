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
class ViewBlock extends \Cascade\Core\Block implements \Cascade\Core\IShebangHandler
{

	/// Inputs.
	protected $inputs = array(
	);

	/// Outputs.
	protected $outputs = array(
		'done' => true,
	);

	/// Must be executed (it is view).
	const force_exec = true;


	/// DUF Form prepared in constructor.
	protected $form;


	/**
	 * Setup block using configuration from a block storage.
	 */
	public function __construct($config, $context)
	{
		// Create form
		$this->form = new \Duf\Form(null, $config, $context->duf_toolbox, \Duf\Form::READ_ONLY);

		// Setup inputs and outputs using form field groups
		$this->inputs = array();
		foreach ($this->form->getFieldGroups() as $group => $group_config) {
			$this->inputs[$group] = null;
		}
		$this->inputs['class'] = null;
		$this->inputs['slot'] = 'default';
		$this->inputs['slot_weight'] = 50;
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
		$this->form->id = $this->fullId();
		$this->form->setDefaults($this->inAll());
		$this->form->useDefaults();

		$class = $this->in('class');
		if ($class) {
			$this->form->html_class = $class;
		}

		$this->templateAdd(null, 'duf/form', array(
			'form' => $this->form,
		));

		$this->out('done', true);
	}
}

