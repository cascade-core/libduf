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
	use \Duf\Renderer\TagUtils;

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		$group_id = $widget_conf['group_id'];
		$group = $form->getFieldGroup($group_id);
		$dimensions = isset($group['collection_dimensions']) ? (int) $group['collection_dimensions'] : 0;

		$collection_key = array();
		$dimensions = $group['collection_dimensions'];
		$collection = $form->getViewData($group_id);
		$prefix = isset($widget_conf['collection_key_prefix']) ? $form->setCollectionKeyPrefix($group_id, $widget_conf['collection_key_prefix']) : null;

		if (empty($collection)) {
			if (isset($widget_conf['empty_widgets'])) {
				echo "<div class=\"empty_collection\">";
				$form->renderWidgets($template_engine, $widget_conf['empty_widgets']);
				echo "</div>\n";
			}
		} else {
			\Duf\CollectionWalker::walkCollection($collection, $dimensions, $prefix,
				function($collection_key) use ($form, $template_engine, $widget_conf, $group_id, $dimensions) {
					$form->setCollectionKey($group_id, $collection_key);
					if (isset($widget_conf['dimensions'][$dimensions]['element'])) {
						$element = $widget_conf['dimensions'][$dimensions]['element'];
					} else {
						$element = 'div';
					}
					if ($element) {
						echo "<", $element;
						if (isset($widget_conf['dimensions'][$dimensions]['class'])) {
							static::renderClassAttr($form, $template_engine, $widget_conf['dimensions'][$dimensions]['class']);
						}
						echo ">\n";
					}
					$form->renderWidgets($template_engine, $widget_conf['widgets']);
					if ($element) {
						echo "</", $element, ">\n";
					}
				},
				function($depth) use ($form, $template_engine, $widget_conf) {
					if (isset($widget_conf['dimensions'][$depth]['class']) || isset($widget_conf['dimensions'][$depth]['element'])) {
						if (isset($widget_conf['dimensions'][$depth]['element'])) {
							$element = $widget_conf['dimensions'][$depth]['element'];
						} else {
							$element = 'div';
						}
						if ($element) {
							echo "<", $element;
							if (isset($widget_conf['dimensions'][$depth]['class'])) {
								static::renderClassAttr($form, $template_engine, $widget_conf['dimensions'][$depth]['class']);
							}
							echo ">\n";
						}
					}
				},
				function($depth) use ($form, $template_engine, $widget_conf) {
					if (isset($widget_conf['dimensions'][$depth]['class']) || isset($widget_conf['dimensions'][$depth]['element'])) {
						if (isset($widget_conf['dimensions'][$depth]['element'])) {
							$element = $widget_conf['dimensions'][$depth]['element'];
						} else {
							$element = 'div';
						}
						if ($element) {
							echo "</div>\n";
						}
					}
				});
		}
		$form->unsetCollectionKey($group_id);
	}

}

