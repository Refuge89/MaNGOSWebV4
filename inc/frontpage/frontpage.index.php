<?php
/****************************************************************************/
/*  						< MangosWeb v4 >  								*/
/*              Copyright (C) <2017> <Mistvale.com>   		                */
/*					  < http://www.mistvale.com >							*/
/*																			*/
/*			Original MangosWeb Enhanced (C) 2010-2011 KeysWow				*/
/*			Original MangosWeb (C) 2007, Sasha, Nafe, TGM, Peec				*/
/****************************************************************************/

//========================//
if(INCLUDED !== TRUE) 
{
	echo "Not Included!"; 
	exit;
}
$pathway_info[] = array('title' => $lang['activation'], 'link' => '');
// ==================== //

$postnum = 0;
$hl = '';

// If the hit counter is enabled, then add a hit when this page is accessed
if($mwe_config['fp_hitcounter'] == 1)
{
    $count_my_page = "inc/frontpage/hitcounter.txt";
    $hits = (int)file_get_contents($count_my_page);
    $hits++;
    file_put_contents($count_my_page, $hits);
}

$alltopics = $DB->select("SELECT * FROM mw_news ORDER BY `id` DESC");
$servers = array();
$multirealms = getRealmlist();
foreach($multirealms as $realmnow_arr)
{
	if($mwe_config['fp_serverinfo'] == 1)
	{
		$data = $RDB->selectRow("SELECT * FROM realmlist WHERE id ='".$realmnow_arr['id']."' LIMIT 1");
		$realm_data_explode = explode(';', $data['dbinfo']);

		//DBinfo column:  char_host;char_port;char_username;char_password;charDBname;world_host;world_port;world_username;world_pass;worldDBname
		$mangosALL = array(
			'db_host' => $realm_data_explode['0'],  	// DB Host
			'db_port' => $realm_data_explode['1'], 		// DB port
			'db_username' => $realm_data_explode['2'], 	// DB username
			'db_password' => $realm_data_explode['3'], 	// DB password
			'db_name' => $realm_data_explode['4'], 		// Character db name
		);
		unset($realm_data_explode);

		$CHDB_EXTRA = new Database(
			$mangosALL['db_host'],
			$mangosALL['db_port'],
			$mangosALL['db_username'],
			$mangosALL['db_password'],
			$mangosALL['db_name']
		);
		unset($mangosALL); // Free up memory.

		$server = array();
		$server['name'] = $data['name'];
		if((int)$mwe_config['fp_realmstatus'] == 1)
		{
			$checkaddress = $data['address'];
			$server['realm_status'] = (check_port_status($checkaddress, $data['port'], 0.5) === true) ? true : false;
		}
		$changerealmtoparam = array("changerealm_to" => $realmnow_arr['id']);
		if($mwe_config['fp_playersonline'] == 1)
		{
			$server['playersonline'] = $CHDB_EXTRA->count("SELECT COUNT(*) FROM `characters` WHERE online=1");
			$server['playersonline'] = $server['playersonline']['COUNT(*)'];
			$server['onlineurl'] = mw_url('server', 'playersonline', $changerealmtoparam);
		}
		if($mwe_config['fp_serverip'] == 1)
		{
			$server['server_ip'] = $data['address'];
		}
		if($mwe_config['fp_servertype'] == 1)
		{
			$server['type'] = $realm_type_def[$data['icon']];
		}
		if($mwe_config['fp_serverlang'] == 1)
		{
			$server['language'] = $realm_timezone_def[$data['timezone']];
		}
		if($mwe_config['fp_serverpop'] == 1)
		{
			$server['population'] = $CHDB_EXTRA->count("SELECT COUNT(*) FROM `characters` WHERE online=1");
			$server['population'] = $server['population']['COUNT(*)'];
		}
		if($mwe_config['fp_serveract'] == 1)
		{
			$server['accounts'] = $DB->count("SELECT COUNT(*) FROM `account`");
			$server['accounts'] = $server['accounts']['COUNT(*)'];
		}
		if($mwe_config['fp_serveractive_act'] == 1)
		{
			$server['active_accounts'] = $DB->count("SELECT COUNT(*) FROM `account` WHERE `last_login` > ". date("Y-m-d", strtotime("-2 week")));
			$server['active_accounts'] = $server['active_accounts']['COUNT(*)'];
		}
		if($mwe_config['fp_serverchars'] == 1)
		{
			$server['characters'] = $CHDB_EXTRA->count("SELECT COUNT(*) FROM `characters`");
			$server['characters'] = $server['characters']['COUNT(*)'];
		}
		unset($CHDB_EXTRA, $data); // Free up memory.

		$server['moreinfo'] = $mwe_config['fp_server_moreinfo'] && 0; // 0 is suppossed to signify that PATH TO SERVER CONFIG IS NOT NULL
		$servers[] = $server;
	}
}

unset($multirealms);
if($mwe_config['module_onlinelist'] == 1)
{
	$usersonhomepage = $DB->count("SELECT COUNT(*) FROM `mw_online`");
	$usersonhomepage = $usersonhomepage['COUNT(*)'];
}

