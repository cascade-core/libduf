--TEST--
Simple test of all fields in default configuration
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
		'#!' => 'default_layout',
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

	if ($type == 'reference') {
		// Skip generated field
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

		case 'radiotabs':
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

line 262 column 25 - Warning: replacing invalid character code 133
line 9 column 1 - Warning: <table> lacks "summary" attribute
line 23 column 1 - Warning: <input> attribute "type" has invalid value "date"
line 34 column 1 - Warning: <input> attribute "type" has invalid value "datetime-local"
line 45 column 1 - Warning: <input> attribute "type" has invalid value "datetime-local"
line 56 column 1 - Warning: <input> attribute "type" has invalid value "email"
line 67 column 1 - Warning: <input> attribute "type" has invalid value "month"
line 78 column 1 - Warning: <input> attribute "type" has invalid value "number"
line 156 column 1 - Warning: <div> proprietary attribute "data-wmd-suffix"
line 176 column 1 - Warning: <input> attribute "type" has invalid value "time"
line 187 column 1 - Warning: <input> attribute "type" has invalid value "url"
line 206 column 1 - Warning: <input> attribute "type" has invalid value "week"
line 257 column 44 - Warning: <input> proprietary attribute "placeholder"
line 258 column 1 - Warning: <input> proprietary attribute "placeholder"
line 259 column 1 - Warning: <input> proprietary attribute "placeholder"
line 519 column 1 - Warning: <input> attribute "type" has invalid value "item_count"
line 98 column 1 - Warning: trimming empty <option>

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
<label for="test.form__foo__checkbox">The checkbox:</label>
</th>
<td>
<input type="hidden" name="foo[checkbox]" value="0"><input type="checkbox" id="test.form__foo__checkbox" tabindex="100" name="foo[checkbox]" value="1"checked >
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__date">The date:</label>
</th>
<td>
<input type="date" id="test.form__foo__date" tabindex="100" name="foo[date]" value="1970-01-01">
<ul class="errors">
<li class="error_field_malformed">Please use &quot;YYYY-MM-DD&quot; format (ISO 8601, date only).</li>
</ul>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__datetime">The datetime:</label>
</th>
<td>
<input type="datetime-local" id="test.form__foo__datetime" tabindex="100" name="foo[datetime]" value="1970-01-01T01:00:00">
<ul class="errors">
<li class="error_field_malformed">Please use &quot;YYYY-MM-DD HH:MM:SS&quot; format (ISO 8601).</li>
</ul>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__datetime-local">The datetime-local:</label>
</th>
<td>
<input type="datetime-local" id="test.form__foo__datetime-local" tabindex="100" name="foo[datetime-local]" value="1970-01-01T01:00:00">
<ul class="errors">
<li class="error_field_malformed">Please use &quot;YYYY-MM-DD HH:MM:SS&quot; format (ISO 8601).</li>
</ul>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__email">The email:</label>
</th>
<td>
<input type="email" id="test.form__foo__email" tabindex="100" name="foo[email]" value="Hello world.">
<ul class="errors">
<li class="error_field_malformed">Please enter valid e-mail address.</li>
</ul>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__month">The month:</label>
</th>
<td>
<input type="month" id="test.form__foo__month" tabindex="100" name="foo[month]" value="Hello world.">
<ul class="errors">
<li class="error_field_malformed">Please use &quot;YYYY-MM&quot; format (ISO 8601, year and month).</li>
</ul>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__number">The number:</label>
</th>
<td>
<input type="number" id="test.form__foo__number" tabindex="100" name="foo[number]" value="Hello world.">
<ul class="errors">
<li class="error_field_malformed">Please enter number.</li>
</ul>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__password">The password:</label>
</th>
<td>
<input type="password" id="test.form__foo__password" tabindex="100" name="foo[password]" value="Hello world.">
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__select">The select:</label>
</th>
<td>
<select id="test.form__foo__select" name="foo[select]" tabindex="100">
<option value=""></option>
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
<label for="test.form__foo__radiotabs">The radiotabs:</label>
</th>
<td>
<div class="radiotabs" id="test.form__foo__radiotabs">

Notice: Undefined index: tabs in /home/pepik/work/interaction4u/hortiflora/work/lib/cascade/libduf/class/Renderer/HtmlForm/RadioTabs.php on line 48

Warning: Invalid argument supplied for foreach() in /home/pepik/work/interaction4u/hortiflora/work/lib/cascade/libduf/class/Renderer/HtmlForm/RadioTabs.php on line 48
</div>
</td>
</tr>
<tr>
<th>
</th>
<td>
<input type="submit" id="test.form__foo__submit" tabindex="100" name="foo[submit]" value="The submit">
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__text">The text:</label>
</th>
<td>
<input type="text" id="test.form__foo__text" tabindex="100" name="foo[text]" value="Hello world.">
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__textarea">The textarea:</label>
</th>
<td>
<textarea id="test.form__foo__textarea" name="foo[textarea]" tabindex="100">Hello world.</textarea>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__jsonarea">The jsonarea:</label>
</th>
<td>
<textarea id="test.form__foo__jsonarea" name="foo[jsonarea]" tabindex="100">Hello world.</textarea>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__markdownarea">The markdownarea:</label>
</th>
<td>
<div id="test.form__foo__markdownarea" class="wmd-editor" data-wmd-suffix="test.form__foo__markdownarea">
<div class="wmd-button-bar-holder">
<div class="wmd-button-bar" id="wmd-button-bar-test.form__foo__markdownarea"></div>
</div>
<div class="wmd-input-and-preview-holder">
<div class="wmd-input-holder">
<textarea id="wmd-input-test.form__foo__markdownarea" name="foo[markdownarea]" tabindex="100">Hello world.</textarea></div>
<div class="wmd-preview-holder">
<div class="wmd-preview-label">Preview</div>
<div id="wmd-preview-test.form__foo__markdownarea" class="wmd-preview"></div>
</div>
</div>
</div>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__time">The time:</label>
</th>
<td>
<input type="time" id="test.form__foo__time" tabindex="100" name="foo[time]" value="01:00:00">
<ul class="errors">
<li class="error_field_malformed">Please use &quot;HH:MM:SS&quot; format (ISO 8601, time only).</li>
</ul>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__url">The url:</label>
</th>
<td>
<input type="url" id="test.form__foo__url" tabindex="100" name="foo[url]" value="Hello world.">
<ul class="errors">
<li class="error_field_malformed">Please enter valid URL.</li>
</ul>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__relative_url">The relative_url:</label>
</th>
<td>
<input type="text" id="test.form__foo__relative_url" tabindex="100" name="foo[relative_url]" value="Hello world.">
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__week">The week:</label>
</th>
<td>
<input type="week" id="test.form__foo__week" tabindex="100" name="foo[week]" value="Hello world.">
<ul class="errors">
<li class="error_field_malformed">Please use &quot;YYYY-Ww&quot; format (e.g. &quot;2015-W3&quot;; ISO 8601, year and week).</li>
</ul>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__value_list">The value_list:</label>
</th>
<td>
<div><textarea id="test.form__foo__value_list" name="foo[value_list]" tabindex="100">Hello world.</textarea>
<div class="textarea_note">Enter list of values, each value on its own line.</div>
</div>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__url_list">The url_list:</label>
</th>
<td>
<div><textarea id="test.form__foo__url_list" name="foo[url_list]" tabindex="100">Hello world.</textarea>
<div class="textarea_note">Enter list of addresses, each address on its own line.</div>
</div>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__email_list">The email_list:</label>
</th>
<td>
<div><textarea id="test.form__foo__email_list" name="foo[email_list]" tabindex="100">Hello world.</textarea>
<div class="textarea_note">Enter list of addresses, each address on its own line.</div>
</div>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__tel_list">The tel_list:</label>
</th>
<td>
<div><textarea id="test.form__foo__tel_list" name="foo[tel_list]" tabindex="100">Hello world.</textarea>
<div class="textarea_note">Enter list of phone numbers including international prefix, each number on its own line.</div>
</div>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__post_address">The post_address:</label>
</th>
<td>
<address id="test.form__foo__post_address"><input class="addr_street"   name="foo[post_address][street]"   value="" placeholder="Street" title="Street">
<input class="addr_city"     name="foo[post_address][city]"     value="" placeholder="City" title="City">
<input class="addr_postcode" name="foo[post_address][postcode]" value="" placeholder="Post code" title="Post code">
<select class="addr_country"  name="foo[post_address][country]" title="Country">
<option value="AF">AF: Afghanistan</option>
<option value="AX">AX: Åland Islands</option>
<option value="AL">AL: Albania</option>
<option value="DZ">DZ: Algeria</option>
<option value="AS">AS: American Samoa</option>
<option value="AD">AD: Andorra</option>
<option value="AO">AO: Angola</option>
<option value="AI">AI: Anguilla</option>
<option value="AQ">AQ: Antarctica</option>
<option value="AG">AG: Antigua and Barbuda</option>
<option value="AR">AR: Argentina</option>
<option value="AM">AM: Armenia</option>
<option value="AW">AW: Aruba</option>
<option value="AU">AU: Australia</option>
<option value="AT">AT: Austria</option>
<option value="AZ">AZ: Azerbaijan</option>
<option value="BS">BS: Bahamas</option>
<option value="BH">BH: Bahrain</option>
<option value="BD">BD: Bangladesh</option>
<option value="BB">BB: Barbados</option>
<option value="BY">BY: Belarus</option>
<option value="BE">BE: Belgium</option>
<option value="BZ">BZ: Belize</option>
<option value="BJ">BJ: Benin</option>
<option value="BM">BM: Bermuda</option>
<option value="BT">BT: Bhutan</option>
<option value="BO">BO: Bolivia, Plurinational State of</option>
<option value="BQ">BQ: Bonaire, Sint Eustatius and Saba</option>
<option value="BA">BA: Bosnia and Herzegovina</option>
<option value="BW">BW: Botswana</option>
<option value="BV">BV: Bouvet Island</option>
<option value="BR">BR: Brazil</option>
<option value="IO">IO: British Indian Ocean Territory</option>
<option value="BN">BN: Brunei Darussalam</option>
<option value="BG">BG: Bulgaria</option>
<option value="BF">BF: Burkina Faso</option>
<option value="BI">BI: Burundi</option>
<option value="KH">KH: Cambodia</option>
<option value="CM">CM: Cameroon</option>
<option value="CA">CA: Canada</option>
<option value="CV">CV: Cabo Verde</option>
<option value="KY">KY: Cayman Islands</option>
<option value="CF">CF: Central African Republic</option>
<option value="TD">TD: Chad</option>
<option value="CL">CL: Chile</option>
<option value="CN">CN: China</option>
<option value="CX">CX: Christmas Island</option>
<option value="CC">CC: Cocos (Keeling) Islands</option>
<option value="CO">CO: Colombia</option>
<option value="KM">KM: Comoros</option>
<option value="CG">CG: Congo</option>
<option value="CD">CD: Congo, the Democratic Republic of the</option>
<option value="CK">CK: Cook Islands</option>
<option value="CR">CR: Costa Rica</option>
<option value="CI">CI: Côte d'Ivoire</option>
<option value="HR">HR: Croatia</option>
<option value="CU">CU: Cuba</option>
<option value="CW">CW: Curaçao</option>
<option value="CY">CY: Cyprus</option>
<option value="CZ" selected>CZ: Czech Republic</option>
<option value="DK">DK: Denmark</option>
<option value="DJ">DJ: Djibouti</option>
<option value="DM">DM: Dominica</option>
<option value="DO">DO: Dominican Republic</option>
<option value="EC">EC: Ecuador</option>
<option value="EG">EG: Egypt</option>
<option value="SV">SV: El Salvador</option>
<option value="GQ">GQ: Equatorial Guinea</option>
<option value="ER">ER: Eritrea</option>
<option value="EE">EE: Estonia</option>
<option value="ET">ET: Ethiopia</option>
<option value="FK">FK: Falkland Islands (Malvinas)</option>
<option value="FO">FO: Faroe Islands</option>
<option value="FJ">FJ: Fiji</option>
<option value="FI">FI: Finland</option>
<option value="FR">FR: France</option>
<option value="GF">GF: French Guiana</option>
<option value="PF">PF: French Polynesia</option>
<option value="TF">TF: French Southern Territories</option>
<option value="GA">GA: Gabon</option>
<option value="GM">GM: Gambia</option>
<option value="GE">GE: Georgia</option>
<option value="DE">DE: Germany</option>
<option value="GH">GH: Ghana</option>
<option value="GI">GI: Gibraltar</option>
<option value="GR">GR: Greece</option>
<option value="GL">GL: Greenland</option>
<option value="GD">GD: Grenada</option>
<option value="GP">GP: Guadeloupe</option>
<option value="GU">GU: Guam</option>
<option value="GT">GT: Guatemala</option>
<option value="GG">GG: Guernsey</option>
<option value="GN">GN: Guinea</option>
<option value="GW">GW: Guinea-Bissau</option>
<option value="GY">GY: Guyana</option>
<option value="HT">HT: Haiti</option>
<option value="HM">HM: Heard Island and McDonald Islands</option>
<option value="VA">VA: Holy See (Vatican City State)</option>
<option value="HN">HN: Honduras</option>
<option value="HK">HK: Hong Kong</option>
<option value="HU">HU: Hungary</option>
<option value="IS">IS: Iceland</option>
<option value="IN">IN: India</option>
<option value="ID">ID: Indonesia</option>
<option value="IR">IR: Iran, Islamic Republic of</option>
<option value="IQ">IQ: Iraq</option>
<option value="IE">IE: Ireland</option>
<option value="IM">IM: Isle of Man</option>
<option value="IL">IL: Israel</option>
<option value="IT">IT: Italy</option>
<option value="JM">JM: Jamaica</option>
<option value="JP">JP: Japan</option>
<option value="JE">JE: Jersey</option>
<option value="JO">JO: Jordan</option>
<option value="KZ">KZ: Kazakhstan</option>
<option value="KE">KE: Kenya</option>
<option value="KI">KI: Kiribati</option>
<option value="KP">KP: Korea, Democratic People's Republic of</option>
<option value="KR">KR: Korea, Republic of</option>
<option value="KW">KW: Kuwait</option>
<option value="KG">KG: Kyrgyzstan</option>
<option value="LA">LA: Lao People's Democratic Republic</option>
<option value="LV">LV: Latvia</option>
<option value="LB">LB: Lebanon</option>
<option value="LS">LS: Lesotho</option>
<option value="LR">LR: Liberia</option>
<option value="LY">LY: Libya</option>
<option value="LI">LI: Liechtenstein</option>
<option value="LT">LT: Lithuania</option>
<option value="LU">LU: Luxembourg</option>
<option value="MO">MO: Macao</option>
<option value="MK">MK: Macedonia, the former Yugoslav Republic of</option>
<option value="MG">MG: Madagascar</option>
<option value="MW">MW: Malawi</option>
<option value="MY">MY: Malaysia</option>
<option value="MV">MV: Maldives</option>
<option value="ML">ML: Mali</option>
<option value="MT">MT: Malta</option>
<option value="MH">MH: Marshall Islands</option>
<option value="MQ">MQ: Martinique</option>
<option value="MR">MR: Mauritania</option>
<option value="MU">MU: Mauritius</option>
<option value="YT">YT: Mayotte</option>
<option value="MX">MX: Mexico</option>
<option value="FM">FM: Micronesia, Federated States of</option>
<option value="MD">MD: Moldova, Republic of</option>
<option value="MC">MC: Monaco</option>
<option value="MN">MN: Mongolia</option>
<option value="ME">ME: Montenegro</option>
<option value="MS">MS: Montserrat</option>
<option value="MA">MA: Morocco</option>
<option value="MZ">MZ: Mozambique</option>
<option value="MM">MM: Myanmar</option>
<option value="NA">NA: Namibia</option>
<option value="NR">NR: Nauru</option>
<option value="NP">NP: Nepal</option>
<option value="NL">NL: Netherlands</option>
<option value="NC">NC: New Caledonia</option>
<option value="NZ">NZ: New Zealand</option>
<option value="NI">NI: Nicaragua</option>
<option value="NE">NE: Niger</option>
<option value="NG">NG: Nigeria</option>
<option value="NU">NU: Niue</option>
<option value="NF">NF: Norfolk Island</option>
<option value="MP">MP: Northern Mariana Islands</option>
<option value="NO">NO: Norway</option>
<option value="OM">OM: Oman</option>
<option value="PK">PK: Pakistan</option>
<option value="PW">PW: Palau</option>
<option value="PS">PS: Palestine, State of</option>
<option value="PA">PA: Panama</option>
<option value="PG">PG: Papua New Guinea</option>
<option value="PY">PY: Paraguay</option>
<option value="PE">PE: Peru</option>
<option value="PH">PH: Philippines</option>
<option value="PN">PN: Pitcairn</option>
<option value="PL">PL: Poland</option>
<option value="PT">PT: Portugal</option>
<option value="PR">PR: Puerto Rico</option>
<option value="QA">QA: Qatar</option>
<option value="RE">RE: Réunion</option>
<option value="RO">RO: Romania</option>
<option value="RU">RU: Russian Federation</option>
<option value="RW">RW: Rwanda</option>
<option value="BL">BL: Saint Barthélemy</option>
<option value="SH">SH: Saint Helena, Ascension and Tristan da Cunha</option>
<option value="KN">KN: Saint Kitts and Nevis</option>
<option value="LC">LC: Saint Lucia</option>
<option value="MF">MF: Saint Martin (French part)</option>
<option value="PM">PM: Saint Pierre and Miquelon</option>
<option value="VC">VC: Saint Vincent and the Grenadines</option>
<option value="WS">WS: Samoa</option>
<option value="SM">SM: San Marino</option>
<option value="ST">ST: Sao Tome and Principe</option>
<option value="SA">SA: Saudi Arabia</option>
<option value="SN">SN: Senegal</option>
<option value="RS">RS: Serbia</option>
<option value="SC">SC: Seychelles</option>
<option value="SL">SL: Sierra Leone</option>
<option value="SG">SG: Singapore</option>
<option value="SX">SX: Sint Maarten (Dutch part)</option>
<option value="SK">SK: Slovakia</option>
<option value="SI">SI: Slovenia</option>
<option value="SB">SB: Solomon Islands</option>
<option value="SO">SO: Somalia</option>
<option value="ZA">ZA: South Africa</option>
<option value="GS">GS: South Georgia and the South Sandwich Islands</option>
<option value="SS">SS: South Sudan</option>
<option value="ES">ES: Spain</option>
<option value="LK">LK: Sri Lanka</option>
<option value="SD">SD: Sudan</option>
<option value="SR">SR: Suriname</option>
<option value="SJ">SJ: Svalbard and Jan Mayen</option>
<option value="SZ">SZ: Swaziland</option>
<option value="SE">SE: Sweden</option>
<option value="CH">CH: Switzerland</option>
<option value="SY">SY: Syrian Arab Republic</option>
<option value="TW">TW: Taiwan, Province of China</option>
<option value="TJ">TJ: Tajikistan</option>
<option value="TZ">TZ: Tanzania, United Republic of</option>
<option value="TH">TH: Thailand</option>
<option value="TL">TL: Timor-Leste</option>
<option value="TG">TG: Togo</option>
<option value="TK">TK: Tokelau</option>
<option value="TO">TO: Tonga</option>
<option value="TT">TT: Trinidad and Tobago</option>
<option value="TN">TN: Tunisia</option>
<option value="TR">TR: Turkey</option>
<option value="TM">TM: Turkmenistan</option>
<option value="TC">TC: Turks and Caicos Islands</option>
<option value="TV">TV: Tuvalu</option>
<option value="UG">UG: Uganda</option>
<option value="UA">UA: Ukraine</option>
<option value="AE">AE: United Arab Emirates</option>
<option value="GB">GB: United Kingdom</option>
<option value="US">US: United States</option>
<option value="UM">UM: United States Minor Outlying Islands</option>
<option value="UY">UY: Uruguay</option>
<option value="UZ">UZ: Uzbekistan</option>
<option value="VU">VU: Vanuatu</option>
<option value="VE">VE: Venezuela, Bolivarian Republic of</option>
<option value="VN">VN: Viet Nam</option>
<option value="VG">VG: Virgin Islands, British</option>
<option value="VI">VI: Virgin Islands, U.S.</option>
<option value="WF">WF: Wallis and Futuna</option>
<option value="EH">EH: Western Sahara</option>
<option value="YE">YE: Yemen</option>
<option value="ZM">ZM: Zambia</option>
<option value="ZW">ZW: Zimbabwe</option>
</select>
</address>
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__item_count">The item_count:</label>
</th>
<td>
<input type="item_count" id="test.form__foo__item_count" tabindex="100" name="foo[item_count]" value="Hello world.">
</td>
</tr>
<tr>
<th>
<label for="test.form__foo__image_list">The image_list:</label>
</th>
<td>
<div><textarea id="test.form__foo__image_list" name="foo[image_list]" tabindex="100">Hello world.</textarea>
<div class="textarea_note">Enter list of addresses, each address on its own line.</div>
</div>
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
