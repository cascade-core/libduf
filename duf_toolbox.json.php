{
    "_": "<?php printf('_%c%c}%c',34,10,10);__halt_compiler();?>",
    "field_sources": {
        "smalldb": "\\Duf\\FieldSource\\Smalldb"
    },
    "field_types": {
        "button": {

        },
        "checkbox": {

        },
        "color": {

        },
        "date": {

        },
        "datetime": {

        },
        "datetime-local": {

        },
        "email": {
            "renderers": {
                "@control": "\\Duf\\Renderer\\HtmlForm\\Input"
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

        },
        "number": {

        },
        "password": {
            "renderers": {
                "@control": "\\Duf\\Renderer\\HtmlForm\\Input"
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
                "@control": "\\Duf\\Renderer\\HtmlForm\\Select"
            },
            "validators": {
            }
        },
        "submit": {
            "renderers": {
                "@control": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@label": false
            },
            "validators": {
            }
        },
        "tel": {

        },
        "text": {
            "renderers": {
                "@control": "\\Duf\\Renderer\\HtmlForm\\Input"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\TextInput"
            }
        },
        "textarea": {
            "renderers": {
                "@control": "\\Duf\\Renderer\\HtmlForm\\TextArea"
            },
            "validators": {
            }
        },
        "time": {

        },
        "url": {

        },
        "week": {

        }
    },
    "form": {
        "renderer": "Duf\\Renderer\\HtmlForm\\Form",
        "common_field_renderers": {
            "@label": "\\Duf\\Renderer\\HtmlForm\\Label",
            "@error": "\\Duf\\Renderer\\HtmlForm\\Error"
        }
    },
    "widgets": {
        "default_layout": {
            "renderer": "\\Duf\\Renderer\\HtmlForm\\DefaultLayout"
        },
        "plain_layout": {
            "renderer": "\\Duf\\Renderer\\HtmlForm\\PlainLayout"
        },
        "fieldsets_layout": {
            "renderer": "\\Duf\\Renderer\\HtmlForm\\FieldsetsLayout"
        },
        "plain_collection": {
            "renderer": "\\Duf\\Renderer\\HtmlForm\\PlainCollection"
        }
    }
}
