{
    "_": "<?php printf('_%c%c}%c',34,10,10);__halt_compiler();?>",
    "field_group_generators": {
        "smalldb": "\\Duf\\FieldGroupGenerator\\Smalldb"
    },
    "field_types": {
        "button": {

        },
        "checkbox": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\TextInput"
            }
        },
        "color": {

        },
        "date": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\DateInput"
            }
        },
        "datetime": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\DateTimeInput"
            }
        },
        "datetime-local": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\DateTimeInput"
            }
        },
        "email": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\EmailInput"
            }
        },
        "file": {

        },
        "hidden": {

        },
        "htmlarea": {

        },
        "json": {

        },
        "image": {

        },
        "mdarea": {

        },
        "money": {

        },
        "month": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\MonthInput"
            }
        },
        "number": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\NumberInput"
            }
        },
        "password": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\TextInput"
            }
        },
        "radio": {

        },
        "range": {

        },
        "reset": {

        },
        "search": {

        },
        "select": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Select",
                "@view": "\\Duf\\Renderer\\HtmlView\\Select"
            },
            "validators": {
            }
        },
        "radiotabs": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\RadioTabs",
                "@view": false
            },
            "validators": {
            }
        },
        "submit": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": false,
                "@label": false
            },
            "validators": {
            }
        },
        "tel": {

        },
        "text": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\TextInput"
            }
        },
        "textarea": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\TextArea",
                "@view": "\\Duf\\Renderer\\HtmlView\\TextArea"
            },
            "validators": {
            }
        },
        "time": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\TimeInput"
            }
        },
        "url": {

        },
        "week": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\WeekInput"
            }
        }
    },
    "form": {
        "renderers": {
            "@edit": "\\Duf\\Renderer\\HtmlForm\\Form",
            "@view": "\\Duf\\Renderer\\HtmlView\\Form"
        },
        "common_field_renderers": {
            "@label": "\\Duf\\Renderer\\HtmlForm\\Label",
            "@error": "\\Duf\\Renderer\\HtmlForm\\Error"
        }
    },
    "widgets": {
        "default_layout": {
            "renderer": "\\Duf\\Renderer\\HtmlLayout\\DefaultTable"
        },
        "plain_layout": {
            "renderer": "\\Duf\\Renderer\\HtmlLayout\\Plain"
        },
        "fieldsets_layout": {
            "renderer": "\\Duf\\Renderer\\HtmlLayout\\Fieldsets"
        },
        "plain_collection": {
            "renderer": "\\Duf\\Renderer\\HtmlCollection\\Plain"
        },
        "tabular_collection": {
            "renderer": "\\Duf\\Renderer\\HtmlCollection\\Tabular"
        },
        "text": {
            "renderer": "\\Duf\\Renderer\\HtmlDecoration\\Text"
        },
        "selection_checkbox": {
            "renderer": "\\Duf\\Renderer\\HtmlCollection\\SelectionCheckbox"
        },
        "action_link": {
            "renderer": "\\Duf\\Renderer\\HtmlCollection\\ActionLink"
        },
        "switch": {
            "renderer": "\\Duf\\Renderer\\Utils\\SwitchWidget"
        },
        "item_actions": {
            "renderer": "\\Duf\\Renderer\\HtmlDecoration\\ItemActions"
        },
        "collection_actions": {
            "renderer": "\\Duf\\Renderer\\HtmlDecoration\\CollectionActions"
        }
    }
}
