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

namespace Duf\Renderer\HtmlCollection;

/**
 * Render tree layout using nested sets.
 */
class NestedSetsTree implements \Duf\Renderer\IWidgetRenderer
{
	use \Duf\Renderer\TagUtils;

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		echo "<ul";
		static::renderClassAttr($form, $template_engine, isset($widget_conf['class']) ? $widget_conf['class'] : 'tree');
		echo ">\n";

		$group_id = $widget_conf['group_id'];
		$group = $form->getFieldGroup($group_id);
		$dimensions = isset($group['collection_dimensions']) ? (int) $group['collection_dimensions'] : 0;
		$depth_key = isset($widget_conf['depth_key']) ? $widget_conf['depth_key'] : 'tree_depth';
		$label_tag = isset($widget_conf['label_tag']) ? $widget_conf['label_tag'] : 'span';

		$collection_key = array();
		$depth = 0;
		\Duf\CollectionWalker::walkCollection($form->getViewData($group_id), $dimensions,
			function($collection_key) use ($form, $template_engine, $widget_conf, $group_id, & $depth, $depth_key, $label_tag) {
				$form->setCollectionKey($group_id, $collection_key);
				$node_depth = $form->getViewData($group_id, $depth_key);

				if ($node_depth > $depth) {
					// Go deeper
					while ($node_depth > $depth) {
						echo "<ul";
						static::renderClassAttr($form, $template_engine,
							isset($widget_conf['node_class']) ? $widget_conf['node_class'] : 'node');
						echo ">\n<li>\n";
						$depth++;
					}
				} else {
					// Go back
					while ($node_depth < $depth) {
						echo "</li>\n</ul>\n";
						$depth--;
					}
					echo "</li>\n<li>";
				}
				echo "<$label_tag>";
				$form->renderWidgets($template_engine, $widget_conf['widgets']);
				echo "</$label_tag>";
			});

		// Go out
		while ($depth + 1 > 0) {
			echo "</li>\n</ul>\n";
			$depth--;
		}
	}

}

