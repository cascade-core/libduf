{
    "_": "<?php printf('_%c%c}%c',34,10,10);__halt_compiler();?>",
    "blocks": {
        "form": {
            "block": "duf/form",
            "x": 0,
            "y": 28,
            "in_con": {
                "form_def": [
                    "config",
                    "duf_example"
                ],
                "form_toolbox": [
                    "config",
                    "duf_toolbox"
                ]
            }
        },
        "show": {
            "block": "duf/show",
            "x": 237,
            "y": 0,
            "in_con": {
                "form": [
                    "form",
                    "form"
                ]
            }
        },
        "print_data": {
            "block": "core/out/print_r",
            "x": 237,
            "y": 125,
            "in_con": {
                "enable": [
                    "form",
                    "done"
                ],
                "data": [
                    "form",
                    "contact"
                ]
            },
            "in_val": {
                "title": "Submitted data"
            }
        },
        "print_def": {
            "block": "core/out/print_r",
            "x": 34,
            "y": 158,
            "in_con": {
                "data": [
                    "config",
                    "duf_example"
                ]
            },
            "in_val": {
                "title": "Form definition",
                "slot_weight": 80
            }
        }
    }
}