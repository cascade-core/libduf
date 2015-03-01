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
 * Markdown flavored `<textarea>` field renderer with WMD editor.
 */
class MarkdownArea extends Input implements \Duf\Renderer\IFieldWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IFieldWidgetRenderer::renderFieldWidget
	public static function renderFieldWidget(\Duf\Form $form, $template_engine, $widget_conf, $group_id, $field_id, $field_conf)
	{
		$suffix = $form->getHtmlFieldId($group_id, $field_id);

		echo	"<div id=\"", $suffix, "\" class=\"wmd-editor\" data-wmd-suffix=\"$suffix\">\n",
				"<div class=\"wmd-button-bar-holder\">\n",
					"<div class=\"wmd-button-bar\" id=\"wmd-button-bar-$suffix\"></div>\n",
				"</div>\n",
				"<div class=\"wmd-input-and-preview-holder\">\n",
					"<div class=\"wmd-input-holder\">\n";

		echo "<textarea",
			" id=\"wmd-input-$suffix\"",
			" name=\"", $form->getHtmlFieldName($group_id, $field_id), "\"",
			" tabindex=\"", $form->base_tabindex + (isset($field_conf['tabindex']) ? $field_conf['tabindex'] : 0), "\"";
		static::commonAttributes($field_conf);
		echo ">",
			htmlspecialchars($form->getRawData($group_id, $field_id)),
			"</textarea>";

		echo			"</div>\n",
					"<div class=\"wmd-preview-holder\">\n",
						"<div id=\"wmd-preview-$suffix\" class=\"wmd-preview\"></div>\n",
					"</div>\n",
				"</div>\n",
			"</div>\n";
	}

}

