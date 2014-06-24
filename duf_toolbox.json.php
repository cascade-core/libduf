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
                "control": "\\Duf\\HtmlFormRenderer::input",
                "error": "\\Duf\\HtmlFormRenderer::error"
            },
            "validators": {
                "html5": "\\Duf\\HtmlFormValidator::input"
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
                "control": "\\Duf\\HtmlFormRenderer::select",
                "error": "\\Duf\\HtmlFormRenderer::error"
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
                "control": "\\Duf\\HtmlFormRenderer::input",
                "error": "\\Duf\\HtmlFormRenderer::error"
            },
            "validators": {
                "html5": "\\Duf\\HtmlFormValidator::input"
            }
        },
        "textarea": {
            "renderers": {
                "label": "\\Duf\\HtmlFormRenderer::label",
                "control": "\\Duf\\HtmlFormRenderer::textarea",
                "error": "\\Duf\\HtmlFormRenderer::error"
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
        "renderer": "\\Duf\\HtmlFormRenderer::form"
    },
    "layouts": {
        "default": {
            "renderer": "\\Duf\\HtmlFormRenderer::layoutDefault"
        }
    }
}
