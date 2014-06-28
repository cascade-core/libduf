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
                    "size": 5,
                    "tabular_hidden": true
                },
                "username": {
                    "type": "text",
                    "label": "Username",
                    "required": true,
                    "placeholder": "Username",
                    "weight": 60,
                    "link_fmt": "/user/{id}"
                },
                "role": {
                    "type": "select",
                    "label": "Role",
                    "placeholder": "Role",
                    "options": {
                        "admin": "Admin",
                        "firma": "Firma"
                    },
                    "weight": 40
                }
            }
        },
        "no_users": {
            "collection_dimensions": 1,
            "fields": {
                "id": {
                    "type": "text",
                    "label": "id",
                    "required": true,
                    "placeholder": "ID",
                    "size": 5,
                    "tabular_hidden": true
                },
                "username": {
                    "type": "text",
                    "label": "Username",
                    "required": true,
                    "placeholder": "Username",
                    "weight": 60,
                    "link_fmt": "/user/{id}"
                },
                "role": {
                    "type": "select",
                    "label": "Role",
                    "placeholder": "Role",
                    "options": {
                        "admin": "Admin",
                        "firma": "Firma"
                    },
                    "weight": 40
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
                "label": "Users in table (automatic configuration)",
                "widgets": [
                    {
                        "#!": "tabular_collection",
                        "group_id": "users",
                        "option_prefix": "tabular",
                        "columns_from_fields": true,
                        "thead": {
                            "hidden": false
                        },
                        "tfoot": {
                            "widgets": [
                                {
                                    "#!": "text",
                                    "text": "Paginator here."
                                }
                            ]
                        },
                        "columns": {
                            "smile": {
                                "label": "Smile",
                                "width": "1",
                                "tbody_widgets": [
                                    {
                                        "#!": "text",
                                        "text": "☺"
                                    }
                                ]
                            },
                            "username": {
                                "label": "Username wich looks like an e-mail"
                            }
                        }
                    }
                ]
            }, {
                "label": "Users in table (automatic configuration; empty collection)",
                "widgets": [
                    {
                        "#!": "tabular_collection",
                        "group_id": "no_users",
                        "option_prefix": "tabular",
                        "columns_from_fields": true,
                        "thead": {
                            "hidden": false
                        },
                        "columns": {
                            "smile": {
                                "label": "Smile",
                                "width": "1",
                                "tbody_widgets": [
                                    {
                                        "#!": "text",
                                        "text": "☺"
                                    }
                                ]
                            },
                            "username": {
                                "label": "Username wich looks like an e-mail"
                            }
                        }
                    }
                ]
            }, {
                "label": "Users in table (manual configuration)",
                "widgets": [
                    {
                        "#!": "tabular_collection",
                        "group_id": "users",
                        "columns_from_fields": false,
                        "columns": {
                            "selection": {
                                "width": "1",
                                "thead_widgets": [
                                ],
                                "tbody_widgets": [
                                    {
                                        "#!": "action_link",
                                        "group_id": "users",
                                        "link_fmt": "/user/{id}!edit",
                                        "widgets": [
                                            {
                                                "#!": "text",
                                                "text": "edit"
                                            }
                                        ]
                                    }
                                ]
                            },
                            "id": {
                                "thead_widgets": [
                                    {
                                        "#!": "text",
                                        "text": "ID"
                                    }
                                ],
                                "tbody_widgets": [
                                    {
                                        "#!": "@view",
                                        "group_id": "users",
                                        "field_id": "id"
                                    }
                                ]
                            },
                            "username": {
                                "width": "60%",
                                "thead_widgets": [
                                    {
                                        "#!": "text",
                                        "text": "User name"
                                    }
                                ],
                                "tbody_widgets": [
                                    {
                                        "#!": "action_link",
                                        "group_id": "users",
                                        "link_fmt": "/user/{id}",
                                        "widgets": [
                                            {
                                                "#!": "@view",
                                                "group_id": "users",
                                                "field_id": "username"
                                            }
                                        ]
                                    }
                                ]
                            },
                            "role": {
                                "thead_widgets": [
                                    {
                                        "#!": "text",
                                        "text": "Role"
                                    }
                                ],
                                "tbody_widgets": [
                                    {
                                        "#!": "@view",
                                        "group_id": "users",
                                        "field_id": "role"
                                    }
                                ]
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

