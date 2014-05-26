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

namespace Duf\FieldSource;

/**
 * Interface to field source. Field group may specify field source rather 
 * instead fields, so the fields will can be generated from various metadata to 
 * eliminate duplication. This interface acts as a connector between third 
 * party parts containing metadata and DUF.
 */
interface IFieldSource
{

	/**
	 * Create field group from state machine properties.
	 *
	 * @param $group_config contains full configuration of the field group. 
	 * 	There should be some identifier of entity type or whatever is 
	 * 	relevant to this field source.
	 * @param $context is global context passed from Toolbox. FieldSource 
	 * 	should use only this context to retrieve all it needs to 
	 * 	generate field group.
	 */
	static function generateFieldGroup($group_config, $context);

}

