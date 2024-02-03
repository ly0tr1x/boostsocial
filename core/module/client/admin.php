<?php
  
if($_SESSION['otp']){
     header('Location:'.site_url('otp'));
    
}


  if( $user["access"]["admin_access"]  && $_SESSION["neira_adminlogin"] && $user["client_type"] == 2 && $_COOKIE["a_login"] && $user["login_ip"] == GetIP()):
  
        if( !route(1) ){
          $route[1] = "index";
        }
        
        if( !file_exists( admin_controller(route(1)) ) ){
          $route[1] = "index";
        }
        
        require admin_controller( route(1) );
        
  else:
      
          $route[1] = "login";
          require admin_controller( route(1) );
          
  endif;