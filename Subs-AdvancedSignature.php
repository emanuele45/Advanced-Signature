<?php
/**
 * Advanced Signature (advsig)
 *
 * @package advsig
 * @author emanuele
 * @copyright 2010 - 2012 emanuele, Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 0.3.0
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 *
 * Hooks
 *
 */

function advsig_permissions (&$permissionGroups, &$permissionList, &$leftPermissionGroups, &$hiddenPermissions, &$relabelPermissions)
{
	$permissionList['board']['hide_topic_signatures'] = array(false, 'topic', 'moderate');
}

function advsig_add_topic_button (&$mod_buttons)
{
	global $topicinfo, $context, $scripturl, $board_info;

	if (!empty($board_info['disabled_signatures']))
		return;

	$mod_buttons['hide_sign'] = array(
		'test' => 'hide_topic_signatures',
		'text' => empty($topicinfo['disabled_signatures']) ? 'hide_sign' : 'unhide_sign',
		'lang' => true,
		'url' => $scripturl . '?action=hide_sign;topic=' . $context['current_topic'],
	);
}

function advsig_add_modsettings (&$config_vars)
{
	global $modSettings;

	if (!empty($modSettings['modlog_enabled']))
		$config_vars[] = array('check', 'disable_log_hide_signature');
}

function advsig_set_modsettings ()
{
	global $modSettings, $topicinfo, $context, $board, $topic;

	$context['hide_topic_signatures'] = allowedTo('hide_topic_signatures');

	if (substr($modSettings['signature_settings'], 0, 1) == 1)
		$modSettings['signature_settings'] = substr_replace($modSettings['signature_settings'], !empty($topicinfo['disabled_signatures']) ? 0 : 1, 0, 1);
}

function advsig_create_action (&$actionArray)
{
	$actionArray['hide_sign'] = array('Subs-AdvancedSignature.php', 'advsig_disable_in_current_topic');
}

/**
 *
 * End of hooks
 *
 */

function advsig_stripSignatures ($sign_t, $id = false)
{
	global $modav_member, $modSettings;

	if (empty($sign_t) && !empty($modSettings['default_signature']) && isset($modav_member))
		$sign_t = str_replace(
			array(
				'{USERNAME}',
				'{NAME}',
				'{PROFILE_HREF}',
				'{WEBSITE_HREF}',
				'{WEBSITE_TITLE}',
			),
			array(
				$modav_member['username'],
				$modav_member['name'],
				$modav_member['href'],
				$modav_member['website']['url'],
				$modav_member['website']['title'],
				),
			$modSettings['default_signature']
		);

	if (is_array($sign_t))
		$signs = $sign_t;
	elseif (strpos($sign_t, '[ENDOFSIGNATURE]') !== false)
		$signs = explode('[ENDOFSIGNATURE]', $sign_t);
	else
		$signs = unserialize($sign_t);

	$signs = array_filter($signs);

	if ($id !== false)
	{
		if (isset($signs[$id]))
			return $signs[$id];
		else
			return $signs[0];
	}
	else
		return $signs;
}

function advsig_countSignatures ($member_id = false)
{
	global $context, $user_info;

	$member_id = !empty($member_id) ? $member_id : $user_info['id'];
	if (!isset($context['user_avail_signatures'][$member_id]))
		advsig_getSignatures($member_id);

	return count($context['user_avail_signatures'][$member_id]);
}

function advsig_getSignatures ($member_id = false)
{
	global $smcFunc, $user_info, $context, $memberContext;

	$member_id = !empty($member_id) ? $member_id : $user_info['id'];
	if (!isset($context['user_avail_signatures'][$member_id]))
	{
		$request = $smcFunc['db_query']('', '
			SELECT signature
			FROM {db_prefix}members
			WHERE id_member = {int:member_id}
			LIMIT 1',
			array(
				'member_id' => $member_id,
			)
		);
		$row = $smcFunc['db_fetch_assoc']($request);
		$smcFunc['db_free_result']($request);
		$context['user_avail_signatures'][$member_id] = advsig_stripSignatures($row['signature']);
	}
	return $context['user_avail_signatures'][$member_id];
}

function advsig_getSignatureID ($name, $poster_ID = false)
{
	if ($name == 'nosignature')
		return -2;
	elseif ($name == 'randomsignature')
		return -1;
	else
	{
		$signs = advsig_countSignatures($poster_ID);
		for ($i = 0; $i < $signs; $i++)
			if ($name == 'signature_' . $i)
				return $i;

		return 0;
	}
}

