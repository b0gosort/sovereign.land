<!DOCTYPE html>
<?php

include "Parsedown.php";
$Parsedown = new Parsedown();

ini_set("user_agent", "Roleplay Platform http://sovereign.land");

function show_error($description) {
	return "<html><head><title>Error | Sovereign.Land</title><style>body{font-family:sans-serif;font-size:20px;margin:40px}</style></head><body><h1>Something went wrong :'(</h2><p>There was an error processing your request. Please try again later.</p><p>If this problem persists, please contact a regional administrator.</p><p><b>Error description:</b> $description</p></body></html>";
}

if (isset($_GET["r"])) {
	$region = filter_var($_GET["r"], FILTER_SANITIZE_STRING);
	$region = strtolower(str_replace(" ", "_", $region));

	if (file_exists("regions/$region.json")) {
		$region_json = file_get_contents("regions/$region.json");
		$region_data = json_decode($region_json, true);

		$census_obja = simplexml_load_file("https://www.nationstates.net/cgi-bin/api.cgi?region=$region&q=numnations+flag");
		$census_json = json_encode($census_obja);
		$census_objb = json_decode($census_json, true);

		$region_display = ucwords(str_replace("_", " ", $region));

		$post_dir = "posts/$region";
	} else {
		echo show_error("The region '$region' could not be found on Sovereign.Land.");
		die();
	}
} else {
	header("Location: http://sovereign.land/");
	die();
}

?>
<html>
	<head>
		<title><?php echo $region_display; ?> | Sovereign.Land</title>
		<link rel="stylesheet" href="stylesheets/region.css">
		<link rel="stylesheet" href="stylesheets/theme_day.css" id="theme_link">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Arimo:400,400i,700,700i|Roboto+Condensed:700">
		<link rel="icon" type="image/png" href="favicon.png">

		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body>
		<div id="topbar">
			<div id="topcontainer">
				<p>
					<img src='<?php echo $census_objb["FLAG"]; ?>'>
					<b><a href="http://nationstates.net/region=<?php echo $region; ?>" target="_blank"><?php echo $region_display; ?></a></b>
					<?php echo $census_objb["NUMNATIONS"]; ?> nations
				</p>
				<p id="topbuttons">
					<a href="http://sovereign.land/">sovereign.land</a>
					<a class="topbutton" href="#" title="Night Theme" onclick="switchTheme(this)">N</a>
					<a class="topbutton" href="#" title="Write New Post" onclick="showForm(this)">+</a>
				</p>
			</div>
		</div>

		<div id="content">
			<form class="postitem" id="post_form" action="makepost.php" method="post">
				<input type="hidden" name="region" value="<?php echo $region; ?>">
				<div class="formsection">
					<p>You must verify your nation before posting.</p>
					<a class="button" href="https://www.nationstates.net/page=verify_login" target="_blank">Get Verification Code</a>
					<input type="text" name="nation" placeholder="enter your nation name">
					<input type="text" name="vcode" placeholder="enter your verification code">
				</div>

				<div class="formsection">
					<input type="text" name="title" id="titlebox" placeholder="enter a post title">
					<textarea name="content"></textarea>
				</div>

				<div class="formsection">
					<button type="submit">Make Post in <?php echo $region_display; ?></button>
				</div>
			</form>

			<?php

			$files = array_filter(glob($post_dir . "/*"), "is_file");

			$page = 1;
			if (isset($_GET["page"])) $page = filter_var($_GET["page"], FILTER_SANITIZE_NUMBER_INT);
			
			$end = $page * 4;
			$start = $end - 4;

			foreach (array_slice(array_reverse($files), $start, $end) as $file) {
				$post_json = file_get_contents($file);
				$post_data = json_decode($post_json, true);

				$title = $post_data["title"];
				//$time = date("d F Y \at H:i", $post_data["time"]);
				$time = $post_data["time"];
				$author = $post_data["author"];
				$author_link = $post_data["authorLink"];
				$content = $Parsedown->text($post_data["content"]);

				echo "<div class='postitem'><h1>$title</h1><p class='postinfo'>Posted on $time by <a href='$author_link' target='_blank'>$author</a></p><span class='postcontent'>$content</span></div>";
			}

			?>

			<div id="footer">
				<p>Developed by <a href="http://b0gosort.github.io">Solborg Development</a></p>
				<p>Copyright &copy; 2017 Cooper Johnston</p>
			</div>
		</div>

		<div id="prevpage" class="pagearrow">
			<?php
			if ($page > 1) {
				$prevpage = $page - 1;
				echo "<a href='region.php?r=$region&page=$prevpage' title='Previous Page'>&lt;</a>\n";
			}			
			?>
		</div>

		<div id="nextpage" class="pagearrow">
			<?php
			$nextpage = $page + 1;
			echo "<a href='region.php?r=$region&page=$nextpage' title='Next Page'>&gt;</a>"
			?>
		</div>

		<script>
			function showForm(button) {
				var theForm = document.getElementById("post_form");

				if (theForm.style.height == "800px") {
					theForm.style.height = 0;
					button.title = "Write New Post";
					button.innerHTML = "+"
				} else {
					theForm.style.height = "800px";
					button.title = "Cancel Post";
					button.innerHTML = "X";
				}
			}

			function switchTheme(button) {
				var themeLink = document.getElementById("theme_link");

				if (button.title == "Day Theme") {
					themeLink.href = "stylesheets/theme_day.css";
					button.title = "Night Theme";
					button.innerHTML = "N";
				} else {
					themeLink.href = "stylesheets/theme_night.css";
					button.title = "Day Theme";
					button.innerHTML = "D";
				}
			}
		</script>
	</body>
</html>