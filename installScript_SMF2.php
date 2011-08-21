<?php 
// If we have found SSI.php and we are outside of SMF, then we are running standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF')) // If we are outside SMF and can't find SSI.php, then throw an error
	die('<b>Error:</b> Cannot install - please verify you put this file in the same place as SMF\'s SSI.php.');
  
updateSettings(array('max_numberofSignatures' => 1));

db_extend('packages');
$smcFunc['db_add_column'] (
			'{db_prefix}messages', 
			array(
			      'name' => 'signature_id',
			      'type' => 'TINYINT',
			      'default' => '0'
			),
			array(),
			'ignore'
		);

$smcFunc['db_add_column'] (
			'{db_prefix}members', 
			array(
			      'name' => 'random_signature',
			      'type' => 'TINYINT',
			      'default' => '0'
			),
			array(),
			'ignore'
		);

$smcFunc['db_add_column'] (
			'{db_prefix}personal_messages', 
			array(
			      'name' => 'signature_id',
			      'type' => 'TINYINT',
			      'default' => '0'
			),
			array(),
			'ignore'
		);

$smcFunc['db_add_column'] (
			'{db_prefix}topics', 
			array(
			      'name' => 'disabled_signatures',
			      'type' => 'TINYINT',
			      'default' => '0'
			),
			array(),
			'ignore'
		);
$smcFunc['db_add_column'] (
			'{db_prefix}boards', 
			array(
			      'name' => 'disabled_signatures',
			      'type' => 'TINYINT',
			      'default' => '0'
			),
			array(),
			'ignore'
		);

?>