<html>
<head>
	<title>Jenkins Dashboard</title>
	<style>
	body {
		padding:0;
		margin:0;
	}

	iframe {
		border:0;
		background-color:black;
		color:white;
		text-align: center;
	}

	</style>
</head>
<body>
<iframe id="status" src="/status/status.php" width="100%" height="100%"></iframe>
<script>
setInterval(function(){
	document.getElementById("status").src="/status/status.php";
}, 300000);
</script>
</body>
</html>
