<!DOCTYPE html>
<html lang="tr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=$settings["site_name"]?></title>
    <link href="/css/admin/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/8dc6670d84.js" crossorigin="anonymous"></script>
    
          </head>
  <body>
    
                    <div class="container">
                <div class="container container-fluid" role="main">
        <div class="col-sm-offset-4 col-sm-4 m-t">
                <?php if( $success ): ?>
          <div class="alert alert-success "><?php echo $successText; ?></div>
        <?php endif; ?>
           <?php if( $error ): ?>
          <div class="alert alert-danger "><?php echo $errorText; ?></div>
        <?php endif; ?>
          <form id="yw0" action="" method="post">                                                          <div class="form-group">
            <label for="exampleInputEmail1">Username</label>
            <input class="form-control" name="username" id="AdminUsers_login" type="text" maxlength="300" />            </div>
            <div class="form-group">
              <label for="exampleInputPassword1">Password</label>
              <input class="form-control" name="password" id="AdminUsers_passwd" type="password" maxlength="300" />            </div>
            
 <?php if(  $_SESSION["recaptcha"]  ): ?>
            <div class="form-group">
              <div class="g-recaptcha" data-sitekey="<?php echo $settings["recaptcha_key"] ?>"></div>
            </div>
          <?php endif; ?>

            <div class="checkbox">
              <label>
                <input type="hidden" name="remember" value="1"> 
              </label>
            </div>
            <button type="submit" name="login" class="btn btn-default">Login</button>
          </form>      </div>
    </div>
    
<!--Glycon-->    </div>
<script src='https://www.google.com/recaptcha/api.js?hl=en'></script>

      </body>
<html>



