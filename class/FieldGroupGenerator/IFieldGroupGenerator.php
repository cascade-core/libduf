<?php
/*
 * Copyright (c) 2014, Josef Kufner  <jk@frozen-doe.net>
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

namespace Duf\FieldGroupGenerator;

/**
 * Interface to field group generator. Field group may specify its generator
 * rather then specifying all details of its configuration. For example fields
 * can be generated from model definition. This interface acts as a connector
 * between third party parts containing metadata and DUF.
 */
interface IFieldGroupGenerator {

	/**
	 * Update a field group.
	 *
	 * @param $group_config contains partial configuration of the field
	 * 	group. Generator fills in holes in it. There should be some
	 * 	identifier of entity type or whatever is relevant to this
	 * 	generator.
	 * @param $context is global context passed from Toolbox. Generator
	 * 	should use only this context to retrieve all it needs to 
	 * 	generate field group.
	 *
	 * @return FALSE if group cannot be updated.
	 */
	static function updateFieldGroup(& $group_config, $context);

}

