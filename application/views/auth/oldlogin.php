<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Oil Pickup</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<!-- Apple devices fullscreen -->
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<!-- Apple devices fullscreen -->
	<meta names="apple-mobile-web-app-status-bar-style" content="black-translucent" />

	<?php
	//test for http / https for non hosted files
	$http = 'http';
	if(isset($_SERVER['HTTPS']))
	{
		$http .= 's';
	}
	?>
	<!-- Styles -->
		<!-- Bootstrap -->
		<link rel="stylesheet" href="<?php echo base_url()?>assets/styles/bootstrap.css">
		<!-- Bootstrap responsive -->
		<link rel="stylesheet" href="<?php echo base_url()?>assets/styles/bootstrap-responsive.min.css">
		<!-- Theme CSS -->
		<link rel="stylesheet" href="<?php echo base_url()?>assets/styles/style.css">
		
		<!-- Color CSS -->
		<link rel="stylesheet" href="<?php echo base_url()?>assets/styles/themes.css">
		
		<!-- jQuery UI -->
		<link rel="stylesheet" href="<?php echo base_url()?>assets/styles/jquery-ui-1.8.16.custom.css">
		
	<!-- End of Styles -->
	
	<!-- Scripts -->
		<!-- jQuery -->
		<script src="<?php echo base_url()?>assets/scripts/jquery-1.9.1.min.js"></script>
		
		<!-- Bootstrap -->
		<script src="<?php echo base_url()?>assets/scripts/bootstrap.min.js"></script>

		<!-- Theme framework -->
		<script src="<?php echo base_url()?>assets/scripts/eakroko.min.js"></script>
	
		<!-- Theme scripts -->
		<script src="<?php echo base_url()?>assets/scripts/application.js"></script>
			
	
		<!--[if lte IE 9]>
			<script src="<?php echo base_url()?>assets/scripts/plugins/placeholder/jquery.placeholder.min.js"></script>
			<script>
				$(document).ready(function() {
					$('input, textarea').placeholder();
				});
			</script>
		<![endif]-->
	<!-- End of Scripts -->	

	<script type="text/javascript">
		function buttons()
		{
			$('.list_buttons').button("refresh");
			$('.button_set').button("refresh");
			$('.button').button("refresh");
		}
	</script>
</head>

<body class='login'>
	<div class="wrapper">
		<h1>Oil Pickup - Admin Section</h1>
		<div id="infoMessage"><?php echo $message;?></div>
		<div class="login-body">
			<h2>SIGN IN</h2>
			<?php echo form_open("auth/login");?>
				<input type="hidden" name="cmd" value="login" />
				<div class="control-group">
					<div class="email controls">
						<input type="text" name='identity' placeholder="Login ID" class='input-block-level' data-rule-required="true">
					</div>
				</div>
				<div class="control-group">
					<div class="pw controls">
						<input type="password" name="password" placeholder="Password" class='input-block-level' data-rule-required="true">
					</div>
				</div>
				<div class="submit">
					<input type="submit" value="Sign me in" class='btn btn-primary'>
				</div>
			</form>
			<div class="forget">
				<a href="forgot_password"><span>Forgot password?</span></a>
			</div>
		</div>
	</div>
</body>

</html>
