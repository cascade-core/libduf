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
 * Render field as thumbnail
 */
class Thumbnail implements \Duf\Renderer\IFieldWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IFieldWidgetRenderer::renderFieldWidget
	public static function renderFieldWidget(\Duf\Form $form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf)
	{
		$values = $form->getViewData($group_id);
		if (empty($values[$field_id])) {
			echo "<span class=\"thumbnail\"></span>\n";
			return;
		}
		$value = $values[$field_id];

		$arg = array(
			$field_id => $value,
		);

		if (parse_url($value, PHP_URL_HOST) == '') {
			$src  = filename_format(isset($widget_conf['image_src'] ) ? $widget_conf['image_src']  : $field_conf['image_src'] , $values, $arg);
			$link = filename_format(isset($widget_conf['image_link']) ? $widget_conf['image_link'] : $field_conf['image_link'], $values, $arg);
		} else {
			$src  = $value;
			$link = isset($widget_conf['image_link']) ? filename_format($widget_conf['image_link'], $values, $arg) : $value;
		}

		if ($link !== null) {
			echo "<a href=\"", htmlspecialchars($link), "\" class=\"thumbnail\">";
		}

		echo "<img class=\"thumbnail\"";
		if (isset($widget_conf['width'])) {
			echo " width=\"", $widget_conf['width'], "\"";
		}
		if (isset($widget_conf['height'])) {
			echo " height=\"", $widget_conf['height'], "\"";
		}
		echo " src=\"", htmlspecialchars($src), "\" alt=\"\">";

		if ($link !== null) {
			echo "</a>";
		}

		echo "\n";
	}

}

