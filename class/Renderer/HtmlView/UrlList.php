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
 * Render list of URLs
 */
class UrlList extends Input implements \Duf\Renderer\IFieldWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IFieldWidgetRenderer::renderFieldWidget
	public static function renderFieldWidget(\Duf\Form $form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf)
	{
		$value = $form->getViewData($group_id, $field_id);
		if (empty($value)) {
			return;
		}

		echo "<ul";
                static::commonAttributes($field_conf);
		echo ">\n";

		foreach ($value as $url) {
			if ($url[0] != '/' && parse_url($url, PHP_URL_SCHEME) === null) {
				// Fix the link
				$url = 'http://'.$url;
			}
			echo "<li><a href=\"", htmlspecialchars($url), "\">", htmlspecialchars($url), "</a></li>\n";
		}

		echo "</ul>\n";
	}
}

