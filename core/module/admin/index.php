<?php


		if($_GET["theme"]!='' && $_GET["theme"] == 1 || $_GET["theme"] == 2):
		
			$updateTheme = $conn->prepare('UPDATE clients SET admin_theme=:admin_theme WHERE client_id=:id');
			$updateTheme->execute(array('admin_theme'=>$_GET['theme'],'id'=>$user["client_id"]));
     
          header("Location:".site_url('admin/'.$_GET["refer"]));
		
		endif;

    
if(!$_GET["refer"]){
if($user["access"]["users"]){
    
    header("Location:".site_url("admin/clients"));
    
}elseif($user["access"]["orders"]){

        header("Location:".site_url("admin/orders"));
        
}elseif($user["access"]["subscriptions"]){

        header("Location:".site_url("admin/subscriptions"));
    
}elseif($user["access"]["dripfeed"]){

        header("Location:".site_url("admin/dripfeed"));
    
}elseif($user["access"]["services"]){

        header("Location:".site_url("admin/services"));
    
}elseif($user["access"]["payments"]){

        header("Location:".site_url("admin/payments"));
    
}elseif($user["access"]["tickets"]){

        header("Location:".site_url("admin/tickets"));
    
}elseif($user["access"]["reports"]){

        header("Location:".site_url("admin/reports"));
    
}elseif($user["access"]["logs"]){

        header("Location:".site_url("admin/logs"));
        
}elseif($user["access"]["general_settings"] || $user["access"]["payments_settings"] || $user["access"]["providers"] || $user["access"]["payments_bonus"] || $user["access"]["alert_settings"] || $user["access"]["modules"]){

        header("Location:".site_url("admin/settings"));
    
}elseif($user["access"]["pages"] || $user["access"]["blog"] || $user["access"]["menu"] || $user["access"]["themes"] || $user["access"]["language"]){
    
        header("Location:".site_url("admin/appearance"));
}

}


require admin_view('index');
