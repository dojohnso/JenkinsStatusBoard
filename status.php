<?php

$username = 'your_jenkins_readonly_username';
$password = 'your_jenkins_readonly_password';
$jenkins_url = 'your_jenkins_url';


$context = stream_context_create(array(
    'http' => array(
        'header'  => "Authorization: Basic " . base64_encode("$username:$password")
    )
));

$projects = array(
	'list-of',
	'full-project-names',
	'you-want-to',
	'keep-track-of',
);

$envs = array(
	'prod',
	'qa',
	'trunk',
);
$stati = array();
foreach ( $projects AS $project )
{
	foreach ( $envs AS $env )
	{
		$job_name = "$project-$env"; //edit this for job name format

		//get the latest build number
		$build_json = file_get_contents( "$jenkins_url/job/$job_name/api/json?pretty=true", false, $context );
		$build_info = json_decode( $build_json );

		$name = $build_info->displayName;
		$build = $build_info->builds[0]->number;

		//check the latest status
		$job_json = file_get_contents( "$jenkins_url/job/$job_name/$build/api/json?pretty=true", false, $context );
		$job_info = json_decode( $job_json );

		$stati[$project][$env][$build]['result'] = strtolower( $job_info->result );
		$stati[$project][$env][$build]['date'] = date( 'm/d/Y @ g:i:s a', substr( $job_info->timestamp, 0, -3 ) );
	}
}
?><html>
<head>
<title>Jenkins Build Status Board</title>
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
	float:left;
	width:25%;
	padding:5px 5px;
	margin:5px 5px 10px 0;
}
.success { background-color: #008B00; }
.failure { background-color: #FF3030; }
.aborted { background-color: #858585; }
.wrapper {
	clear:both;
	border-bottom: 1px dotted #999;
}
.project {
	text-align:right;
	width:15%;
	padding-right:10px;
	float:left;
	font-size: 24px;
}
.env { float:left; }
.date { float:right;padding-top:2px; font-size: 12px; color:#fefefe; }
#lastupdate {
	color:#fff;
	clear:both;
	text-align: center;
	font-size:12px;
	font-family: helvetica;
}

</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script>
var failure = false;
</script>
</head>
<body>
<div id="lastupdate">Last updated: <?php echo date('m/d/Y g:i:s a') ?></div>
<?php
foreach ( $stati AS $project => $project_info )
{
	?>
	<div class="wrapper">
	<div class='project'><?php echo $project; ?></div>
	<?php
	foreach ( $project_info AS $env => $env_info )
	{
		foreach ( $env_info AS $build => $status )
		{
			?>
			<div class="status <?php echo $status['result']; ?>">
				<div class='env'><?php echo strtoupper( $env ); ?> #<?php echo $build; ?></div>
				<div class='date'><?php echo $status['date']; ?></div>
			</div>

			<?php if ($status['result'] == 'failure') { ?>
				<script>
				failure = true;
				</script>
			<?php }
		}
	}
	?>
	</div>
	<?php
}
?>

<script>
if ( failure )
{
	setInterval("$('body').animate( {'background-color':'#FF3030'}, 1500 ).animate( {'background-color':'#000'}, 1500 );",3000);
}
</script>
</body>
</html>
