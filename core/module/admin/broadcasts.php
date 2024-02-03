<?php

  if( !route(2) ):
    $page   = 1;
  elseif( is_numeric(route(2)) ):
    $page   = route(2);
  elseif( !is_numeric(route(2)) ):
    $action = route(2);
  endif;

  if( empty($action) ):
      
    $notifications        = $conn->prepare("SELECT * FROM notifications_popup ");
    $notifications        -> execute(array());
    $notifications        = $notifications->fetchAll(PDO::FETCH_ASSOC);
    
    

    require admin_view('broadcasts');
	
	
	
	
    
    elseif( $action == "edit" ):
	
      if( $_POST ):
            $nId = $_POST['id'];
            $title = $_POST['title'];
              $description =$_POST['description'];
              $isAllPage = $_POST['isAllPage'];
              $allPages = json_encode($_POST['allPages']); 
              $action_link = $_POST['action_link'];
                            $icon = $_POST['icon'];

              $action_text = $_POST['action_text'];
              $expiry_date = $_POST['expiry_date'];
              $isAllUser = $_POST['isAllUser'];
              $status = $_POST['status'];
              
              if(date("Y-m-d H:i:s") < $expiry_date){
                    $insert = $conn->prepare("UPDATE notifications_popup SET title=:title,description=:description,isAllPage=:isAllPage,allPages=:allPages,action_link=:action_link,action_text=:action_text,expiry_date=:expiry_date,isAllUser=:isAllUser,status=:status,icon=:icon  WHERE id=:id ");
               $insert = $insert-> execute(array("id"=>$nId,"title"=>$title,"description"=>$description,"isAllPage"=>$isAllPage,"allPages"=>$allPages,"action_link"=>$action_link,"action_text"=>$action_text,"expiry_date"=>$expiry_date,"isAllUser"=>$isAllUser,"status"=>$status,"icon"=>$icon));              if( $insert ):
          
                  header("Location:".site_url("admin/broadcasts"));
                else:
          
                  header("Location:".site_url("admin/broadcasts"));
                endif;
              }else {
                  echo '<script>alert("Error! Expiry Date should be more than current date");</script>';
                  
              }
              

		 
			
	else:
	    $link = $_SERVER['REQUEST_URI'];
        $link_array = explode('/',$link);
        $nId = end($link_array);
        $pages        = $conn->prepare("SELECT * FROM pages ");
        $pages        -> execute(array());
        $pages        = $pages->fetchAll(PDO::FETCH_ASSOC);
        
        $notifications        = $conn->prepare("SELECT * FROM notifications_popup WHERE id= $nId LIMIT 1");
        $notifications        -> execute(array());
        $notifData        = $notifications->fetchAll(PDO::FETCH_ASSOC)[0];   
       
	    require admin_view('editbroadcasts');
	  
	endif;
	
	elseif( $action == "delete" ):
	
	if( $_POST ):
	 $notification_id =  $_POST['notification_id'];

		   $delete = $conn->prepare("DELETE FROM notifications_popup WHERE id= $notification_id");
           $delete->execute(array("id"=>$notification_id));
            if( $delete ):
			
              header("Location:".site_url("admin/broadcasts"));
            else:
			
              header("Location:".site_url("admin/broadcasts"));
            endif;
			
			
	  
	endif;
	
	elseif( $action == "create" ):
	    
	    $pages        = $conn->prepare("SELECT * FROM pages ");
        $pages        -> execute(array());
        $pages        = $pages->fetchAll(PDO::FETCH_ASSOC);
	  require admin_view('createbroadcasts');

	
  elseif( $action == "new" ):
            
            
          $title = $_POST['title'];
          $description = $_POST['description'];
          
          $isAllPage = $_POST['isAllPage'];
          if(!$isAllPage){
             $isAllPage= 0; 
          }
          $allPages = json_encode($_POST['allPages']); 
          $action_link = $_POST['action_link'];
          $action_text  = $_POST['action_text'];
          $expiry_date = $_POST['expiry_date'];
          $isAllUser = $_POST['isAllUser'];
                                      $icon = $_POST['icon'];

            if(date("Y-m-d H:i:s") < $expiry_date){
          
          $sql = "INSERT INTO notifications_popup (title, description, action_link, action_text , expiry_date, isAllUser, isAllPage, allPages,icon) VALUES (?,?,?,?,?,?,?,?,?)";
          $insert = $conn->prepare($sql)->execute([$title, $description, $action_link, $action_text ,$expiry_date, $isAllUser, $isAllPage, $allPages,$icon]);
          if($insert){
              echo 'Created Successfuly';
          }else{
              echo 'Error! Please try Again';
          }
          header("Location:".site_url("admin/broadcasts"));

            }else {
                 echo '<script>alert("Error! Expiry Date should be more than current date");</script>';
            }
  endif;