<?php
    $message = $this->session->flashdata('message');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	    <title>LEO login page</title>
	    <meta name="author" content="Amit Shah" />
	    <meta name="description" content="LEO Dashboard" />
	    <meta name="application-name" content="LEO Dashboard" />
	
	    <!-- Mobile Specific Metas -->
	    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	
	    <!-- Le styles -->
	    <link href="<?php echo base_url()?>assets/styles/style.css" rel="stylesheet" />
    </head>
      
    <body>
        <div class="login-wrap">
            <img src="<?php echo base_url()?>assets/images/logo.png"/>
            <div><?php echo $message;?></div>
            
            <div class="login-form">
                <form class="form-horizontal" action="<?php echo base_url()?>auth/login" method="post" id="loginForm" >
                    <ul class="admin-list">
                        <li>
                            <input type="radio" class="radio" value="user" id="login_as" name="login_as">
                            <label>App User</label>
                        </li>
                        <li>
                            <input type="radio" class="radio" value="affiliate" id="login_as" name="login_as">
                            <label>Affiliate</label>
                        </li>
                        <li>
                            <input type="radio" checked="checked" class="radio" value="retailer" id="login_as" name="login_as">
                            <label>Retailer</label>
                        </li>
                        <li>
                            <input type="radio" class="radio" value="licensee" id="login_as" name="login_as">
                            <label>Licensee</label>
                        </li>
                    </ul>
                    <input name="identity" id="identity" type="text" class="text" placeholder="User name" required/>
                    <input name="password" id="password" type="password" class="text" placeholder="Password" required/>
                    
                    <input type="submit" class="submit-login" value="Login" />
                    <a href="<?php echo base_url()?>auth/AdminLogin" class="forgot">Click here for Admin Login</a>
                </form>
            </div>
        </div>
	    <script  type="text/javascript" src="<?php echo base_url()?>assets/scripts/jquery-1.10.2.min.js"></script>
  </body>
</html>