<!DOCTYPE html>
<?php

include "Parsedown.php";
$Parsedown = new Parsedown();

function show_error($description) {
	return "<html><head><title>Error | Sovereign.Land</title><style>body{font-family:sans-serif;font-size:20px;margin:40px}</style></head><body><h1>Something went wrong :'(</h2><p>There was an error processing your request. Please try again later.</p><p>If this problem persists, please contact a regional administrator.</p><p><b>Error description:</b> $description</p></body></html>";
}

if (isset($_GET["r"])) {
	$region = filter_var($_GET["r"], FILTER_SANITIZE_STRING);
	$region = strtolower(str_replace(" ", "_", $region));

	if (file_exists("regions/$region.json")) {
		$region_json = file_get_contents("regions/$region.json");
		$region_data = json_decode($region_json, true);

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
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Arimo:400,700|Montserrat:700">
		<link rel="icon" type="image/png" href="favicon.png">
	</head>
	<body>
		<form action="" method="get" id="searchbar">
			<input type="text" name="r" placeholder="enter a region name and press enter">
		</form>

		<div id="banner" style="background-image: url('images/<?php echo $region; ?>.jpg')">
			<div id="titlearea">
				<h1><a href="http://sovereign.land/">sovereign.land</a></h1>
				<h2><a href=""><?php echo $region_display; ?></a></h2>
				<!--<h3>game player, regional government, security council, founderless, gargantuan, sinker, defender, democratic, offsite forums, world assembly</h3>-->
				<button onclick="showForm(this)">Write New Post</button>
				<a href=""><button>Edit This Page</button></a>
			</div>
		</div>

		<form class="postitem" id="post_form" action="makepost.php" method="post">
			<div class="formsection">
				<p>You must verify your nation before posting.</p>
				<button>Get Verification Code</button>
				<input type="text" name="nation" placeholder="enter your nation name">
				<input type="text" name="vcode" placeholder="enter your verification code">
			</div>

			<div class="formsection">
				<input type="text" name="title" id="titlebox" placeholder="enter a post title">
				<textarea name="content"></textarea>
			</div>

			<div class="formsection">
				<button type="submit">Make Post in Region Name</button>
			</div>
		</form>

		<?php

		$files = array_filter(glob($post_dir . "/*"), "is_file");

		foreach(array_slice(array_reverse($files), 0, 4) as $file) {
			$post_json = file_get_contents($file);
			$post_data = json_decode($post_json, true);

			$title = $post_data["title"];
			//$time = date("d F Y \at H:i", $post_data["time"]);
			$time = $post_data["time"];
			$author = $post_data["author"];
			$author_link = $post_data["authorLink"];
			$content = $Parsedown->text($post_data["content"]);

			echo "<div class='postitem'><h1>$title</h1><p class='postinfo'>Posted on $time by <a href='$author_link' target='_blank'>$author</a></p>$content</div>";
		}

		?>

		<script>
			function showForm(button) {
				var theForm = document.getElementById("post_form");

				if (theForm.style.height == "732px") {
					theForm.style.height = 0;
				} else {
					theForm.style.height = "732px";
				}

				button.innerHTML = button.innerHTML == "Write New Post" ? "Cancel Post" : "Write New Post";
			}
		</script>
	</body>
</html>