<?php if (!defined('BB2_CORE')) die('I said no cheating!');

function bb2_blacklist($package) {

	// Blacklisted user agents
	// These user agent strings occur at the beginning of the line.
	$bb2_spambots_0 = array(
		"<sc",			// XSS exploit attempts
		"8484 Boston Project",	// video poker/porn spam
		"adwords",		// referrer spam
		"autoemailspider",	// spam harvester
		"blogsearchbot-martin",	// from honeypot
		"Digger",		// spam harvester
		"ecollector",		// spam harvester
		"EmailCollector",	// spam harvester
		"Email Extractor",	// spam harvester
		"Email Siphon",		// spam harvester
		"EmailSiphon",		// spam harvester
		"grub crawler",		// misc comment/email spam
		"HttpProxy",		// misc comment/email spam
		"Internet Explorer",	// XMLRPC exploits seen
		"Jakarta Commons",	// custommised spambots
		"Java 1.",		// definitely a spammer
		"Java/1.",		// definitely a spammer
		"libwww-perl",		// spambot scripts
		"LWP",			// spambot scripts
		"Microsoft URL",	// spam harvester
		"Missigua",		// spam harvester
		"Movable Type",		// customised spambots
		"Mozilla ",		// malicious software
		"Mozilla/4.0(",		// from honeypot
		"Mozilla/4.0+(",	// suspicious harvester
		"MSIE",			// malicious software
		"OmniExplorer",		// spam harvester
		"PussyCat ",		// misc comment spam
		"psycheclone",		// spam harvester
		"Shockwave Flash",	// spam harvester
		"User Agent: ",		// spam harvester
		"User-Agent: ",		// spam harvester
		"Wordpress Hash Grabber",// malicious software
		"\"",			// malicious software
	);

	// These user agent strings occur anywhere within the line.
	$bb2_spambots = array(
		"; Widows ",		// misc comment/email spam
		"a href=",		// referrer spam
		"Bad Behavior Test",	// Add this to your user-agent to test BB
		"compatible ; MSIE",	// misc comment/email spam
		"compatible-",		// misc comment/email spam
		"DTS Agent",		// misc comment/email spam
		"Gecko/25",		// revisit this in 500 years
		"grub-client",		// search engine ignores robots.txt
		"hanzoweb",		// very badly behaved crawler
		"Indy Library",		// misc comment/email spam
		"larbin@unspecified",	// stealth harvesters
		"Murzillo compatible",	// comment spam bot
		".NET CLR 1)",		// free poker, etc.
		"POE-Component-Client",	// free poker, etc.
		"Turing Machine",	// www.anonymizer.com abuse
		"WISEbot",		// spam harvester
		"WISEnutbot",		// spam harvester
		"Windows NT 4.0;)",	// wikispam bot
		"Windows NT 5.0;)",	// wikispam bot
		"Windows NT 5.1;)",	// wikispam bot
		"Windows XP 5",		// spam harvester
		"\\\\)",		// spam harvester
	);

	// These are regular expression matches.
	$bb2_spambots_regex = array(
		"/^[A-Z]{10}$/",	// misc email spam
		"/^Mozilla...[05]$/i",	// fake user agent/email spam
		"/[bcdfghjklmnpqrstvwxz ]{8,}/",
//		"/(;\){1,2}$/",		// misc spammers/harvesters
//		"/MSIE.*Windows XP/",	// misc comment spam
	);

	// Do not edit below this line.

	$ua = $package['headers_mixed']['User-Agent'];

	foreach ($bb2_spambots_0 as $spambot) {
		$pos = stripos($ua, $spambot);
		if ($pos !== FALSE && $pos == 0) {
			return "17f4e8c8";
		}
	}

	foreach ($bb2_spambots as $spambot) {
		if (stripos($ua, $spambot) !== FALSE) {
			return "17f4e8c8";
		}
	}

	foreach ($bb2_spambots_regex as $spambot) {
		if (preg_match($spambot, $ua)) {
			return "17f4e8c8";
		}
	}

	return FALSE;
}

?>
