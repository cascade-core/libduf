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

namespace Duf\Renderer\HtmlDecoration;

/**
 * "Add to basket" button with input field for specifying amount.
 *
 * @warning This widget creates `<form>` element, so it can be used only in
 * 	view, not in form.
 */
class AddToBasketWidget implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		$values = $form->getViewData($widget_conf['group_id']);
		$link = template_format($widget_conf['link'], $values);
		$amount_field_name = isset($widget_conf['amount_field_name']) ? $widget_conf['amount_field_name'] : "amount";

		echo "<form class=\"add_to_basket_widget\" method=\"post\" action=\"", htmlspecialchars($link), "\">";
		echo "<input class=\"amount\" required name=\"", htmlspecialchars($amount_field_name), "\" value=\"1\">";
		echo "<input class=\"submit\" type=\"submit\" value=\"", _('Add to basket'), "\">";
		if (isset($widget_conf['hidden_fields'])) {
			foreach ($widget_conf['hidden_fields'] as $name => $value) {
				echo "<input type=\"hidden\" name=\"", htmlspecialchars($name), "\" value=\"", htmlspecialchars(template_format($value, $values)), "\">";
			}
		}
		if (isset($widget_conf['target_form_id'])) {
			echo "<input type=\"hidden\" name=\"__[", $form::createFormToken($widget_conf['target_form_id']), "]\" value=\"1\">";
		}
		echo "</form>\n";
	}

}

