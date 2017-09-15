<!DOCTYPE html>
<?php

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

		<div id="banner">
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

		<div class="postitem">
			<h1>Sample Event Post</h1>
			<p class="postinfo">Posted on 02 September 2017 at 12:00 by <a href="">Nation Name</a></p>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce nec fermentum ipsum. Etiam auctor in mi vel ultricies. Fusce rhoncus vitae nunc quis elementum. Phasellus at enim congue, volutpat purus vitae, pharetra dui. Mauris et risus pharetra, consectetur nisl vitae, semper massa. Pellentesque vitae feugiat velit. Quisque a sapien eget ligula venenatis ultrices vel eu erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
			<p>Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nullam tempus metus ut turpis tristique, eu viverra enim mollis. Vivamus maximus leo id consectetur fringilla. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nunc venenatis velit vitae tortor accumsan mollis. Pellentesque suscipit erat quis pulvinar fringilla. Proin mattis ex tellus, sit amet faucibus libero ornare vitae.</p>
			<p>Sed vitae nulla libero. Interdum et malesuada fames ac ante ipsum primis in faucibus. Mauris eget justo quis magna mollis tincidunt. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus mattis consequat bibendum. Integer at elit at quam gravida ultricies. Vestibulum vitae volutpat quam. Sed in egestas velit, ac facilisis lorem. In tristique, lorem id dictum pharetra, lorem turpis vestibulum lacus, in hendrerit velit leo sollicitudin tellus. Aliquam ac arcu odio. Nunc fermentum scelerisque turpis, vel mattis quam lobortis in.</p>
		</div>

		<div class="postitem">
			<h1>Another Event Post</h1>
			<p class="postinfo">Posted on 31 August 2017 at 08:00 by <a href="">Nation Name</a></p>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce nec fermentum ipsum. Etiam auctor in mi vel ultricies. Fusce rhoncus vitae nunc quis elementum. Phasellus at enim congue, volutpat purus vitae, pharetra dui. Mauris et risus pharetra, consectetur nisl vitae, semper massa. Pellentesque vitae feugiat velit. Quisque a sapien eget ligula venenatis ultrices vel eu erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
			<p>Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nullam tempus metus ut turpis tristique, eu viverra enim mollis. Vivamus maximus leo id consectetur fringilla. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nunc venenatis velit vitae tortor accumsan mollis. Pellentesque suscipit erat quis pulvinar fringilla. Proin mattis ex tellus, sit amet faucibus libero ornare vitae.</p>
		</div>

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