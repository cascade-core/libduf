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
 * Post address view
 */
class PostAddress extends Input implements \Duf\Renderer\IFieldWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IFieldWidgetRenderer::renderFieldWidget
	public static function renderFieldWidget(\Duf\Form $form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf)
	{
		$a = $form->getViewData($group_id, $field_id);
		if ($a === null) {
			return;
		}

		echo "<address";

		static::commonAttributes($field_conf);

		echo ">";
		echo "<div>", htmlspecialchars($a['street']), "</div>";
		echo "<div>", htmlspecialchars($a['city']), "</div>";
		echo "<div>", preg_replace('/^([0-9]{3})([0-9]{2})$/', '\1&nbsp;\2', htmlspecialchars($a['postcode'])), ", ",
			htmlspecialchars($a['country']);
		if ($a['country'] == 'CZ' || $a['country'] == 'SK') {
			echo " <span class=\"map\">(<a target=\"_blank\" rel=\"nofollow\" href=\"http://mapy.cz/?q=",
				urlencode($a['street'].', '.$a['city'].', '.$a['postcode']), "\">mapa</a>)</span>";
		}
		echo "</div>";
		echo "</address>\n";
	}

}

