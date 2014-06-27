{
    "_": "<?php printf('_%c%c}%c',34,10,10);__halt_compiler();?>",
    "blocks": {
        "form1": {
            "block": "duf/form",
            "x": 222,
            "y": 224,
            "in_val": {
                "enable": false
            },
            "in_con": {
                "form_def": [
                    "config",
                    "doc/examples/simple"
                ]
            }
        },
        "show1": {
            "block": "duf/show",
            "x": 481,
            "y": 196,
            "in_con": {
                "enable": [
                    "form1",
                    "form"
                ],
                "form": [
                    "form1",
                    "form"
                ]
            },
            "in_val": {
                "slot_weight": "32\n"
            }
        },
        "print_data1": {
            "block": "core/out/print_r",
            "x": 473,
            "y": 320,
            "in_con": {
                "enable": [
                    "form1",
                    "form"
                ],
                "data": [
                    "form1",
                    "contact"
                ]
            },
            "in_val": {
                "title": "Submitted data",
                "header_level": 3,
                "slot_weight": 35
            }
        },
        "form2": {
            "block": "duf/form",
            "x": 236,
            "y": 743,
            "in_con": {
                "form_def": [
                    "config",
                    "doc/examples/list"
                ],
                "users": [
                    "listing2",
                    "list"
                ]
            }
        },
        "show2": {
            "block": "duf/show",
            "x": 475,
            "y": 664,
            "in_con": {
                "enable": [
                    "form2",
                    "form"
                ],
                "form": [
                    "form2",
                    "form"
                ]
            },
            "in_val": {
                "slot_weight": 42
            }
        },
        "print_data2": {
            "block": "core/out/print_r",
            "x": 476,
            "y": 795,
            "in_con": {
                "enable": [
                    "form2",
                    "form"
                ],
                "data": [
                    ":array",
                    "form2",
                    "quest",
                    "form2",
                    "users"
                ]
            },
            "in_val": {
                "title": "Submitted data",
                "header_level": 3,
                "slot_weight": 45
            }
        },
        "header2": {
            "block": "core/out/header",
            "x": 472,
            "y": 466,
            "in_con": {
                "enable": [
                    "form2",
                    "form"
                ]
            },
            "in_val": {
                "level": 2,
                "text": "List Form",
                "slot_weight": 40
            }
        },
        "header1": {
            "block": "core/out/header",
            "x": 476,
            "y": 0,
            "in_con": {
                "enable": [
                    "form1",
                    "form"
                ]
            },
            "in_val": {
                "level": 2,
                "text": "Simple Form",
                "slot_weight": 30
            }
        },
        "listing2": {
            "block": "smalldb/listing",
            "x": 0,
            "y": 766,
            "in_val": {
                "type": "user"
            }
        },
        "print_collection": {
            "block": "core/out/print_r",
            "x": 251,
            "y": 917,
            "in_con": {
                "data": [
                    "listing2",
                    "list"
                ]
            },
            "in_val": {
                "title": "Collection",
                "header_level": 3,
                "slot_weight": 47
            }
        }
    }
}