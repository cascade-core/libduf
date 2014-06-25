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
                "label": "\\Duf\\HtmlFormRenderer::label",
                "control": "\\Duf\\HtmlFormRenderer::input"
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
                "label": "\\Duf\\HtmlFormRenderer::label",
                "control": "\\Duf\\HtmlFormRenderer::input"
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
                "label": "\\Duf\\HtmlFormRenderer::label",
                "control": "\\Duf\\HtmlFormRenderer::select"
            },
            "validators": {
            }
        },
        "submit": {
            "renderers": {
                "control": "\\Duf\\HtmlFormRenderer::input"
            },
            "validators": {
            }
        },
        "tel": {

        },
        "text": {
            "renderers": {
                "label": "\\Duf\\HtmlFormRenderer::label",
                "control": "\\Duf\\HtmlFormRenderer::input"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\TextInput"
            }
        },
        "textarea": {
            "renderers": {
                "label": "\\Duf\\HtmlFormRenderer::label",
                "control": "\\Duf\\HtmlFormRenderer::textarea"
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
        "renderer": "\\Duf\\HtmlFormRenderer::form",
        "common_field_renderers": {
            "error": "\\Duf\\HtmlFormRenderer::error"
        }
    },
    "layouts": {
        "default": {
            "renderer": "\\Duf\\HtmlFormRenderer::layoutDefault"
        }
    }
}
