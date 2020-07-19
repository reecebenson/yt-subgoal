<?php
	/* --------------------- */
	/* Reece Benson | © 2017 */
	/* --------------------- */
	$page['id']			= -1;
	$page['restricted']	= false;
	require_once('sys/core.php');

	// > Check $_GET's are set
	if(!isset($_GET)) die('Error!');
	if(!isset($_GET['type']) || empty($_GET['type'])) die('No type specified.');
	if(!isset($_GET['cid']) || empty($_GET['cid'])) die('No channel specified.');

	// > Build our correct API
	$api = new YT_API;
	$api->init();
	$api->getChannelDetails($_GET['cid']);
	$api->updateSubscriberCount();

	// > Update Plugin Requests Data
	@$site->incrementPluginRequest(1, $_GET['cid']);

	// > Variables
	$override = false;
	$transSpeed = 150;
	$startPoint = $api->getSubscriberCount();
	$endPoint = $startPoint;
	$type = $_GET['type'];
	$cid = $_GET['cid'];
	$raw = (isset($_GET['raw']) && !empty($_GET['raw']));
	$goalSplitter = (isset($_GET['goalSplitter']) ? $_GET['goalSplitter'] : 50);

	// > Override
	if(isset($_GET['override']) && !empty($_GET['override']) && $_GET['override'] == "1")
	{
		$override = true;
		$transSpeed = (isset($_GET['speed']) && !empty($_GET['speed']) ? $_GET['speed'] : 150);
		$startPoint = (isset($_GET['start']) && (!empty($_GET['start'] || $_GET['start'] == "0")) ? $_GET['start'] : $api->getSubscriberCount());
		$endPoint = (isset($_GET['end']) && !empty($_GET['end']) ? $_GET['end'] : $api->getSubscriberCount());
	}
?>
<?php if(!$raw) { ?>
<html>
	<head>
		<title><?=$api->getChannelName();?></title>
		<link rel="stylesheet" href="<?=$www;?>/assets/css/odometer-theme-default.css" />
		<script src="<?=$pwww;?>/assets/js/jquery-1.11.0.min.js"></script>
		<script src="<?=$www;?>/assets/js/odometer.min.js"></script>
		<style type="text/css">
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            width: 100%;
            font-family: "Verdana", sans-serif;
        }

        body {
            display: table;
        }

        .center {
            text-align: center;
            display: table-cell;
            vertical-align: middle;

            font-size: 64px;
        }

        .remove_me
        {
        	font-size: 12px;
        	color: #ccc;
        }
		</style>
	</head>
	<body>
		<div class="center">
			<div id="counter" class="odometer">
				<?php
					switch($type)
					{
						case "count":
							echo $startPoint;//number_format($api->getSubscriberCount());
							break;
						case "goal":
							echo ceil($api->getSubscriberCount()/$goalSplitter)*$goalSplitter;
							break;
					}
				?>
			</div>
			<div class="remove_me">
				<?php if($type == "goal") { ?><span>sub goal</span><?php } ?>
				<br/><br/>
				plugin created by reece benson aka simple
				<?php if(!$override) { ?>
					<br/>made it because i was bored and wanted to be useful
				<?php }else{ ?>
					<br/><i>override enabled, speed = <?=$transSpeed;?>ms, start = <?=$startPoint;?>, end = <?=$endPoint;?></i>
				<?php } ?>
			</div>
		</div>

		<!-- Poller -->
		<script type="text/javascript">
		var $ = jQuery;
		$(document).ready(function()
		{
			<?php if($type == "count") { ?>
			var el = document.querySelector('#counter');
			od = new Odometer({
				el: el,
				value: <?=$startPoint;?>,
				format: '(,ddd)',
				theme: 'minimal',
				spacer: ',',
				duration: <?=$transSpeed;?>
			});

			<?php if(!$override) { ?>
				od.update(<?=$startPoint;?>);
			<?php } ?>

			setInterval(function()
			{
				$.get( '<?=$www;?>/plugins/ytcounter/sc/<?=$api->getChannelID();?>&raw=1', function(count) {
					if($.isNumeric(count) && count > 0)
					{
						od.update(count);
					}
				});
  			}, 2000);

  			<?php }else if($type == "goal") { ?>

			var el = document.querySelector('#counter');
			od = new Odometer({
				el: el,
				value: Math.ceil(<?=$api->getSubscriberCount();?>/<?=$goalSplitter;?>)*<?=$goalSplitter;?>,
				format: '(,ddd)',
				theme: 'minimal',
				spacer: ',',
				duration: <?=$transSpeed;?>
			});

			setInterval(function()
			{
				$.get( '<?=$www;?>/plugins/ytcounter/sg/<?=$api->getChannelID();?>&raw=1&goalSplitter=<?=$goalSplitter;?>', function(count) {
					if($.isNumeric(count) && count > 0)
					{
						od.update(count);
					}
				});
  			}, 2500);
  			<?php } ?>
		});
		</script>
	</body>
</html>
<?php
}else{
	switch($type)
	{
		default:
		case "count":
			echo $api->getSubscriberCount();
			break;

		case "goal":
			echo ceil($api->getSubscriberCount()/$goalSplitter)*$goalSplitter;
			break;
	}
}
 ?>