<?php
	/* --------------------- */
	/* Reece Benson | © 2017 */
	/* --------------------- */
	$page['id'] = 4;
	$page['restricted']	= false;

	require_once('sys/core.php');

	// > Has our form been submitted?
	$ytSubmitted = false;
	if(isset($_POST))
	{
		if(isset($_POST['ytSubmit']) && @$_POST['ytSubmit'] == "1")
		{
			if(isset($_POST['ytName']) && @!empty($_POST['ytName']))
			{
				$ytSubmitted = true;
				$api = new YT_API;
				$api->init();
				$api->getChannelDetails($_POST['ytName']);
				$api->updateSubscriberCount();
			}
		}
	}

	// > Increase Plugin Click
	$site->increasePluginView(1);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>YouTube Counter | Reece Benson</title>
		<?php require_once('pages/sheader.php'); ?>
		<link rel="stylesheet" href="<?=$pwww;?>/assets/css/neon-forms.css">
	</head>
	<body>
		<?php require_once('pages/maintenance.php'); ?>

		<div class="wrap">
			<?php require_once('pages/navigation.php'); ?>

			<!-- YouTube Sub Counter Separator -->
			<section class="breadcrumb" style="padding: 10px 0;">
				<div class="container">
					<div class="row">
						<div class="col-sm-12">
							<ol class="breadcrumb bc-12" style="margin: 0;">
								<li><a href="<?=$www;?>"><i class="entypo-home"></i> Home</a></li>
								<li><a href="<?=$www;?>/plugins">Plugins</a></li>
								<li class="active"><strong>YouTube Counter</strong></li>
							</ol>
						</div>
					</div>
				</div>
			</section>

			<!-- Content -->
			<?php
				// > Get our plugin
				$plugin = $site->getPlugin(1);
			?>
			<section class="content-section">
				<div class="container">
					<div class="row">
						<div class="col-sm-12 text-center">
							<h3><?=$plugin['name'];?></h3>
							<p><?=$plugin['info'];?></p>
						</div>
					</div>

					<?php if($site->getPluginState(1) == "1") { ?>
					<div class="row">
						<div class="col-sm-3"></div>
						<div class="col-sm-6">
							<form action="" method="post">
								<input type="hidden" name="ytSubmit" value="1" />

								<div class="input-group">
									<input class="form-control" type="text" maxlength="50" value="<?=($ytSubmitted ? $_POST['ytName'] : '');?>" placeholder="YouTube Name or Channel ID" name="ytName" />

									<span class="input-group-btn">
										<button class="btn btn-success" type="submit" id="ytSearchSubmit"><i class="entypo-right-thin"></i></button>
									</span>
								</div>
								
							</form>
						</div>
						<div class="col-sm-3"></div>
					</div>

					<hr/>
					
					<?php if(!$ytSubmitted) { ?>

					<div class="row">
						<div class="col-sm-3"></div>
						<div class="col-sm-6 text-center">
							<h3>Active in the last 30 minutes</h3>
							<p>
								Listed below are the channels that have used this service in</br>
								the previous 30 minutes.
							</p>
							<p>
								<table class="table text-left">
									<thead>
										<tr>
											<th scope="col" class="col-sm-7">Channel ID</th>
											<th scope="col">Request Count</th>
											<th scope="col" class="col-sm-1">Channel</th>
										</tr>
									</thead>
									<tbody>
									<?php
										// Get Plugin Request Leaderboards
										$reqs = $site->getLastActiveRequests(1);
										while($r = $reqs->fetch_array()) { ?>
										<tr>
											<th scope="row"><?=$r['reference'];?></th>
											<td><?=$r['count'];?></td>
											<td><a href="https://youtube.com/channel/<?=$r['reference'];?>/videos?flow=grid&view=57" target="_blank"><i class="entypo-export"></i></a></td>
										</tr>
										<?php }
									?>
									</tbody>
								</table>
							</p>
							<h3>Current Requests Leaderboard</h3>
							<p>
								This shows the channels with the highest amount of requests.</br>
								Overall total requests: <strong><?=$site->getOverallPluginRequests(1);?></strong> <em>(since 20th April)</em>
							</p>
							<p>
									<table class="table text-left">
										<thead>
											<tr>
												<th scope="col" class="col-sm-7">Channel ID</th>
												<th scope="col">Request Count</th>
												<th scope="col" class="col-sm-1">Channel</th>
											</tr>
										</thead>
										<tbody>
										<?php
											// Get Plugin Request Leaderboards
											$reqs2 = $site->getTopTenPluginRequests(1);
											while($r2 = $reqs2->fetch_array()) { ?>
											<tr>
														<th scope="row"><?=$r2['reference'];?></th>
															<td><?=$r2['count'];?></td>
															<td><a href="https://youtube.com/channel/<?=$r2['reference'];?>/videos?flow=grid&view=57" target="_blank"><i class="entypo-export"></i></a></td>
											</tr>
											<?php }
										?>
										</tbody>
						</table>
							</p>
						</div>
						<div class="col-sm-3"></div>
					</div>

					<?php } else { ?>
					<div class="row">
						<div class="col-sm-12 text-center">
							<h3><?=$api->getChannelName();?>'s Details</h3>
							<p><div class="g-ytsubscribe" data-channelid="<?=$api->getChannelID();?>" data-layout="default" data-count="default"></div></p>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-sm-2"></div>
						<div class="col-sm-8">
							<div class="form-group">
								<label for="field-3" class="col-sm-3 control-label">Current Subscribers</label>
								
								<div class="col-sm-8">
									<div class="input-group">
										<input type="text" class="form-control" id="field-3" value="<?=$api->getSubscriberCount();?>" readonly>

										<span class="input-group-btn">
											<button class="btn btn-primary" type="submit" disabled><i class="entypo-globe"></i></button>
										</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="field-3" class="col-sm-3 control-label">Channel ID</label>
								
								<div class="col-sm-8">
									<div class="input-group">
										<input type="text" class="form-control" id="field-3" value="<?=$api->getChannelID();?>" readonly>

										<span class="input-group-btn">
											<button class="btn btn-primary" type="submit" disabled><i class="entypo-globe"></i></button>
										</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="field-3" class="col-sm-3 control-label">Channel Name</label>
								
								<div class="col-sm-8">
									<div class="input-group">
										<input type="text" class="form-control" id="field-3" value="<?=$api->getChannelName();?>" readonly>

										<span class="input-group-btn">
											<button class="btn btn-primary" type="submit" disabled><i class="entypo-globe"></i></button>
										</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="field-3" class="col-sm-3 control-label">Live?</label>
								
								<div class="col-sm-8">
									<div class="input-group">
										<input type="text" class="form-control" id="field-3" value="<?=$api->getChannelLivestate();?>" readonly>

										<span class="input-group-btn">
											<button class="btn btn-primary" type="submit" disabled><i class="entypo-globe"></i></button>
										</span>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-2"></div>
					</div>

					<hr/>

					<div class="row form-horizontal">
						<div class="col-sm-2"></div>
						<div class="col-sm-8">
							<div class="form-group">
								<label for="field-3" class="col-sm-3 control-label">Split Count</label>
								
								<div class="col-sm-8">
									<input type="number" class="form-control" id="ytSplitGoals" placeholder="Sub Goal Increments (i.e. 25, 50, 100, 250, etc.)" value="50" step="5">
								</div>
							</div>
							<div class="form-group">
								<label for="field-3" class="col-sm-3 control-label">Subscription Counter</label>
								
								<div class="col-sm-8">
									<div class="input-group">
										<input type="text" class="form-control" id="field-3" placeholder="Browser Source Link" value="<?=$api->getLink('sc');?>" readonly>

										<span class="input-group-btn">
											<a href="<?=$api->getLink('sc');?>" target="_blank"><button class="btn btn-success" type="submit"><i class="entypo-right-thin"></i></button></a>
										</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="field-3" class="col-sm-3 control-label">Subscription Goal</label>
								
								<div class="col-sm-8">
									<div class="input-group">
										<input type="text" class="form-control" id="ytSubGoal" placeholder="Browser Source Link" value="<?=$api->getLink('sg', 'goalSplitter=50');?>" readonly>

										<span class="input-group-btn">
											<a href="<?=$api->getLink('sg', 'goalSplitter=50');?>" id="ytSubGoalLink" target="_blank"><button class="btn btn-success" type="submit"><i class="entypo-right-thin"></i></button></a>
										</span>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-2"></div>
					</div>

					<hr/>

					<div class="row">
						<div class="col-sm-12 text-center">
							<h3>CSS Options</h3>
							<br/>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-sm-2"></div>
						<div class="col-sm-8">
							<div class="form-group">
								<label class="col-sm-3 control-label">Text Colour</label>
								
								<div class="col-sm-8">
									<div class="input-group">
										<input type="text" id="cssTextColour" class="form-control colorpicker" data-format="rgba" value="rgba(0, 0, 0, 1)" />
										
										<div class="input-group-addon">
											<i class="color-preview"></i>
										</div>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label">Background Colour</label>
								
								<div class="col-sm-8">
									<div class="input-group">
										<input type="text" id="cssBackgroundColour" class="form-control colorpicker" data-format="rgba" value="rgba(255, 255, 255, 1)" />
										
										<div class="input-group-addon">
											<i class="color-preview"></i>
										</div>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label">Font Name</label>
								
								<div class="col-sm-8">
									<input type="text" id="cssFontName" class="form-control" value="Verdana" placeholder="Enter custom font names (must be installed on your PC)">
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label">Font Size</label>
								
								<div class="col-sm-8">
									<input type="text" id="cssFontSize" class="form-control" value="64">
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label">Bold Text</label>
								
								<div class="col-sm-8">
									<select type="text" id="cssBoldText" class="form-control">
										<option value="true">Yes</option>
										<option value="false">No</option>
									</select>
								</div>
							</div>

							<div class="form-group">
								<div class="col-sm-1"></div>
								<div class="col-sm-10">
									<button type="button" class="btn btn-success" style="width: 100%;" id="cssUpdateBtn">
										<i class="entypo-cog"></i> Update CSS
									</button>
								</div>
								<div class="col-sm-1"></div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label">Generated CSS</label>
								
								<div class="col-sm-8">
									<textarea class="form-control" id="generatedCSS" style="min-height: 250px;" readonly>body { };</textarea>
								</div>
							</div>
						</div>
						<div class="col-sm-2"></div>
					</div>
					<?php } ?>
					<?php }else{ ?>
					<div class="row">
						<div class="col-sm-12 text-center">
							<p>This tool is currently undergoing maintenance. Please check back later!</p>
						</div>
					</div>
					<?php } ?>
				</div>
			</section>

			<!-- Footer -->
			<?php require_once('pages/sfooter.php'); ?>
		</div>

		<!-- Scripts for this page -->
		<script src="<?=$www;?>/assets/js/gsap/main-gsap.js"></script>
		<script src="<?=$www;?>/assets/js/bootstrap.js"></script>
		<script src="<?=$www;?>/assets/js/joinable.js"></script>
		<script src="<?=$www;?>/assets/js/resizeable.js"></script>
		<script src="<?=$pwww;?>/assets/js/bootstrap-colorpicker.min.js"></script>
		<script src="<?=$www;?>/assets/js/neon-slider.js"></script>
		<script src="https://apis.google.com/js/platform.js"></script>

		<!-- JavaScripts initializations and stuff -->
		<script src="<?=$www;?>/assets/js/neon-custom.js"></script>
		<script src="<?=$pwww;?>/assets/js/neon-custom.js"></script>

		<!-- Custom Scripts -->
		<script type="text/javascript">
		var $ = jQuery;
		$(document).ready(function()
		{
			function updateCSSGen()
			{
				var txtColor = $("#cssTextColour");
				var bgColor = $("#cssBackgroundColour");
				var fName = $("#cssFontName");
				var txtBold = $("#cssBoldText");
				var txtSize = $("#cssFontSize");

				// > Check if the text is bold
				var boldText = "";
				if(txtBold.val() == "true") boldText = "\tfont-weight: bold;\n";
				else boldText = "";

				$('#generatedCSS').val("body {\n\tbackground-color: " + bgColor.val() + ";\n\tmargin: 0px auto;\n\toverflow: hidden;\n\tcolor: " + txtColor.val() + ";\n\tfont-family: \"" + fName.val() + "\";\n" + boldText + "}\n\n.center {\n\ttext-align: center;\n\tdisplay: table-cell;\n\tvertical-align: middle;\n\tfont-size: " + txtSize.val() + "px !important;\n}\n\n.remove_me {\n\tdisplay: none !important;\n}");
			}

			$('#cssTextColour,#cssBackgroundColour,#cssFontName,#cssBoldText,#cssFontSize').bind('input propertychange', updateCSSGen);
			$('#cssTextColour,#cssBackgroundColour,#cssFontName,#cssBoldText,#cssFontSize').change(updateCSSGen);
			$('#cssUpdateBtn').on('click', updateCSSGen);
			updateCSSGen();

			var splitGoals = $("#ytSplitGoals");
			var subGoalA = $("#ytSubGoalLink");
			var subGoal = $("#ytSubGoal");
			var link = "<?=$api->getLink('sg');?>";
			$('#ytSplitGoals').bind('input propertychange', function()
			{
				subGoal.val(link + "&goalSplitter=" + splitGoals.val());
				subGoalA.prop('href', subGoal.val());
			});
		});
		</script>
	</body>
</html>
