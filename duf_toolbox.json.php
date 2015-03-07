{
    "_": "<?php printf('_%c%c}%c',34,10,10);__halt_compiler();?>",
    "field_group_generators": {
    },
    "field_types": {
        "reference": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Reference",
                "@view": "\\Duf\\Renderer\\HtmlView\\Reference"
            },
            "value_processor": "\\Duf\\FieldValueProcessor\\Reference",
            "validators": {
                "html5": "\\Duf\\FieldValidator\\Reference"
            }
        },
        "button": {

        },
        "checkbox": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
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
                "@view": "\\Duf\\Renderer\\HtmlView\\Input",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\DateInput"
            }
        },
        "datetime": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\DateTimeInput"
            }
        },
        "datetime-local": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\DateTimeInput"
            }
        },
        "email": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
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
                "@view": "\\Duf\\Renderer\\HtmlView\\Input",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\MonthInput"
            }
        },
        "number": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\NumberInput"
            }
        },
        "password": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
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
                "@view": "\\Duf\\Renderer\\HtmlView\\Select",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
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
            "value_processor": "\\Duf\\FieldValueProcessor\\SubmitInput",
            "validators": {
            }
        },
        "tel": {

        },
        "text": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\TextInput"
            }
        },
        "textarea": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\TextArea",
                "@view": "\\Duf\\Renderer\\HtmlView\\TextArea",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "validators": {
            }
        },
        "jsonarea": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\TextArea",
                "@view": "\\Duf\\Renderer\\HtmlView\\JsonDump",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "value_processor": "\\Duf\\FieldValueProcessor\\JsonData",
            "validators": {
            }
        },
        "markdownarea": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\MarkdownArea",
                "@view": "\\Duf\\Renderer\\HtmlView\\MarkdownArea",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "validators": {
            }
        },
        "time": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\TimeInput"
            }
        },
        "url": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input",
                "@thumbnail": "\\Duf\\Renderer\\HtmlView\\Thumbnail",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\UrlInput"
            }
        },
        "relative_url": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input",
                "@thumbnail": "\\Duf\\Renderer\\HtmlView\\Thumbnail",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\TextInput"
            }
        },
        "week": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\Input",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "validators": {
                "html5": "\\Duf\\FieldValidator\\WeekInput"
            }
        },
        "value_list": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\ValueList",
                "@view": "\\Duf\\Renderer\\HtmlView\\ValueList",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "value_processor": "\\Duf\\FieldValueProcessor\\LineList",
            "validators": {
            }
        },
        "url_list": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\UrlList",
                "@view": "\\Duf\\Renderer\\HtmlView\\UrlList",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "value_processor": "\\Duf\\FieldValueProcessor\\LineList",
            "validators": {
            }
        },
        "email_list": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\UrlList",
                "@view": "\\Duf\\Renderer\\HtmlView\\EmailList",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "value_processor": "\\Duf\\FieldValueProcessor\\LineList",
            "validators": {
            }
        },
        "tel_list": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\TelList",
                "@view": "\\Duf\\Renderer\\HtmlView\\ValueList",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "value_processor": "\\Duf\\FieldValueProcessor\\LineList",
            "validators": {
            }
        },
        "post_address": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\PostAddress",
                "@view": "\\Duf\\Renderer\\HtmlView\\PostAddress",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "value_processor": "\\Duf\\FieldValueProcessor\\PostAddress",
            "validators": {
            }
        },
        "item_count": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\Input",
                "@view": "\\Duf\\Renderer\\HtmlView\\ItemCount",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "validators": {
            }
        },
        "image_list": {
            "renderers": {
                "@edit": "\\Duf\\Renderer\\HtmlForm\\UrlList",
                "@view": "\\Duf\\Renderer\\HtmlView\\ImageList",
                "@hidden": "\\Duf\\Renderer\\HtmlForm\\HiddenInput"
            },
            "value_processor": "\\Duf\\FieldValueProcessor\\LineList",
            "validators": {
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
        "null_layout": {
            "renderer": "\\Duf\\Renderer\\HtmlLayout\\NullLayout"
        },
        "default_layout": {
            "renderer": "\\Duf\\Renderer\\HtmlLayout\\DefaultTable"
        },
        "plain_layout": {
            "renderer": "\\Duf\\Renderer\\HtmlLayout\\Plain"
        },
        "fieldsets_layout": {
            "renderer": "\\Duf\\Renderer\\HtmlLayout\\Fieldsets"
        },
        "table_layout": {
            "renderer": "\\Duf\\Renderer\\HtmlLayout\\Table"
        },
        "placeholder": {
            "renderer": "\\Duf\\Renderer\\HtmlDecoration\\Placeholder"
        },
        "html_template": {
            "renderer": "\\Duf\\Renderer\\HtmlLayout\\HtmlTemplate"
        },
        "plain_collection": {
            "renderer": "\\Duf\\Renderer\\HtmlCollection\\Plain"
        },
        "tabular_collection": {
            "renderer": "\\Duf\\Renderer\\HtmlCollection\\Tabular"
        },
        "nested_sets_tree": {
            "renderer": "\\Duf\\Renderer\\HtmlCollection\\NestedSetsTree"
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
        },
        "grouped_list": {
            "renderer": "\\Duf\\Renderer\\HtmlCollection\\GroupedList"
        },
        "heading": {
            "renderer": "\\Duf\\Renderer\\HtmlDecoration\\Heading"
        },
        "slot": {
            "renderer": "\\Duf\\Renderer\\HtmlDecoration\\Slot"
        },
        "price": {
            "renderer": "\\Duf\\Renderer\\HtmlDecoration\\Price"
        },
        "thumbnail": {
            "renderer": "\\Duf\\Renderer\\HtmlDecoration\\Thumbnail"
        },
        "add_to_basket": {
            "renderer": "\\Duf\\Renderer\\HtmlDecoration\\AddToBasketWidget"
        },
        "filter_simple_button": {
            "renderer": "\\Duf\\Renderer\\HtmlFilter\\SimpleButton"
        },
        "filter_active_filters": {
            "renderer": "\\Duf\\Renderer\\HtmlFilter\\ActiveFilters"
        },
        "filter_paginator": {
            "renderer": "\\Duf\\Renderer\\HtmlFilter\\Paginator"
        }
    },
    "content_types": {
        "text/markdown": {
            "renderer": "\\Duf\\Renderer\\Content\\Markdown"
        }
    }
}
