<?php
/*
 * Copyright (c) 2015, Josef Kufner  <jk@frozen-doe.net>
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

namespace Duf\FieldGenerator;

/**
 * Interface to field generator.
 *
 * @see IFieldGroupGenerator
 */
interface IFieldGenerator {

	/**
	 * Update a field.
	 *
	 * @param $field_config contains partial configuration of the field.
	 * 	Generator fills in holes in it. Custom properties in field
	 * 	configuration shall be used by this generator to prepare
	 * 	everything required.
	 * 	
	 * @return FALSE if field cannot be updated.
	 */
	function updateField(& $field_config);

}

