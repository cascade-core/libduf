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
 * Render collection using usual widgets, wrapping items in `<div>`s.
 */
class Plain implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		$group_id = $widget_conf['group_id'];
		$group = $form->getFieldGroup($group_id);
		$dimensions = isset($group['collection_dimensions']) ? (int) $group['collection_dimensions'] : 0;

		$items = $form->getRawData($group_id);

		if ($group['collection_dimensions'] != 1) {
			// TODO: Allow more than one dimensions.
			throw new \Exception('Not implemented: Only 1 dimensional collections are supported.');
		}

		$collection_key = array();
		self::walkCollection($form, $template_engine, $widget_conf, $group_id,
			$items, $group['collection_dimensions'], 0, $collection_key);
		$form->unsetCollectionKey($group_id);
	}


	private static function walkCollection($form, $template_engine, $widget_conf, $group_id, $items, $remaining_depth, $depth, & $collection_key)
	{
		if ($remaining_depth > 0) {
			// We need to go deeper ...
			echo "<div";
			if (isset($widget_conf['dimensions'][$depth]['class'])) {
				$class = $widget_conf['dimensions'][$depth]['class'];
				if (is_array($class)) {
					echo " class=\"", htmlspecialchars(join(' ', $class)), "\"";
				} else {
					echo " class=\"", htmlspecialchars($class), "\"";
				}
			}
			echo ">\n";
			foreach($items as $i => $item) {
				$collection_key[$depth] = $i;
				self::walkCollection($form, $template_engine, $widget_conf, $group_id,
					$item, $remaining_depth - 1, $depth + 1, $collection_key);
			}
			echo "</div>\n";
		} else {
			// Deep enough.
			$form->setCollectionKey($group_id, $collection_key);
			echo "<div";
			if (isset($widget_conf['dimensions'][$depth]['class'])) {
				$class = $widget_conf['dimensions'][$depth]['class'];
				if (is_array($class)) {
					echo " class=\"", htmlspecialchars(join(' ', $class)), "\"";
				} else {
					echo " class=\"", htmlspecialchars($class), "\"";
				}
			}
			echo ">\n";

			$form->renderWidgets($template_engine, $widget_conf['widgets']);

			echo "</div>\n";
		}
	}

}

