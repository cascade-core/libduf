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
 *
 * TODO: Some caching would be nice.
 */
class Smalldb implements IFieldGroupGenerator
{
	/// Smalldb.
	protected $smalldb;


	/**
	 * Constructor.
	 */
	public function __construct(\Smalldb\StateMachine\AbstractBackend $smalldb)
	{
		$this->smalldb = $smalldb;
	}


	/**
	 * Factory for compatibility with cascade Context.
	 */
	public static function createFromConfig($config, $context, $alias)
	{
		return new self($context->smalldb);
	}


	/// @copydoc IFieldGroupGenerator::updateFieldGroup
	public function updateFieldGroup(& $group_config)
	{
		// Step 1: Generate smalldb machine stuff if machine type is defined
		if (!empty($group_config['machine_type'])) {
			$this->generateFieldGroupFromMachine($group_config['machine_type'], $group_config);
		}

		// Step 2: Process all fields to implement smalldb-specific stuff
		foreach ($group_config['fields'] as $field_id => & $field) {
			$this->postprocessField($field_id, $field);
		}
	}


	/**
	 * Assumes the group represents smalldb machine, so it generates fields
	 * and actions to represent this smalldb state machine.
	 */
	protected function generateFieldGroupFromMachine($machine_type, & $group_config)
	{
		$machine = $this->smalldb->getMachine($machine_type);
		if (!$machine) {
			// Not failing because we want to survive magic templates
			error_msg('Unknown machine type: %s', $machine_type);
			return array();
		}

		$machine_type_url = str_replace('_', '-', $machine_type);

		// Add properties as form fields
		$fields = $machine->describeAllMachineProperties();

		// Add fake state field
		$fields['state'] = array(
			'type' => 'text',
			'computed' => true,
			'readonly' => true,
			'hidden' => true,
		);

		// Generate sorting options
		foreach ($fields as $field_id => & $field)
		if (!isset($field['order_by'])) {
			$field['order_by'] = $field_id;
			if (!isset($field['order_asc'])) {
				$field['order_asc'] = true;
			}
		}

		// Add relations to form fields
		foreach ($machine->describeAllMachineReferences() as $ref_name => $ref) {
			$ref['type'] = 'reference';
			if (isset($fields[$ref_name])) {
				throw new \InvalidArgumentException('Reference name collides with property name: '.$ref_name);
			}

			// FIXME: Shouldn't we use toolbox?
			$smalldb = $this->smalldb;
			$ref['options_value_keys'] = $machine->describeId();
			$ref['options_factory'] = function($field_conf, $value) use ($smalldb, $ref) {
				// FIXME: $field_conf == $ref ... almost. So $ref should be removed.
				$items = array();
				$machine_id_keys = $ref['options_value_keys'];
				if (count($machine_id_keys) != 1) {
					throw new \Exception('Sorry, only simple primary keys are supported.');
				}
				$machine_id_key = reset($machine_id_keys);

				$filters = array(
					'type' => $ref['machine_type'],
					'limit' => false,
				);

				if (isset($ref['filters'])) {
					$filters = array_merge($filters, $ref['filters']);
				}

				if (!empty($ref['lazy_load'])) {
					// Load only selected item, rest will get loaded by Select2 or something like that
					$filters[$machine_id_key] = $value;
				}

				return $smalldb->createListing($filters)->query();
			};

			$fields[$ref_name] = $ref;
		}

		// Merge with overrides
		if (isset($group_config['fields'])) {
			$group_config['fields'] = array_replace_recursive($fields, $group_config['fields']);
		} else {
			$group_config['fields'] = $fields;
		}

		// Stable sort by weight, preserving keys
		$weight_step = 1. / (count($group_config['fields']) + 1);
		$extra_weight = 0;
		foreach ($group_config['fields'] as & $f) {
			if (isset($f['weight'])) {
				$f['weight'] += $extra_weight;
			} else {
				$f['weight'] = 50 + $extra_weight;
			}
			$extra_weight += $weight_step;
		}
		uasort($group_config['fields'], function($a, $b) {
			return $a['weight'] - $b['weight'] > 0 ? 1 : -1;
		});

		// TODO: Add permission check to actions

		// Add actions
		$item_actions = array();
		$collection_actions = array();
		$id_properties = $machine->describeId();
		$id_fmt = join('/', array_map(function($x) { return "{{$x}}"; }, $id_properties));
		foreach ($machine->describeAllMachineActions() as $a => $action) {
			if (empty($action['transitions'])) {
				// Action with no transition -- no button needed
				continue;
			}

			// Build labels
			$label = isset($action['label']) ? $action['label'] : $a;
			$desc = isset($action['description']) ? $action['description'] : null;
			$link = isset($action['link']) ? $action['link'] : null;

			// Collection actions
			if (isset($action['transitions'][''])) {
				$null_ref = $machine->nullRef();
				$collection_actions[$a] = array(
					'label' => $label,
					'description' => $desc,
					'link' => $link !== null ? $link : "/$machine_type_url!$a",
					'hidden' => !empty($action['hidden']),
					'is_allowed_callback' => function($item) use ($machine, $a, $id_properties, $null_ref) {
						return $machine->isTransitionAllowed($null_ref, $a);
					}
				);
				if (count($action['transitions']) == 1) {
					// only collection action, skip items
					continue;
				}
			}

			// Item actions
			$item_actions[$a] = array(
				'label' => $label,
				'description' => $desc,
				'link' => $link !== null ? $link : (($url_fmt = $machine->getUrlFormat()) !== null ? $url_fmt."!$a" : "/$machine_type_url/$id_fmt!$a"),
				'hidden' => !empty($action['hidden']),
				'is_allowed_callback' => function($item) use ($machine, $a, $id_properties) {
					if ($item instanceof \Smalldb\StateMachine\Reference) {
						return $item->machine->isTransitionAllowed($item, $a);
					} else if (empty($item)) {
						return $machine->isTransitionAllowed($machine->nullRef(), $a);
					} else {
						$id = array();
						foreach ($id_properties as $k) {
							$id[] = isset($item[$k]) ? $item[$k] : null;
						}
						/*
						debug_dump($a, 'Checking item action');
						debug_dump($id, 'ID');
						debug_dump($item, 'Item');
						// */
						$ref = $machine->ref($id);
						return $machine->isTransitionAllowed($ref, $a);
					}
				}
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


	/**
	 * Postprocess field to implement Smalldb-specific stuff like selects
	 * from listings.
	 */
	protected function postprocessField($field_id, & $field_conf)
	{
		if (isset($field_conf['options_listing_filters'])) {
			$smalldb = $this->smalldb;

			$field_conf['options_factory'] = function($field_conf, $value) use ($smalldb) {
				$listing = $smalldb->createListing($field_conf['options_listing_filters'])->fetchAll();

				// Key containing the primary key of the listing
				if (isset($field_conf['options_listing_key'])) {
					$key = $field_conf['options_listing_key'];
				} else {
					$key = 'id';
				}

				// Value format
				if (isset($field_conf['options_value_fmt'])) {
					$value_fmt = $field_conf['options_value_fmt'];
				} else {
					$value_fmt = '{name}';
				}

				$options = array();
				foreach ($listing as $item) {
					$options[$item[$key]] = filename_format($value_fmt, $item);
				}
				return $options;
			};
		}
	}

}

