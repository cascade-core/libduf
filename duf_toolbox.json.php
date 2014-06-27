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
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input"
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

        },
        "url": {

        },
        "week": {

        }
    },
    "form": {
        "renderers": {
            "@edit": "Duf\\Renderer\\HtmlForm\\Form",
            "@view": "Duf\\Renderer\\HtmlView\\Form"
        },
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
