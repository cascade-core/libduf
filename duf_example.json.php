{
	"_": "<?php printf('_%c%c}%c',34,10,10);__halt_compiler();?>",
	"fields": {
		"contact": {
			"from": {
				"type": "text",
				"label": "Your name"
			},
			"subject": {
				"type": "text",
				"label": "Subject"
			},
			"type": {
				"type": "select",
				"label": "Type",
				"options": [
					"spam",
					"notification",
					"question",
					"warning"
				]
			},
			"body": {
				"type": "textarea",
				"label": "Message"
			}
		},
		"submit": {
			"submit": {
				"type": "submit",
				"label": "Send"
			}
		}
	},
	"layout": {
		"type": "default"
	}
}

