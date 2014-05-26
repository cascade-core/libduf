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
            }
        },
        "submit": {
            "renderers": {
                "control": "\\Duf\\HtmlFormRenderer::submit"
            }
        },
        "tel": {

        },
        "text": {
            "renderers": {
                "label": "\\Duf\\HtmlFormRenderer::label",
                "control": "\\Duf\\HtmlFormRenderer::input"
            }
        },
        "textarea": {
            "renderers": {
                "label": "\\Duf\\HtmlFormRenderer::label",
                "control": "\\Duf\\HtmlFormRenderer::textarea"
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
