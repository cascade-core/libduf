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
 * Show simple button to perform HTTP POST.
 *
 * TODO: Add slot inside of `<button>`
 */
class B_duf__show_button extends \Cascade\Core\Block {

	protected $inputs = array(
		'link' => null,
		'label' => null,
		'target_form_id' => null,
		'slot' => 'default',
		'slot_weight' => 50,
	);

	protected $outputs = array(
	);

	const force_exec = true;


	public function main()
	{
		$this->templateAdd(null, 'duf/button', array(
			'link' => $this->in('link'),
			'label' => $this->in('label'),
			'hash' => md5($this->in('target_form_id')),	// FIXME
		));
	}
}

