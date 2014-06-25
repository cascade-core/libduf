--TEST--
Simple test of all fields in default configuration
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
				// to be generated
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
	'submit' => array(
		'submit' => 'Submit',
	),
);

// Generate all simple fields from toolbox
foreach ($toolbox_config['field_types'] as $type => $type_cfg) {
	if (empty($type_cfg)) {
		// not implemented
		continue;
	}
	
	$form_def['field_groups']['foo']['fields'][$type] = array(
		'type' => $type,
		'label' => 'The '.$type,
	);

	// Field specific fixes
	switch ($type) {
		case 'select':
			for ($i = 1; $i < 5; $i++) {
				$form_def['field_groups']['foo']['fields'][$type]['options'][$i] = "Option $i";
			}
			$form_def['field_groups']['foo']['fields'][$type]['options']['Hello world.'] = "Hello world option";
			break;
	}

	$post_data['foo'][$type] = 'Hello world.';
}

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

line 6 column 1 - Warning: <table> lacks "summary" attribute
line 12 column 1 - Warning: <input> attribute "type" has invalid value "email"

<!DOCTYPE html>
<html>
<head><title></title></head>
<body>
<form id="test.form" class="duf_form" action="" method="post">
<table class="duf_form">
<tr>
<th>
<label for="test.form__foo__email">The email:</label>
</th>
<td>
<input type="email" id="test.form__foo__email" name="foo[email]" value="Hello world.">
<ul class="errors">
<li class="error_field_malformed">Please enter valid e-mail address.</li>
</ul>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__password">The password:</label>
</th>
<td>
<input type="password" id="test.form__foo__password" name="foo[password]" value="Hello world.">
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__select">The select:</label>
</th>
<td>
<select id="test.form__foo__select" name="foo[select]">
<option value="1">Option 1</option>
<option value="2">Option 2</option>
<option value="3">Option 3</option>
<option value="4">Option 4</option>
<option value="Hello world." selected>Hello world option</option>
</select>
</td>
</tr>
<tr>
<th>
</th>
<td>
<input type="submit" id="test.form__foo__submit" name="foo[submit]" value="The submit">
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__text">The text:</label>
</th>
<td>
<input type="text" id="test.form__foo__text" name="foo[text]" value="Hello world.">
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__textarea">The textarea:</label>
</th>
<td>
<textarea id="test.form__foo__textarea" name="foo[textarea]">Hello world.</textarea>
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
