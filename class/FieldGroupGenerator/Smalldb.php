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
		} else {
			$machine_type = $group_config['machine_type'];
		}
		$machine = $context->smalldb->getMachine($machine_type);
		if (!$machine) {
			return array();
		}

		$group_config['fields'] = $machine->describeAllMachineProperties();

		// TODO: Add permission check to actions

		$item_actions = array();
		$collection_actions = array();
		$id_fmt = join('/', array_map(function($x) { return "{{$x}}"; }, $machine->describeId()));
		foreach ($machine->describeAllMachineActions() as $a => $action) {
			if (isset($action['transitions'][''])) {
				$collection_actions[$a] = array(
					'label' => $action['label'],
					'description' => $action['description'],
					'link' => "/$machine_type!$a",
				);
				if (count($action['transitions']) == 1) {
					// only collection action, skip items
					continue;
				}
			}
			$item_actions[$a] = array(
				'label' => $action['label'],
				'description' => $action['description'],
				'link' => "/$machine_type/$id_fmt!$a",
			);
		}

		/*
		debug_dump($machine->describeAllMachineActions(), 'Machine actions');
		debug_dump($collection_actions, 'Collection actions');
		debug_dump($item_actions, 'Item actions');
		// */

		$group_config['collection_actions'] = $collection_actions;
		$group_config['item_actions'] = $item_actions;
	}

}

