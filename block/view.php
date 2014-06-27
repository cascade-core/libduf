<?php
/*
 * Copyright (c) 2010, Josef Kufner  <jk@frozen-doe.net>
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

/**
 * DUF - Declarative Universal Forms. Simplified block for displaying read-only 
 * forms, which are simple views.
 */

class B_duf__view extends \Cascade\Core\Block {

	protected $inputs = array(
		'form_def' => array(),
		'slot' => 'default',
		'slot_weight' => 50,
		'*' => null,
	);

	protected $outputs = array(
		'done' => true,
	);

	const force_exec = true;


	public final function main()
	{
		$form = new \Duf\Form($this->fullId(), $this->in('form_def'), $this->context->duf_toolbox, \Duf\Form::READ_ONLY);

		$form->setDefaults($this->inAll());
		$form->useDefaults();

		$this->templateAdd(null, 'duf/form', array(
			'form' => $form,
		));

		$this->out('done', true);
	}

}

