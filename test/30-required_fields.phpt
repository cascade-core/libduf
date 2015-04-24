--TEST--
Required fields
--FILE--
<?php
require(dirname(__FILE__).'/../../../../lib/autoload.php');

$id = 'test.form';

// Create toolbox
$toolbox_config = json_decode(file_get_contents(dirname(__FILE__).'/../duf_toolbox.json.php'), TRUE);
$toolbox = new \Duf\Toolbox($toolbox_config, null);

// Form definition
$form_def = array(
	'field_groups' => array(
		'foo' => array(
			'fields' => array(
				'rq_text_filled' => array(
					'type' => 'text',
					'label' => 'Required text (filled)',
					'required' => true,
				),
				'rq_text_empty' => array(
					'type' => 'text',
					'label' => 'Required text (empty)',
					'required' => true,
				),
			),
		),
		'submit' => array(
			'fields' => array(
				'submit' => array(
					'type' => 'submit',
					'label' => 'Submit',
				),
			),
		),
	),
	'layout' => array(
		'#!' => 'default_layout',
	),
);

// POST data received from a client
$post_data = array(
	'foo' => array(
		'rq_text_filled' => 'Hello world',
		'rq_text_empty' => '',
	),
	'submit' => array(
		'submit' => 'Submit',
	),
);

// Create form
$form = new \Duf\Form($id, $form_def, $toolbox);

// Make it think it is submitted
$post_data['__'] = array(
	$form->getToken() => 1,
);

$form->loadInput($post_data);

echo "Submitted: ", var_export($form->isSubmitted(), true), "\n";
echo "Valid:     ", var_export($form->isValid(), true), "\n";
echo "\n";

ob_start();
echo "<!DOCTYPE html>\n",
	"<html>\n",
	"<head><title></title></head>\n",
	"<body>\n";
$form->render();
echo "</body>\n</html>\n";
$output = ob_get_clean();

echo "== Form errors ==\n";
var_export($form->form_errors);
echo "\n\n";

echo "== Field errors ==\n";
var_export($form->field_errors);
echo "\n\n";

echo "== Output ==\n";
$tidy = new Tidy();
$tidy->parseString($output);
print_r($tidy->errorBuffer);

echo "\n\n", $output, "\n";

/*
$values = $form->getValues();

if ($values !== $post_data) {
	var_export($values);
} else {
	echo "Values == Post data\n";
}
*/

?>
--EXPECTF--
Submitted: true
Valid:     false

== Form errors ==
array (
  'form_field_error' => 
  array (
    'message' => 'The form is not correctly filled. Please check marked fields.',
  ),
)

== Field errors ==
array (
  'foo' => 
  array (
    'rq_text_empty' => 
    array (
      'field_required' => 
      array (
        'message' => 'Please fill this field.',
      ),
    ),
  ),
)

== Output ==
line 9 column 1 - Warning: <table> lacks "summary" attribute
line 15 column 1 - Warning: <input> proprietary attribute "required"
line 23 column 1 - Warning: <input> proprietary attribute "required"

<!DOCTYPE html>
<html>
<head><title></title></head>
<body>
<form id="test.form" action="" method="post" class=" form">
<ul class="errors">
<li class="error_form_field_error">The form is not correctly filled. Please check marked fields.</li>
</ul>
<table class="form">
<tr>
<th>
<label for="test.form__foo__rq_text_filled">Required text (filled):</label>
</th>
<td>
<input type="text" id="test.form__foo__rq_text_filled" tabindex="100" name="foo[rq_text_filled]" value="Hello world" required>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__rq_text_empty">Required text (empty):</label>
</th>
<td>
<input type="text" id="test.form__foo__rq_text_empty" tabindex="100" name="foo[rq_text_empty]" value="" required>
<ul class="errors">
<li class="error_field_required">Please fill this field.</li>
</ul>
</td>
</tr>
<tr>
<th>
</th>
<td>
<input type="submit" id="test.form__submit__submit" tabindex="100" name="submit[submit]" value="Submit">
</td>
</tr>
</table>
<input type="hidden" name="__[%d:%d:%x]" value="1">
<!--[if IE]><input type="text" disabled style="display:none!important;" size="1"><![endif]-->
</form>
</body>
</html>
