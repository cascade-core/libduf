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

namespace Duf\Renderer\HtmlView;

/**
 * Render list of images.
 */
class ImageList extends Input implements \Duf\Renderer\IFieldWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IFieldWidgetRenderer::renderFieldWidget
	public static function renderFieldWidget(\Duf\Form $form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf)
	{
		$values = $form->getViewData($group_id);
		if (empty($values[$field_id])) {
			return;
		}
		$value = $values[$field_id];

		$field_conf['class'] = isset($field_conf['class']) ? (array) $field_conf['class'] : array();
		$field_conf['class'][] = 'images';

		echo "<div";
                static::commonAttributes($field_conf);
		echo ">\n";

		foreach ($value as $filename) {
			$arg = array(
				$field_id => $filename,
			);
			if (parse_url($filename, PHP_URL_HOST) == '') {
				$src  = filename_format($field_conf['image_src'],  $values, $arg);
				$link = filename_format($field_conf['image_link'], $values, $arg);
			} else {
				$src  = $filename;
				$link = $filename;
			}
			echo	"<a href=\"", htmlspecialchars($link), "\">",
				"<img src=\"", htmlspecialchars($src), "\" alt=\"", htmlspecialchars($filename), "\">",
				"</a>",
				"</li>\n";
		}

		echo "</div>\n";
	}
}

