<?php 
// If we have found SSI.php and we are outside of SMF, then we are running standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF')) // If we are outside SMF and can't find SSI.php, then throw an error
	die('<b>Error:</b> Cannot install - please verify you put this file in the same place as SMF\'s SSI.php.');

updateSettings(array('max_numberofSignatures' => 1));

$result = db_query("DESCRIBE {$db_prefix}messages signature_id"
			,__FILE__,__LINE__
		  );
if (!mysql_num_rows($result))
	db_query("ALTER TABLE
			{$db_prefix}messages
			ADD signature_id
			TINYINT
			NOT NULL"
			, __FILE__, __LINE__
		);

$result = db_query("DESCRIBE {$db_prefix}members random_signature"
			,__FILE__,__LINE__
		  );
if (!mysql_num_rows($result))
	db_query("ALTER TABLE
			{$db_prefix}members
			ADD random_signature
			TINYINT
			NOT NULL"
			, __FILE__, __LINE__
		);

$result = db_query("DESCRIBE {$db_prefix}personal_messages signature_id"
			,__FILE__,__LINE__
		  );
if (!mysql_num_rows($result))
	db_query("ALTER TABLE
			{$db_prefix}personal_messages
			ADD signature_id
			TINYINT
			NOT NULL"
			, __FILE__, __LINE__
		);

?>