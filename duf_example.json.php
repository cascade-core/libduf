{
    "_": "<?php printf('_%c%c}%c',34,10,10);__halt_compiler();?>",
    "form": [

    ],
    "fields": {
        "contact": {
            "from": {
                "type": "text",
                "label": "Your name",
                "default": "Melody"
            },
            "subject": {
                "type": "text",
                "label": "Subject",
                "default": "The oldest written message in recorded history"
            },
            "type": {
                "type": "select",
                "label": "Type",
                "options": [
                    "spam",
                    "notification",
                    "question",
                    "warning"
                ],
                "default": 1
            },
            "body": {
                "type": "textarea",
                "label": "Message",
                "default": "Hello sweetie!"
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