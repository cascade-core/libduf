--TEST--
Compile form specification
--FILE--
<?php
require(dirname(__FILE__).'/../../../../lib/autoload.php');

$id = 'test_form';

// Create toolbox
$toolbox_config = json_decode(file_get_contents(dirname(__FILE__).'/../duf_toolbox.json.php'), TRUE);
$toolbox = new \Duf\Toolbox($toolbox_config, null);

// Form definition
$form_def = array(
	'field_groups' => array(
		'item' => array(
			"fields" => array(
				"name" => array(
					"type" => "text",
					"label" => "Item name",
				),
			),
		),
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
$compiler = new \Duf\FormCompiler($id, $form_def, $toolbox);

echo $compiler->getSourceCode();

?>
--EXPECT--

