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
        "users": {
            "collection": "list",
            "fields": {
                "username": {
                    "type": "text",
                    "label": "Username",
                    "required": true
                },
                "role": {
                    "type": "text",
                    "label": "Role"
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
        "#!": "fieldsets_layout",
        "fieldsets": [
            {
                "label": "Quest",
                "widgets": [
                    {
                        "#!": "default",
                        "field_group": "quest"
                    }
                ]
            }, {
                "label": "Users",
                "widgets": [
                    {
                        "#!": "default",
                        "field_group": "users"
                    }
                ]
            }
        ]
    }
}

