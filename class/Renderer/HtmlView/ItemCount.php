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
 * Render count of items.
 *
 * This is workaround until texts in JSON configs are translatable with proper plural forms.
 */
class ItemCount extends Input implements \Duf\Renderer\IFieldWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IFieldWidgetRenderer::renderFieldWidget
	public static function renderFieldWidget(\Duf\Form $form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf)
	{
		if (isset($field_conf['link'])) {
			$tag = 'a';
		} else {
			$tag = 'span';
		}

		// Get value early, so class can be set
		$count = $form->getViewData($group_id, $field_id);

		echo "<$tag";

		if (isset($field_conf['link'])) {
			echo " href=\"", htmlspecialchars(filename_format($field_conf['link'], $form->getViewData($group_id))), "\"";
		}

		static::commonAttributes($field_conf);

		echo ">";

		printf(ngettext('<span>%d</span> item', '<span>%d</span> items', $count), $count);

		echo "</$tag>\n";
	}

}

