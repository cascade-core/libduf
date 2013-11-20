;<?php exit(); __HALT_COMPILER; ?>


[block:form]
.block = "duf/form"
.x = 0
.y = 0
form_def[] = "config:duf_example"
form_toolbox[] = "config:duf_toolbox"

[block:show]
.block = "duf/show"
.x = 205
.y = 9
form[] = "form:form"

[block:print_r]
.block = "core/out/print_r"
.x = 208
.y = 165
data[] = "config:duf_example"
title = "Form definition"
slot_weight = 80


; vim:filetype=dosini:
