{
	"_": "<?php printf('_%c%c}%c',34,10,10);__halt_compiler();?>",

	"field_types": {
		"button": {},
		"checkbox": {},
		"color": {},
		"date": {},
		"datetime": {},
		"datetime-local": {},
		"email": {},
		"file": {},
		"hidden": {},
		"htmlarea": {},
		"json": {},
		"image": {},
		"mdarea": {},
		"money": {},
		"month": {},
		"number": {},
		"password": {},
		"radio": {},
		"range": {},
		"reset": {},
		"search": {},
		"select": {
			"renderers": {
				"label": "\\Duf\\Renderer::label",
				"control": "\\Duf\\Renderer::select"
			}
		},
		"submit": {
			"renderers": {
				"control": "\\Duf\\Renderer::submit"
			}
		},
		"tel": {},
		"text": {
			"renderers": {
				"label": "\\Duf\\Renderer::label",
				"control": "\\Duf\\Renderer::input"
			}
		},
		"textarea": {
			"renderers": {
				"label": "\\Duf\\Renderer::label",
				"control": "\\Duf\\Renderer::textarea"
			}
		},
		"time": {},
		"url": {},
		"week": {}
	},

	"form": {
		"renderer": "\\Duf\\Renderer::form"
	},

	"layouts": {
		"default": {
			"renderer": "\\Duf\\Renderer::layoutDefault"
		}
	}

}

