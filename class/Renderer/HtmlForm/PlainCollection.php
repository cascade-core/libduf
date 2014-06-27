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

namespace Duf\Renderer\HtmlForm;

/**
 * Render collection using usual widgets.
 */
class PlainCollection implements \Duf\Renderer\IWidgetRenderer
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

		foreach($items as $i => $item) {
			$form->setCollectionKey($group_id, $i);
			echo "<div";
			if (isset($widget_conf['class'])) {
				if (is_array($widget_conf['class'])) {
					echo " class=\"", htmlspecialchars(join(' ', $widget_conf['class'])), "\"";
				} else {
					echo " class=\"", htmlspecialchars($widget_conf['class']), "\"";
				}
			}
			echo ">\n";

			$form->renderWidgets($template_engine, $widget_conf['widgets']);

			echo "</div>\n";
		}
		$form->unsetCollectionKey($group_id);

	}

}

