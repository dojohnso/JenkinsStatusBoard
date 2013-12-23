<?php

$username = 'your_jenkins_readonly_username';
$password = 'your_jenkins_readonly_password';
$jenkins_url = 'http://your_jenkins_url';

$context = stream_context_create(array(
    'http' => array(
        'header'  => "Authorization: Basic " . base64_encode("$username:$password")
    )
));

// get all jobs
$projects = file_get_contents( "$jenkins_url/api/json?pretty=true", false, $context );
$projects = json_decode( $projects );
$jobs = $projects->jobs;

$stati = array();
foreach ( $jobs AS $job )
{
	//get the latest build number
	$build_json = @file_get_contents( "{$job->url}api/json?pretty=true", false, $context );

	$build_info = json_decode( $build_json );

	$build = $build_info->builds[0]->number;

	//check the latest status
	$job_json = @file_get_contents( "{$job->url}$build/api/json?pretty=true", false, $context );
	$job_info = json_decode( $job_json );

	$job_info->timestamp = !empty( $job_info->timestamp ) ? substr( $job_info->timestamp, 0, -3 ) : null;

	$stati[$job->name][$build]['result'] = strtolower( $job_info->result );
	$stati[$job->name][$build]['date'] = empty( $job_info->timestamp ) ? 'n/a' : date( 'n-j H:i:s', $job_info->timestamp );
	$stati[$job->name][$build]['timestamp'] = $job_info->timestamp;
}
?><html>
<head>
<title>Build Status Board</title>
<style>
body {
	background: black;
	color: white;
	margin:0;
	padding:0;
	font-family: helvetica;
	position: relative;
}

.status {
	text-align: center;
	position:relative;
	color:#fefefe;
	float:left;
	width:auto;
	overflow:hidden;
	padding:10px;
	background-color: #858585;
	margin:5px 5px 10px 0;
}
.success { background-color: #008B00; }
.failure { background-color: #FF3030; }
.aborted { background-color: #858585; }
#lastupdate {
	color:#fff;
	clear:both;
	text-align: center;
	font-size:12px;
	font-family: helvetica;
}

</style>
</head>
<body>
<div id="lastupdate">Last updated: <?php echo date('m/d/Y g:i:s a') ?> in <?php echo time() - $t; ?> seconds</div>
<?php
foreach ( $stati AS $project => $project_info )
{
	foreach ( $project_info AS $build => $status )
	{
		$proj = '';
		$words = preg_split("/[^A-Za-z0-9]/", $project);
		foreach ($words as $w) {
			$proj .= $w[0];
		}
		?>
			<div class="status <?php echo $status['result']; ?>">
				<div class='project'>
					<?php echo strtoupper( $proj ); ?>
				</div>
			</div>
		<?php
	}
}
?>

</body>
</html>