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
 * Render link to an action of an item. Like ItemActions, but renders only one
 * selected action and ignores 'hidden' option.
 */
class Action extends ItemActions implements \Duf\Renderer\IWidgetRenderer
{

	/// @copydoc \Duf\Renderer\IWidgetRenderer::renderWidget
	public static function renderWidget(\Duf\Form $form, $template_engine, $widget_conf)
	{
		$action_name = $widget_conf['action'];
		$group_id = $widget_conf['group_id'];
		$item = $form->getRawData($group_id);
		$actions = $form->getFieldGroupOption($group_id, 'item_actions');
		$action = $actions[$action_name];

		static::renderAction($action_name, $action, $item);
	}

}
