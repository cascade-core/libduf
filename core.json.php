{
    "_": "<?php printf('_%c%c}%c',34,10,10);__halt_compiler();?>",
    "shebangs": {
        "duf_form": {
            "class": "\\Duf\\Cascade\\FormBlock"
        },
        "duf_view": {
            "class": "\\Duf\\Cascade\\ViewBlock"
        }
    },
    "context": {
        "resources": {
            "duf_toolbox": {
                "class": "\\Duf\\Toolbox",
                "_load_config": "duf_toolbox"
            },
            "duf_smalldb": {
                "factory": [
                    "\\Duf\\FieldGroupGenerator\\Smalldb",
                    "createFromConfig"
                ],
                "_resources": {
                    "smalldb": "smalldb"
                }
            }
        }
    }
}