function advsig_getSignatureByID ($ID, $member_id = false)
{
	global $modav_member, $memberContext;

	if (!empty($member_id) && !empty($memberContext[$member_id]))
	{
		$modav_member['username'] = $memberContext[$member_id]['username'];
		$modav_member['name'] = $memberContext[$member_id]['name'];
		$modav_member['href'] = empty($memberContext[$member_id]['href']) ? '' : $memberContext[$member_id]['href'];
		$modav_member['website']['url'] = empty($memberContext[$member_id]['website']['url']) ? '' : $memberContext[$member_id]['website']['url'];
		$modav_member['website']['title'] = empty($memberContext[$member_id]['website']['title']) ? '' : $memberContext[$member_id]['website']['title'];
	}

	$signs = advsig_getSignatures($member_id);

	if (empty($signs))
		return '';

	if ($ID == -2)
		return '';
	elseif ($ID == -1)
		return parse_bbc($signs[rand(0,count($signs)-1)]);
	elseif ($ID < count($signs))
		return parse_bbc($signs[$ID]);
	else
		return parse_bbc($signs[0]);
}

function advsig_prepare_signatures ($is_post = false, $poster_ID = false, $signature_id = false)
{
	global $context, $user_info, $txt;

	if (!empty($_POST))
		$chosen_signature = isset($_POST['multiplesignatures']) ? $_POST['multiplesignatures'] : 'signature_0';
	else
		$chosen_signature = 'none';

	$signature_id = isset($_REQUEST['msg']) ? $signature_id : null;
	$signs = advsig_countSignatures($poster_ID);

	// It's a post
	if ($is_post && $signs > 0 && !empty($poster_ID))
		$context['stored_signature'] = $signature_id !== false ? $signature_id : 0;

	$context['avail_signatures'][-2] = array(
		'name' => 'nosignature',
		'label' => $txt['nosignature'],
		'selected' => $chosen_signature == 'nosignature' || $signature_id==-2 ? 1 : 0
	);
	$context['avail_signatures'][-1] = array(
		'name' => 'randomsignature',
		'label' => $txt['randomsignature'],
		'selected' => $chosen_signature == 'randomsignature' || $signature_id==-1 ? 1 : 0
	);
	for ($i = 0; $i < $signs; $i++)
		$context['avail_signatures'][$i] = array(
			'name' => 'signature_' . $i,
			'label' => sprintf($txt['signature_numb'], $i + 1),
			'selected' => $chosen_signature == 'signature_' . $i || $signature_id==$i ? 1 : 0
		);
}

function advsig_disable_in_current_topic ()
{
	global $smcFunc, $topic, $board, $modSettings;

	// A bit of copy and paste from function Sticky() (LockTopic.php)
	isAllowedTo('hide_topic_signatures');

	if (empty($topic))
		fatal_lang_error('not_a_topic', false);

	$request = $smcFunc['db_query']('', '
		SELECT disabled_signatures
		FROM {db_prefix}topics
		WHERE id_topic = {int:current_topic}
		LIMIT 1',
		array(
			'current_topic' => $topic,
		)
	);
	list ($is_disabled) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	$smcFunc['db_query']('', '
		UPDATE {db_prefix}topics
		SET disabled_signatures = {int:is_disabled}
		WHERE id_topic = {int:current_topic}',
		array(
			'current_topic' => $topic,
			'is_disabled' => empty($is_disabled) ? 1 : 0,
		)
	);

	if (!empty($modSettings['modlog_enabled']) && empty($modSettings['disable_log_hide_signature']))
		logAction(empty($is_disabled) ? 'hide_sign' : 'unhide_sign', array('topic' => $topic, 'board' => $board));

	redirectexit('topic=' . $topic . '.' . $_REQUEST['start'] . (WIRELESS ? ';moderate' : ''));
}

/* ADMIN */
function advsig_restoreSignatures (&$sig_t, $admins = false)
{
	global $modSettings, $smcFunc;

	//Sorry admins you are powerfull, but not gods... :P
	if ($admins)
	{
		$request = $smcFunc['db_query']('', '
			SELECT id_member, signature
			FROM {db_prefix}members
			WHERE id_group = {int:admin_group}
				OR FIND_IN_SET({int:admin_group}, additional_groups) = 1',
			array(
				'admin_group' => 1,
			)
		);
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$sig = strtr($row['signature'], array('<br />' => "\n"));
			advsig_restoreSignatures($sig);
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}members
				SET signature = {string:signature}
				WHERE id_member = {int:id_member}',
				array(
					'id_member' => $row['id_member'],
					'signature' => $sig,
				)
			);
		}
	}

	if (!empty($modSettings['max_numberofSignatures']))
	{
		$sigs = advsig_stripSignatures($sig_t);
		if (count($sigs) > $modSettings['max_numberofSignatures'])
		{
			$c_sigs = count($sigs) + 1;
			for ($i = $modSettings['max_numberofSignatures']; $i < $c_sigs; $i++)
				unset($sigs[$i]);
		}

	$sig_t = serialize($sigs);
	}
}

?>