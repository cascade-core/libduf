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
class FormBlock extends \Cascade\Core\Block
{

	protected $inputs = array(
	);

	protected $outputs = array(
	);

	const force_exec = true;

	protected $config;

	/**
	 * Setup block using configuration from a block storage.
	 */
	public function __construct($config)
	{
		$this->config = $config;

		// setup inputs and outputs
		$this->inputs = array();
		$this->outputs = array(
			'duf_form' => true,
		);
		foreach ($this->config['fields'] as $group => $fields) {
			$this->inputs[$group] = null;
			$this->outputs[$group] = true;
		}
		$this->outputs['done'] = true;
	}


	public function main()
	{
		$form = new \Duf\Form($this->fullId(), $this->config, $this->context->duf_toolbox);

		$form->loadInput();

		if ($form->isSubmitted()) {
			$form->useInput();
		} else {
			$form->loadDefaults();
			$form->setDefaults($this->inAll());
			$form->useDefaults();
		}

		$this->outAll($form->getValues());
		$this->out('duf_form', $form);
		$this->out('done', $form->isSubmitted() && $form->isValid());
	}
}

