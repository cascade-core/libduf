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

namespace Duf\FieldGroupGenerator;

/**
 * Connector to make DUF work with Smalldb.
 */
class Smalldb implements IFieldGroupGenerator
{

	/// @copydoc IFieldGroupGenerator::updateFieldGroup
	static function updateFieldGroup(& $group_config, $context)
	{
		if (empty($group_config['machine_type'])) {
			throw new \InvalidArgumentException('Missing machine_type in field group configuration.');
		}
		$machine = $context->smalldb->getMachine($group_config['machine_type']);
		if (!$machine) {
			return array();
		}

		$group_config['fields'] = $machine->describeAllMachineProperties();
	}

}

