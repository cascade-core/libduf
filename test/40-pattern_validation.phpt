--TEST--
Patter validation
--FILE--
<?php
require(dirname(__FILE__).'/../../../lib/autoload.php');

$id = 'test.form';

// Create toolbox
$toolbox_config = json_decode(file_get_contents(dirname(__FILE__).'/../duf_toolbox.json.php'), TRUE);
$toolbox = new \Duf\Toolbox($toolbox_config, null);

// Form definition
$form_def = array(
	'field_groups' => array(
		'foo' => array(
			'fields' => array(
				'no_pattern' => array(
					'type' => 'text',
					'label' => 'Text (filled)',
				),
				'number_not_rq_fill_good' => array(
					'type' => 'text',
					'label' => 'Number (not rq.; filled; good)',
					'pattern' => '[0-9]+',
				),
				'number_not_rq_fill_bad' => array(
					'type' => 'text',
					'label' => 'Number (not rq.; filled; bad)',
					'pattern' => '[0-9]+',
				),
				'number_not_rq_empty' => array(
					'type' => 'text',
					'label' => 'Number (not rq.; empty)',
					'pattern' => '[0-9]+',
				),
				'number_rq_empty' => array(
					'type' => 'text',
					'label' => 'Number (required; empty)',
					'required' => true,
					'pattern' => '[0-9]+',
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
		'#!' => 'default',
	),
);

// POST data received from a client
$post_data = array(
	'foo' => array(
		'no_pattern' => 'Hello world',
		'number_not_rq_fill_good' => '123',
		'number_not_rq_fill_bad' => 'abc',
		'number_not_rq_empty' => '',
		'number_rq_empty' => '',
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
)

== Field errors ==
array (
  'foo' => 
  array (
    'number_not_rq_fill_bad' => 
    array (
      'field_malformed' => 
      array (
        'message' => 'Field does not match pattern: [0-9]+',
        'pattern' => '[0-9]+',
      ),
    ),
    'number_rq_empty' => 
    array (
      'field_required' => 
      array (
        'message' => 'Please fill this field.',
      ),
    ),
  ),
)

== Output ==
line 6 column 1 - Warning: <table> lacks "summary" attribute
line 20 column 1 - Warning: <input> proprietary attribute "pattern"
line 28 column 1 - Warning: <input> proprietary attribute "pattern"
line 39 column 1 - Warning: <input> proprietary attribute "pattern"
line 47 column 1 - Warning: <input> proprietary attribute "required"
line 47 column 1 - Warning: <input> proprietary attribute "pattern"

<!DOCTYPE html>
<html>
<head><title></title></head>
<body>
<form id="test.form" class="duf_form" action="" method="post">
<table class="duf_form">
<tr>
<th>
<label for="test.form__foo__no_pattern">Text (filled):</label>
</th>
<td>
<input type="text" id="test.form__foo__no_pattern" name="foo[no_pattern]" value="Hello world">
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__number_not_rq_fill_good">Number (not rq.; filled; good):</label>
</th>
<td>
<input type="text" id="test.form__foo__number_not_rq_fill_good" name="foo[number_not_rq_fill_good]" value="123" pattern="[0-9]+">
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__number_not_rq_fill_bad">Number (not rq.; filled; bad):</label>
</th>
<td>
<input type="text" id="test.form__foo__number_not_rq_fill_bad" name="foo[number_not_rq_fill_bad]" value="abc" pattern="[0-9]+">
<ul class="errors">
<li class="error_field_malformed">Field does not match pattern: [0-9]+</li>
</ul>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__number_not_rq_empty">Number (not rq.; empty):</label>
</th>
<td>
<input type="text" id="test.form__foo__number_not_rq_empty" name="foo[number_not_rq_empty]" value="" pattern="[0-9]+">
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__number_rq_empty">Number (required; empty):</label>
</th>
<td>
<input type="text" id="test.form__foo__number_rq_empty" name="foo[number_rq_empty]" value="" required pattern="[0-9]+">
<ul class="errors">
<li class="error_field_required">Please fill this field.</li>
</ul>
</td>
</tr>
<tr>
<th>
</th>
<td>
<input type="submit" id="test.form__submit__submit" name="submit[submit]" value="Submit">
</td>
</tr>
</table>
<input type="hidden" name="__[%d:%d:%x]" value="1">
<!--[if IE]><input type="text" disabled style="display:none!important;" size="1"><![endif]-->
</form>
</body>
</html>

