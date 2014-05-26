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
 * Interpreter for Cascade::Core::JsonBlockStorage using hashbang feature.
 */
class FormBlock extends \Cascade\Core\Block implements \Cascade\Core\IHashbangHandler
{

	protected $inputs = array(
	);

	protected $outputs = array(
	);

	const force_exec = true;

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
			$this->inputs[$group] = null;
			$this->outputs[$group] = true;
		}
		$this->outputs['done'] = true;
	}


	/**
	 * Create block proxy.
	 */
	public static function createFromHashbang($block_config, $hashbang_config, \Cascade\Core\Context $context, $block_type)
	{
		$block = new self($block_config, $context);
		return $block;
	}


	public function main()
	{
		$this->form->setId($this->fullId());
		$this->form->loadInput();

		if ($this->form->isSubmitted()) {
			$this->form->useInput();
		} else {
			$this->form->loadDefaults();
			$this->form->setDefaults($this->inAll());
			$this->form->useDefaults();
		}

		$this->outAll($this->form->getValues());
		$this->out('duf_form', $this->form);
		$this->out('done', $this->form->isSubmitted() && $this->form->isValid());
	}
}
