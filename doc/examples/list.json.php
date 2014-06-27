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
                    "type": "textarea",
                    "label": "Description",
                    "default": "Find the oldest written message in recorded history.",
                    "required": true
                }
            }
        },
        "users": {
            "collection_dimensions": 1,
            "fields": {
                "id": {
                    "type": "text",
                    "label": "id",
                    "required": true,
                    "placeholder": "ID",
                    "size": 5
                },
                "username": {
                    "type": "text",
                    "label": "Username",
                    "required": true,
                    "placeholder": "Username"
                },
                "role": {
                    "type": "select",
                    "label": "Role",
                    "placeholder": "Role",
                    "options": {
                        "admin": "Admin",
                        "firma": "Firma"
                    }
                }
            }
        },
        "submit": {
            "fields": {
                "submit": {
                    "type": "submit",
                    "label": "Submit"
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
                        "#!": "default_layout",
                        "field_group": "quest"
                    }
                ]
            }, {
                "label": "Users",
                "widgets": [
                    {
                        "#!": "plain_collection",
                        "group_id": "users",
                        "dimensions": [
                            {
                                "class": "collection"
                            }, {
                                "class": "item"
                            }
                        ],
                        "widgets": [
                            {
                                "#!": "plain_layout",
                                "rows": [
                                    {
                                        "widgets": [
                                            {
                                                "#!": "@edit",
                                                "group_id": "users",
                                                "field_id": "id"
                                            }, {
                                                "#!": "@edit",
                                                "group_id": "users",
                                                "field_id": "username"
                                            }, {
                                                "#!": "@edit",
                                                "group_id": "users",
                                                "field_id": "role"
                                            }, {
                                                "#!": "@error",
                                                "group_id": "users",
                                                "field_id": "id"
                                            }, {
                                                "#!": "@error",
                                                "group_id": "users",
                                                "field_id": "username"
                                            }, {
                                                "#!": "@error",
                                                "group_id": "users",
                                                "field_id": "role"
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    }
                ]
            }, {
                "label": "Users in table",
                "widgets": [
                    {
                        "#!": "tabular_collection",
                        "group_id": "users",
                        "columns": {
                            "id": {
                                "thead": {
                                    "widgets": [
                                        {
                                            "#!": "text",
                                            "text": "ID"
                                        }
                                    ]
                                },
                                "tbody": {
                                    "widgets": [
                                        {
                                            "#!": "@view",
                                            "group_id": "users",
                                            "field_id": "id"
                                        }
                                    ]
                                }
                            },
                            "username": {
                                "width": "60%",
                                "thead": {
                                    "widgets": [
                                        {
                                            "#!": "text",
                                            "text": "User name"
                                        }
                                    ]
                                },
                                "tbody": {
                                    "widgets": [
                                        {
                                            "#!": "@view",
                                            "group_id": "users",
                                            "field_id": "username"
                                        }
                                    ]
                                }
                            },
                            "role": {
                                "thead": {
                                    "widgets": [
                                        {
                                            "#!": "text",
                                            "text": "Role"
                                        }
                                    ]
                                },
                                "tbody": {
                                    "widgets": [
                                        {
                                            "#!": "@view",
                                            "group_id": "users",
                                            "field_id": "role"
                                        }
                                    ]
                                }
                            }
                        }
                    }
                ]
            }, {
                "class": "submit",
                "widgets": [
                    {
                        "#!": "@edit",
                        "group_id": "submit",
                        "field_id": "submit"
                    }
                ]
            }
        ]
    }
}

