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

namespace Duf;

/**
 * Helper to walk multi-dimensional collections.
 */
class CollectionWalker
{

	/**
	 * Walk multi-dimensional collection.
	 *
	 * This method is designed for use with anonymous functions with binded
	 * variables (`use` keyword). Other way would be to pass variables via
	 * method arguments and it means to specify them three times, which is
	 * not very practical.
	 *
	 * Trees are not supported. But if tree nodes are given in a list, it is
	 * possible to indent them manually.
	 *
	 * @note Do not forget to set Form collection key before rendering any child
	 * 	widgets in `$render_function`.
	 *
	 * @par Example
	 * 	To walk the collection from your IWidgetRenderer::renderWidget()
	 * 	method, use code like this:
	 * 	@code
	 *		$group_id = $widget_conf['group_id'];
	 *		$group = $form->getFieldGroup($group_id);
	 *
	 *	 	self::walkCollection($form->getRawData($group_id), $group['collection_dimensions'],
	 *	 		function($collection_key, $item) use ($form, $template_engine, $group_id, $widget_conf) {
	 *				$form->setCollectionKey($group_id, $collection_key);
	 *	 		      	$form->renderWidgets($template_engine, $widget_conf['widgets']);
	 *	 		},
	 *	 		function($depth) use () {
	 *	 		      	echo "<div>\n";
	 *	 		},
	 *	 		function($depth) use () {
	 *	 			echo "</div>\n";
	 *	 		});
	 *		$form->unsetCollectionKey($group_id);
	 *	@endcode
	 *
	 * @see Form::getRawData(), Form::setCollectionKey(), Form::unsetCollectionKey()
	 *
	 * @param $collection is the collection to walk.
	 * @param $dimension is amount of dimensions to traverse (0 = single item, 1 = list, 2 = matrix, ...).
	 * @param $collection_prefix is a list of keys to walk into collection before walking starts.
	 * @param $render_function is `function($collection_key, $item)` called for each item in collection.
	 * @param $on_enter is `function($depth)` called when going deeper.
	 * @param $on_leave is `function($depth)` called when going back.
	 * @param $depth is for internal use, leave it unspecified.
	 * @param $collection_key is for internal use, leave it unspecified.
	 */
	public static function walkCollection($collection, $dimension, $collection_prefix, $render_function, $on_enter = null, $on_leave = null,
		$depth = 0, & $collection_key = null)
	{
		if ($collection_prefix !== null) {
			// Strip prefixed dimensions from collection
			if (count($collection_prefix) == 1) {
				$key = reset($collection_prefix);
				if (isset ($collection[$key])) {
					$collection = $collection[$key];
					$dimension--;
				} else {
					return;
				}
			} else {
				throw new \Exception('Not implemented: $collection_prefix can have only one key to prefix. Sorry.');
			}
		}
		if ($dimension > 0) {
			// We need to go deeper ...
			if ($collection_key === null) {
				$collection_key = array();
			}
			if (empty($collection)) {
				return;
			}
			if ($on_enter !== null) {
				$on_enter($depth);
			}
			foreach($collection as $i => $sub_collection) {
				$collection_key[$depth] = $i;
				self::walkCollection($sub_collection, $dimension - 1, null, $render_function, $on_enter, $on_leave, $depth + 1, $collection_key);
			}
			if ($on_leave !== null) {
				$on_leave($depth);
			}
		} else {
			// Deep enough.
			$render_function($collection_key, $collection);
		}
	}


	/**
	 * Walk multi-dimensional collection and keep reference to parallel collection.
	 *
	 * Just like walkCollection(), but `$target` parameter is walked in
	 * parallel, which means the `$target_item` reference points to the same
	 * position as `$item`, but in `$target` structure.
	 *
	 * @param $collection is the collection to walk.
	 * @param $dimension is amount of dimensions to traverse (0 = single item, 1 = list, 2 = matrix, ...).
	 * @param $render_function is `function($collection_key, $item, $target_item)` called for each item in collection.
	 * @param $on_enter is `function($depth)` called when going deeper.
	 * @param $on_leave is `function($depth)` called when going back.
	 * @param $depth is for internal use, leave it unspecified.
	 * @param $collection_key is for internal use, leave it unspecified.
	 *
	 * @see walkCollection()
	 */
	public static function walkCollectionWithTarget($collection, & $target, $dimension, $render_function, $on_enter = null, $on_leave = null,
		$depth = 0, & $collection_key = null)
	{
		if ($dimension > 0) {
			// We need to go deeper ...
			if ($collection_key === null) {
				$collection_key = array();
			}
			if ($on_enter !== null) {
				$on_enter($depth);
			}
			foreach($collection as $i => $sub_collection) {
				$collection_key[$depth] = $i;
				if (!isset($target[$i])) {
					$target[$i] = array();
				}
				self::walkCollectionWithTarget($sub_collection, $target[$i], $dimension - 1,
					$render_function, $on_enter, $on_leave, $depth + 1, $collection_key);
			}
			if ($on_leave !== null) {
				$on_leave($depth);
			}
		} else {
			// Deep enough.
			$render_function($collection_key, $collection, $target);
		}
	}

}

