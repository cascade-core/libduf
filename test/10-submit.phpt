--TEST--
Form with submit button
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
		'submit' => array(
			"fields" => array(
				"submit" => array(
					"type" => "submit",
					"label" => "Submit",
				),
			),
		),
	),
	'layout' => array(
		'type' => 'default',
	),
);

// Create form
$form = new \Duf\Form($id, $form_def, $toolbox);

// POST data received from a client
$post_data = array(
	'submit' => array(
		'submit' => 'Submit',
	),
	'__' => array(
		$form->getToken() => 1,	// make it think it is submitted
	),
);


$form->loadInput($post_data);

echo "Submitted: ", var_export($form->isSubmitted(), true), "\n";
echo "Valid:     ", var_export($form->isValid(), true), "\n";
echo "\n";

print_r($form->getValues());

?>
--EXPECT--
Submitted: true
Valid:     true

Array
(
    [submit] => Array
        (
            [submit] => 1
        )

)

