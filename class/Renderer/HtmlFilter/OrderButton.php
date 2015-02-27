<?php
/*
 * Copyright (c) 2015, Josef Kufner  <josef@kufner.cz>
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

namespace Duf\Renderer\HtmlFilter;

/**
 * Simple button to modify filters
 */
class OrderButton extends SimpleButton
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		// Get value early, so class can be set
		$filters = $form->getViewData($widget_conf['group_id']);

		$order_by = $widget_conf['order_by'];
		$order_asc = $widget_conf['order_asc'];
		$effective_order_asc = ($order_by == $filters['order_by']) ? $filters['order_asc'] : null;

		$overrides = array(
			'order_by' => $order_by,
			'order_asc' => $effective_order_asc === null ? $order_asc : ! $effective_order_asc,
		);

		echo "<a href=\"", htmlspecialchars(static::buildFilterLink($filters, $overrides)), "\"";
		static::renderClassAttr($form, $template_engine,
			isset($widget_conf['class']) ? $widget_conf['class'] : null,
			'button filter',
			static::isFilterActive($filters, $overrides) ? 'active' : null);
		echo ">";
		if (isset($widget_conf['widgets'])) {
			$form->renderWidgets($template_engine, $widget_conf['widgets']);
		} else if (isset($widget_conf['label'])) {
			echo htmlspecialchars($widget_conf['label']);
		}

		if ($effective_order_asc !== null) {
			if ($effective_order_asc) {
				echo " <span class=\"asc\">▼</span>";
			} else {
				echo " <span class=\"desc\">▲</span>";
			}
		}
		echo "</a>\n";
	}

}

