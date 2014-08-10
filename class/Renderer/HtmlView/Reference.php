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
 * Render reference field value.
 */
class Reference extends Input implements \Duf\Renderer\IFieldWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IFieldWidgetRenderer::renderFieldWidget
	public static function renderFieldWidget(\Duf\Form $form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf)
	{
		if (isset($field_conf['link'])) {
			$tag = 'a';
		} else {
			$tag = 'span';
		}

		$raw_values = $form->getRawData($group_id);

		echo "<$tag";

		if (isset($field_conf['link'])) {
			echo " href=\"", htmlspecialchars(filename_format($field_conf['link'], $raw_values)), "\"";
		}

		static::commonAttributes($field_conf);

		echo ">";

		// Value
		echo template_format($field_conf['value_fmt'], $raw_values);

		echo "</$tag>\n";
	}

}

