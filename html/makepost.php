<!DOCTYPE html>
<?php

ini_set("user_agent", "Roleplay Platform http://sovereign.land");

function show_error($description) {
	return "<h1>Something went wrong :'(</h2><p>There was an error processing your request. Please try again later.</p><p>If this problem persists, please contact a regional administrator.</p><p><b>Error description:</b> $description</p></body></html>";
}

function iseligible($name, $settings, $set_region) {
	$census_obja = simplexml_load_file("https://www.nationstates.net/cgi-bin/api.cgi?nation=$name&q=name+region+wa");
	$census_json = json_encode($census_obja);
	$census_objb = json_decode($census_json, true);

	$nat_region = strtolower($census_objb["REGION"]);
	if ($settings["requireResidency"] && $nat_region != $set_region) return false;

	if ($settings["requireWA"] && strpos($census_objb["UNSTATUS"], "WA") === false) return false;

	return true;
}

$message = "";

if (isset($_POST["nation"])) {
	$nation = strtolower(filter_var(str_replace(" ", "_", $_POST["nation"]), FILTER_SANITIZE_ENCODED));
	$vcode = filter_var($_POST["vcode"], FILTER_SANITIZE_ENCODED);
	$region = filter_var($_POST["region"], FILTER_SANITIZE_ENCODED);

	$config_json = file_get_contents("regions/$region.json");
	$config_data = json_decode($config_json, true);

	$nation_display = ucwords(str_replace("_", " ", $nation));

	if (iseligible($nation, $config_data, $region)) {
		$verification = file_get_contents("https://www.nationstates.net/cgi-bin/api.cgi?a=verify&nation=$nation&checksum=$vcode");
		if (strpos($verification, "1") !== false) {
			$post_title = filter_var($_POST["title"], FILTER_SANITIZE_STRING);
			$timestamp = date("d F Y") . " at " . date("H:i");
			$post_content = filter_var($_POST["content"], FILTER_SANITIZE_STRING);

			$t = time();

			$post_data = array (
				"title" => $post_title,
				"time" => $timestamp,
				"author" => $nation_display,
				"authorLink" => "https://nationstates.net/nation=$nation",
				"content" => $post_content
			);
			$post_json = json_encode($post_data);

			$post_file = fopen("posts/$region/$t" . "_$nation.json", "w");
			fwrite($post_file, $post_json);
			fclose($post_file);

			$message = "<h1>Success</h1><p>Your post has been made.</p>";
		} else {
			$message = show_error("Verification process failed");
		}
	} else {
		$message = show_error("Nation does not meet posting requirements");
	}
} else {
	$message = show_error("No nation name specified");
}

?>
<html>
	<head>
		<title>Make a Post | Sovereign.Land</title>
		<style>
			body {
				font-family: sans-serif;
				font-size: 20px;
				margin: 40px;
			}
		</style>
	</head>
	<body>
		<?php echo $message; ?>
	</body>
</html>