{
    "_": "<?php printf('_%c%c}%c',34,10,10);__halt_compiler();?>",
    "block:form": {
        ".block": "duf/form",
        ".x": 0,
        ".y": 28,
        "form_def": [
            "config:duf_example"
        ],
        "form_toolbox": [
            "config:duf_toolbox"
        ]
    },
    "block:show": {
        ".block": "duf/show",
        ".x": 237,
        ".y": 0,
        "form": [
            "form:form"
        ]
    },
    "block:print_data": {
        ".block": "core/out/print_r",
        ".x": 237,
        ".y": 125,
        "enable": [
            "form:done"
        ],
        "data": [
            "form:contact"
        ],
        "title": "Submitted data"
    },
    "block:print_def": {
        ".block": "core/out/print_r",
        ".x": 34,
        ".y": 158,
        "data": [
            "config:duf_example"
        ],
        "title": "Form definition",
        "slot_weight": 80
    }
}