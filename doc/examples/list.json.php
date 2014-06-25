{
    "_": "<?php printf('_%c%c}%c',34,10,10);__halt_compiler();?>",
    "form": {
    },
    "field_groups": {
        "quest": {
            "collection": false,
            "fields": {
                "title": {
                    "type": "text",
                    "label": "Title",
                    "default": "The Quest",
                    "required": true
                },
                "description": {
                    "type": "text",
                    "label": "Description",
                    "default": "Find the oldest written message in recorded history.",
                    "required": true
                }
            }
        },
        "objectives": {
            "collection": "list",
            "fields": {
                "objective": {
                    "type": "text",
                    "label": "Objective",
                    "required": true
                },
                "contact": {
                    "type": "email",
                    "label": "Contact"
                }
            }
        },
        "submit": {
            "fields": {
                "submit": {
                    "type": "submit",
                    "label": "Send"
                }
            }
        }
    },
    "layout": {
        "type": "default"
    }
}

