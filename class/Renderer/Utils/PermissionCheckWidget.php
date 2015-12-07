<?php
/*
 * Copyright (c) 2015, Josef Kufner  <jk@frozen-doe.net>
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

namespace Duf\Renderer\Utils;

/**
 * Permission check widget - render set of widgets when permission is granted
 * to user, otherwise render another set of widgets.
 */
class PermissionCheckWidget implements \Duf\Renderer\IWidgetRenderer
{
	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		$group_id = $widget_conf['group_id'];
		$action = $widget_conf['action'];

		$item = $form->getViewData($group_id);
		$actions = $form->getFieldGroupOption($group_id, 'item_actions');

		if ($actions === null) {
			throw new \RuntimeException('Missing field group option "item_actions" in permission check widget. (Is Smalldb field generator in place?)');
		}

		if (!isset($actions[$action])) {
			throw new \InvalidArgumentException('Unknown action: '.$action);
		}

		if (!isset($actions[$action]['is_allowed_callback'])) {
			throw new \RuntimeException('Missing is_allowed_callback in permission check widget. (Is Smalldb field generator in place?)');
		}

		$f = $actions[$action]['is_allowed_callback'];
		$is_allowed = $f($item);

		if ($is_allowed) {
			if (isset($widget_conf['allowed_widgets'])) {
				$form->renderWidgets($template_engine, $widget_conf['allowed_widgets']);
			}
		} else {
			if (isset($widget_conf['denied_widgets'])) {
				$form->renderWidgets($template_engine, $widget_conf['denied_widgets']);
			}
		}
	}

}

