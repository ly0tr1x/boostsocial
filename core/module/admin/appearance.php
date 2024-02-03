<?php

  if( !route(2) ):
    $route[2]   = "pages";
  endif;

  if( $_SESSION["client"]["data"] ):
    $data = $_SESSION["client"]["data"];
    foreach ($data as $key => $value) {
      $$key = $value;
    }
    unset($_SESSION["client"]);
  endif;

  $menuList = ["Pages"=>"pages","Announcements"=>"news","Blog"=>"blog","Menu"=>"menu","Themes"=>"themes","Languages"=>"language","New Year"=>"new_year","Folders"=>"files"];

  if( !array_search(route(2),$menuList) ):
    header("Location:".site_url("admin/appearance"));
  
   elseif( route(2) == "pages" ):
    $access = $user["access"]["pages"];
      if( $access ):
        if( route(3) == "edit" ):
            $title = "Pages";
          if( $_POST ):
            $id = route(4);
            foreach ($_POST as $key => $value) {
              $$key = $value;
            }
              if( $content == "<br>" || $content == "<p><br></p>" ): $content = ""; endif;
            if( !countRow(["table"=>"pages","where"=>["page_get"=>$id]]) ):
              $error    = 1;
              $icon     = "error";
              $errorText= "Please choose valid payment method";
            else:
              $update = $conn->prepare("UPDATE pages SET page_content=:content WHERE page_get=:id ");
              $update->execute(array("id"=>$id,"content"=>$content ));
                if( $update ):
                  $success    = 1;
                  $successText= "Transaction successful";
                else:
                  $error    = 1;
                  $errorText= "Operation failed";
                endif;
            endif;
          endif;
          $page = $conn->prepare("SELECT * FROM pages WHERE page_get=:get ");
          $page->execute(array("get"=>route(4)));
          $page = $page->fetch(PDO::FETCH_ASSOC); if( !$page ): header("Location:".site_url("admin/appearance/pages")); endif;
        elseif( !route(3) ):
          $pageList = $conn->prepare("SELECT * FROM pages ");
          $pageList->execute(array());
          $pageList = $pageList->fetchAll(PDO::FETCH_ASSOC);
        else:
          header("Location:".site_url("admin/appearance/pages"));
        endif;
      endif;
    if( route(5) ): header("Location:".site_url("admin/appearance/pages")); endif;
    
      elseif( route(2) == "menu" ):
          
    $access = $user["access"]["menu"];
    
          if( $access ):
              
               $id = route(4);
         
        if( $id ):
            
          if(route(3) == "public_true"):

          $update = $conn->prepare("UPDATE menu SET status=:status WHERE id=:id");
          $update = $update->execute(array("id"=>$id,"status"=>2));
          
          header("Location:".site_url("admin/appearance/menu"));

          
          elseif(route(3) == "public_false"):

          $update = $conn->prepare("UPDATE menu SET status=:status WHERE id=:id");
          $update = $update->execute(array("id"=>$id,"status"=>1));
           
          header("Location:".site_url("admin/appearance/menu"));

          
          ## Burası yangın yeri ##
          
          elseif(route(3) == "nopublic_true"): 

              $update = $conn->prepare("UPDATE menu SET public=:public WHERE id=:id");
              $update = $update->execute(array("id"=>$id,"public"=>2));
              
                        header("Location:".site_url("admin/appearance/menu"));

              
          elseif(route(3) == "nopublic_false"): 

              $update = $conn->prepare("UPDATE menu SET public=:public WHERE id=:id");
              $update = $update->execute(array("id"=>$id,"public"=>1));
                    header("Location:".site_url("admin/appearance/menu"));

          endif;

  

        endif;
              
              
        $public = $conn->prepare("SELECT * FROM menu WHERE menu.edit=:edit");
        $public->execute(array("edit"=>0));
        $public = $public->fetchAll(PDO::FETCH_ASSOC);
        
        $nopublic = $conn->prepare("SELECT * FROM menu WHERE menu.edit=:edit");
        $nopublic->execute(array("edit"=>0));
        $nopublic = $nopublic->fetchAll(PDO::FETCH_ASSOC);
              
        if( $_POST ):
          foreach ($_POST as $key => $value) {
            $$key = $value;
          }
          $conn->beginTransaction();
          $update = $conn->prepare("UPDATE settings SET service_list=:services  WHERE id=:id ");
          $update = $update->execute(array("id"=>1,"services"=>$services  ));
          if( $update ):
            $conn->commit();
            header("Location:".site_url("admin/appearance/menu"));
            $_SESSION["client"]["data"]["success"]    = 1;
            $_SESSION["client"]["data"]["successText"]= "Transaction successful";
          else:
            $conn->rollBack();
            $error    = 1;
            $errorText= "Operation failed";
          endif;
          
        endif;
      endif;

    
    elseif( route(2) == "blog" ):
      
      $titleAdmin = "Blog";
    $access = $user["access"]["blog"];
      if( $access ):
 function permalink($str, $options = array())
 {
     $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());
     $defaults = array(
         'delimiter' => '-',
         'limit' => null,
         'lowercase' => true,
         'replacements' => array(),
         'transliterate' => true
     );
     $options = array_merge($defaults, $options);
     $char_map = array(
         // Latin
         'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
         'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
         'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
         'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
         'ß' => 'ss',
         'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
         'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
         'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
         'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
         'ÿ' => 'y',
         // Latin symbols
         '©' => '(c)',
         // Greek
         'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
         'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
         'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
         'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
         'Ϋ' => 'Y',
         'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
         'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
         'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
         'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
         'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
         // Turkish
         'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
         'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
         // Russian
         'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
         'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
         'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
         'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
         'Я' => 'Ya',
         'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
         'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
         'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
         'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
         'я' => 'ya',
         // Ukrainian
         'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
         'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
         // Czech
         'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
         'Ž' => 'Z',
         'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
         'ž' => 'z',
         // Polish
         'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
         'Ż' => 'Z',
         'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
         'ż' => 'z',
         // Latvian
         'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
         'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
         'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
         'š' => 's', 'ū' => 'u', 'ž' => 'z'
     );
     $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
     if ($options['transliterate']) {
         $str = str_replace(array_keys($char_map), $char_map, $str);
     }
     $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
     $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
     $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
     $str = trim($str, $options['delimiter']);
     return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
 }
 
        if( route(3) == "edit" ):
          if( $_POST ):
            $id = route(4);
            foreach ($_POST as $key => $value) {
              $$key = $value;
            }
        
          if ( $_FILES["logo"] && ( $_FILES["logo"]["type"] == "image/jpeg" || $_FILES["logo"]["type"] == "image/jpg" || $_FILES["logo"]["type"] == "image/png" || $_FILES["logo"]["type"] == "image/gif"  ) ):
            $logo_name      = $_FILES["logo"]["name"];
            $uzanti         = substr($logo_name,-4,4);
            $logo_newname   = "images/blog/".md5(rand(10,999)).".jpg";
            $upload_logo    = move_uploaded_file($_FILES["logo"]["tmp_name"],$logo_newname);
            
          elseif( $post["blog_image"] != "" ):
            $logo_newname   = $post["blog_image"];
          else:
            $logo_newname   = "";
          endif;
        
       if( empty($content) ):
          $error    = 1;
          $errorText= "Please write a blog";
          $icon     = "error";
        elseif( empty($name) ):
          $error    = 1;
          $errorText= "Write name";
          $icon     = "error";
        else:
              $update = $conn->prepare("UPDATE blogs SET blog_content=:content, blog_title=:name, blog_image=:logo WHERE id=:id ");
              $update->execute(array("id"=>$id,"content"=>$content,"name"=>$name,"logo"=>$logo_newname ));
                if( $update ):
                  $success    = 1;
                  $successText= "Transaction successful";
                else:
                  $error    = 1;
                  $errorText= "Operation failed";
                endif;
            endif;
          endif;
          $post = $conn->prepare("SELECT * FROM blogs WHERE id=:id ORDER BY blog_created DESC ");
          $post->execute(array("id"=>route(4)));
          $post = $post->fetch(PDO::FETCH_ASSOC); if( !$post ): header("Location:".site_url("admin/appearance/blog")); 
        endif;

        elseif( !route(3) ):
            
            
              if( $_POST ):
        
            foreach ($_POST as $key => $value) {
              $$key = $value;
            }
        
          if ( $_FILES["logo"] && ( $_FILES["logo"]["type"] == "image/jpeg" || $_FILES["logo"]["type"] == "image/jpg" || $_FILES["logo"]["type"] == "image/png" || $_FILES["logo"]["type"] == "image/gif"  ) ):
            $logo_name      = $_FILES["logo"]["name"];
            $uzanti         = substr($logo_name,-4,4);
            $logo_newname   = "images/blog/".md5(rand(10,999)).".jpg";
            $upload_logo    = move_uploaded_file($_FILES["logo"]["tmp_name"],$logo_newname);
            
          elseif( $post["blog_image"] != "" ):
            $logo_newname   = $post["blog_image"];
          else:
            $logo_newname   = "";
          endif;
        
       if( empty($content) ):
          $error    = 1;
          $errorText= "Please write a blog";
          $icon     = "error";
        elseif( empty($name) ):
          $error    = 1;
          $errorText= "Write name";
          $icon     = "error";
        else:
            

          $insert = $conn->prepare("INSERT INTO blogs SET blog_content=:content, blog_title=:name, blog_image=:logo, blog_created=:date, url=:url ");
          $insert = $insert->execute(array("content"=>$content,"name"=>$name,"logo"=>$logo_newname,"date"=>date("Y-m-d H:i:s"),"url"=>permalink($name) ));
              
                if( $insert ):
                  $success    = 1;
                  $successText= "Transaction successful";
                  $referrer = site_url("admin/settings/blog");
                else:
                  $error    = 1;
                  $errorText= "Operation failed";
                endif;
            endif;
          endif;
     
       
            
          $postList = $conn->prepare("SELECT * FROM blogs ORDER BY blog_created DESC ");
          $postList->execute(array());
          $postList = $postList->fetchAll(PDO::FETCH_ASSOC);
          
          elseif( route(3) == "delete" ):
          $id = route(4);
            if( !countRow(["table"=>"blogs","where"=>["id"=>$id]]) ):
              $error    = 1;
              $icon     = "error";
              $errorText= "Please select valid payout bonus";
            else:
              $delete = $conn->prepare("DELETE FROM blogs WHERE id=:id ");
              $delete->execute(array("id"=>$id));
      
                if( $delete ):
                  $error    = 1;
                  $icon     = "success";
                  $errorText= "Transaction successful";
                  $referrer = site_url("admin/settings/blog");
                else:
                  $error    = 1;
                  $icon     = "error";
                  $errorText= "Operation failed";
                endif;
            endif;
             header("Location:".site_url("admin/appearance/blog"));
            exit();
        else:
          header("Location:".site_url("admin/appearance/blog"));
        endif;
      endif;
    if( route(5) ): header("Location:".site_url("admin/appearance/blog")); endif;
    
    
  elseif( route(2) == "language" ):
      $titleAdmin = "Language";
    $access = $user["access"]["language"];
      if( $access ):
        $languageList = $conn->prepare("SELECT * FROM languages");
        $languageList->execute(array());
        $languageList = $languageList->fetchAll(PDO::FETCH_ASSOC);
        if( route(3) && route(3) != "new" && !countRow(["table"=>"languages","where"=>["language_code"=>route(3)]]) ):
          header("Location:".site_url("admin/appearance/language"));
        elseif( route(3) == "new" ):
          include 'language/default.php';
        else:
            if(route(3)){
          $language = $conn->prepare("SELECT * FROM languages WHERE language_code=:code");
          $language->execute(array("code"=>route(3)));
          $language = $language->fetch(PDO::FETCH_ASSOC);
          include 'language/'.route(3).'.php';
         }
        endif;
        if( $_POST && route(3) != "new" && countRow(["table"=>"languages","where"=>["language_code"=>route(3)]]) ):
            
        $isim = $_POST["language"];
            
          $update = $conn->prepare("UPDATE languages SET language_name=:name WHERE language_code=:code ");
          $update->execute(array("code"=>route(3),"name"=>$isim));
            
          $html = '<?php '.PHP_EOL.PHP_EOL;
          $html.= '$languageArray= [';
          foreach ($_POST["Language"] as $key => $value):

            $value = str_replace('"',"'",$value);

            $html .= ' "'.$key.'" => "'.$value.'", '.PHP_EOL;
          endforeach;
          $html .=  '];';
          file_put_contents('language/'.route(3).'.php', $html);
          header("Location:".site_url("admin/appearance/language/".route(3)));
        elseif( route(3) == "new" && $_POST ):
          $name = $_POST["language"];
          $code = $_POST["languagecode"];
          if( countRow(["table"=>"languages","where"=>["language_code"=>$code]]) ):
            $error      = 1;
            $errorText  = "Bu dil kodu zaten kullanılıyor.";
          else:
            $insert = $conn->prepare("INSERT INTO languages SET language_name=:name, language_code=:code ");
            $insert->execute(array("name"=>$name,"code"=>$code ));
              if( $insert ):
                $html = '<?php '.PHP_EOL.PHP_EOL;
                $html.= '$languageArray= [';
                foreach ($_POST["Language"] as $key => $value):
                  $value = str_replace('"',"'",$value);

                  $html .= ' "'.$key.'" => "'.$value.'", '.PHP_EOL;
                endforeach;
                $html .=  '];';
                file_put_contents('language/'.$code.'.php', $html);
                header("Location:".site_url("admin/appearance/language/"));
              endif;
          endif;
        elseif( $_GET["lang-default"] && $_GET["lang-id"] ):
          $update = $conn->prepare("UPDATE languages SET default_language=:default");
          $update->execute(array("default"=>0));
          $update = $conn->prepare("UPDATE languages SET default_language=:default WHERE language_code=:code ");
          $update->execute(array("code"=>$_GET["lang-id"],"default"=>1));
          header("Location:".site_url("admin/appearance/language"));
        elseif( $_GET["lang-type"] && $_GET["lang-id"] ):
          if( countRow(["table"=>"languages","where"=>["language_type"=>"2"]]) > 1 && $_GET["lang-type"] == 1 ):
            $update = $conn->prepare("UPDATE languages SET language_type=:type WHERE language_code=:code ");
            $update->execute(array("code"=>$_GET["lang-id"],"type"=>$_GET["lang-type"]));
          elseif( $_GET["lang-type"] == 2 ):
            $update = $conn->prepare("UPDATE languages SET language_type=:type WHERE language_code=:code ");
            $update->execute(array("code"=>$_GET["lang-id"],"type"=>$_GET["lang-type"]));
          endif;
          header("Location:".site_url("admin/appearance/language"));
        endif;
      endif;
  elseif( route(2) == "themes" ):
      $titleAdmin = "Themes";
    $access = $user["access"]["themes"];
      if( $access ):
        if( route(3) == "active" && countRow(["table"=>"themes","where"=>["theme_dirname"=>route(4)]]) ):
          $update = $conn->prepare("UPDATE settings SET site_theme=:theme WHERE id=:id ");
          $update->execute(array("id"=>1,"theme"=>route(4)));
          
          unset($_SESSION["theme"]);
          
          header("Location:".site_url("admin/appearance/themes"));
        elseif( route(3) && countRow(["table"=>"themes","where"=>["theme_dirname"=>route(3)]]) ):
          $lyt   =  $_GET["file"];
          $theme = $conn->prepare("SELECT * FROM themes WHERE theme_dirname=:name");
          $theme->execute(array("name"=>route(3)));
          $theme = $theme->fetch(PDO::FETCH_ASSOC);
           
          if( $_POST ):
            $text = $_POST["code"];
            $text = str_replace("&lt;","<",$text);
            $text = str_replace("&gt;",">",$text);
            $text = str_replace("&quot;",'"',$text);
            $updated_file   = fopen($fn,"w");
            fwrite($updated_file, $text);
            fclose($updated_file);
            header("Location:".site_url("admin/appearance/themes/".$theme["theme_dirname"]."?file=".$lyt));
          endif;
        elseif( route(3) && !countRow(["table"=>"themes","where"=>["theme_dirname"=>route(3)]]) ):
          header("Location:".site_url("admin/appearance/themes"));
        else:
          $themes = $conn->prepare("SELECT * FROM themes ORDER BY id DESC");
          $themes->execute(array());
          $themes = $themes->fetchAll(PDO::FETCH_ASSOC);
        endif;
      endif;


 elseif( route(2) == "news" ):

    $access = $user["access"]["providers"];
      if( $access ):
          
        if( route(3) == "new" && $_POST ):
          foreach ($_POST as $key => $value) {
            $$key = $value;
          }

          if( empty($icon) ):
            $error    = 1;
            $errorText= "Select icon.";
            $icon     = "error";
          elseif( empty($title) ):
            $error    = 1;
            $errorText= "Announcement name cannot be empty";
            $icon     = "error";
          elseif( empty($content) ):
            $error    = 1;
            $errorText= "Announcement content cannot be empty";
            $icon     = "error";
          else:
              
            $conn->beginTransaction();
            $insert = $conn->prepare("INSERT INTO news SET news_icon=:icon, news_title=:title, news_content=:content, news_date=:date ");
            $insert = $insert->execute(array("icon"=>$icon,"title"=>$title,"content"=>$content,"date"=>date("Y-m-d H:i:s") ));
            if( $insert ):
              $conn->commit();
              $referrer = site_url("admin/appearance/news");
              $error    = 1;
              $errorText= "Transaction successful";
              $icon     = "success";
            else:
              $conn->rollBack();
              $error    = 1;
              $errorText= "Operation failed";
              $icon     = "error";
            endif;
          endif;
          echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer,"time"=>1]);
          exit();
        elseif( route(3) == "edit" && $_POST  ):
          foreach ($_POST as $key => $value) {
            $$key = $value;
          }
          $id = route(4);
         
          if( empty($icon) ):
            $error    = 1;
            $errorText= "Select icon.";
            $icon     = "error";
          elseif( empty($title) ):
            $error    = 1;
            $errorText= "Announcement name cannot be empty";
            $icon     = "error";
          elseif( empty($content) ):
            $error    = 1;
            $errorText= "Announcement content cannot be empty";
            $icon     = "error";
          else:
   
            $conn->beginTransaction();
            $update = $conn->prepare("UPDATE news SET news_icon=:icon, news_title=:title, news_content=:content WHERE id=:id ");
            $update = $update->execute(array("icon"=>$icon,"title"=>$title,"content"=>$content,"id"=>$id));
            if( $update ):
              $conn->commit();
              $referrer = site_url("admin/appearance/news");
              $error    = 1;
              $errorText= "Transaction successful";
              $icon     = "success";
            else:
              $conn->rollBack();
              $error    = 1;
              $errorText= "Operation failed";
              $icon     = "error";
            endif;
          endif;
          echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer,"time"=>1]);
          exit();
              elseif( route(3) == "delete" ):
          $id = route(4);
            if( !countRow(["table"=>"news","where"=>["id"=>$id]]) ):
              $error    = 1;
              $icon     = "error";
              $errorText= "Please select valid announcement";
            else:
              $delete = $conn->prepare("DELETE FROM news WHERE id=:id ");
              $delete->execute(array("id"=>$id));
                if( $delete ):
                  $error    = 1;
                  $icon     = "success";
                  $errorText= "Transaction successful";
                  $referrer = site_url("admin/appearance/news");
                else:
                  $error    = 1;
                  $icon     = "error";
                  $errorText= "Operation failed";
                endif;
            endif;
            echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer,"time"=>0]);
            exit();
        elseif( !route(3) ):
          $newsList = $conn->prepare("SELECT * FROM news ");
          $newsList->execute(array());
          $newsList = $newsList->fetchAll(PDO::FETCH_ASSOC);
        else:
          header("Location:".site_url("admin/appearance/news"));
        endif;
      endif;
      if( route(5) ): header("Location:".site_url("admin/appearance/news")); endif;

      elseif( route(2) == "files" ):
     
      $access = $user["access"]["blog"];
        if( $access ):            
        
                if($_FILES["logo"] ):
               
               if ( $_FILES["logo"] && ( $_FILES["logo"]["type"] == "image/jpeg" || $_FILES["logo"]["type"] == "image/jpg" || $_FILES["logo"]["type"] == "image/png" || $_FILES["logo"]["type"] == "image/gif"  ) ):
            $logo_name      = $_FILES["logo"]["name"];
            $uzanti         = substr($logo_name,-4,4);
            $logo_newname   = "img/files/".md5(rand(1,999999)).$uzanti;
            $upload_logo    = move_uploaded_file($_FILES["logo"]["tmp_name"],$logo_newname);
               
                $url = site_url($logo_newname);
             
                $insert = $conn->prepare("INSERT INTO files SET link=:link, date=:date");      
                $insert = $insert->execute(array("link"=>$url,"date"=>date("Y-m-d H:i:s")));
                 
          endif;
          
                endif;
        
                $fileList = $conn->prepare("SELECT * FROM files ORDER BY date DESC ");
                $fileList->execute(array());
                $fileList = $fileList->fetchAll(PDO::FETCH_ASSOC);
                
                //1
                if( route(3) == "delete" ):
                    $id = route(4);
                    
                    if( countRow(["table"=>"files","where"=>["id"=>$id]]) ):
                        $delete = $conn->prepare("DELETE FROM files WHERE id=:id ");
                        $delete->execute(array("id"=>$id));
                    endif;
                    
                    header("Location:".site_url("admin/appearance/files"));
                    exit();
                endif;
                //1

  endif;

      if( route(5) ): header("Location:".site_url("admin/appearance/files")); 
 endif;
 
 
 
   

 if( route(2) == "new_year" ):
   $access = $admin["access"]["pages"];

      if( $access ):
        if( $_POST ):
          foreach ($_POST as $key => $value) {
            $$key = $value;
          }
          $conn->beginTransaction();
          $update = $conn->prepare("UPDATE decoration SET 
          fire_works=:fire_works,
          snowflakes=:snowflakes,
          garlands=:garlands,
          toys=:toys,
          toy_a=:toy_a,
          toy_b=:toy_b,
          toy_c=:toy_c,
          toy_d=:toy_d,
          toy_e=:toy_e,
          toy_f=:toy_f,
          toy_g=:toy_g,
          toy_h=:toy_h,
          toy_i=:toy_i,
          toy_j=:toy_j,
          toy_k=:toy_k,
          toy_l=:toy_l,
          toy_size=:toy_size,
          toy_quantity=:toy_quantity,
          toy_speed=:toy_speed,
          toy_launch=:toy_launch,
          fire_speed=:fire_speed,
          fire_size=:fire_size,
          gar_style=:gar_style,
          gar_shape=:gar_shape,
          snow_speed=:snow_speed,
          snowflakes=:snowflakes,
          snow_fall=:snow_fall
          WHERE id=:id ");
          $update = $update->execute(array("id"=>1,"snow_fall" => $snow_fall,
          "garlands" => $garlands,
          "fire_works" => $fire_works,
          "fire_speed" => $fire_speed,
          "fire_size" => $fire_size,
          "gar_style" => $gar_style,
          "gar_shape" => $gar_shape,
          "snow_speed" => $snow_speed,
          "snowflakes" => $snowflakes,
          "toys" => $toys,
          "toy_a" => $toy_a,
          "toy_b" => $toy_b,
          "toy_c" => $toy_c,
          "toy_d" => $toy_d,
          "toy_e" => $toy_e,
          "toy_f" => $toy_f,
          "toy_g" => $toy_g,
          "toy_h" => $toy_h,
          "toy_i" => $toy_i,
          "toy_j" => $toy_j,
          "toy_k" => $toy_k,
          "toy_l" => $toy_l,
          "toy_size" => $toy_size,
          "toy_quantity" => $toy_quantity,
          "toy_speed" => $toy_speed,
          "toy_launch" => $toy_launch));
          if( $update ):
            $conn->commit();
            header("Location:".site_url("admin/appearance/new_year"));
            $_SESSION["client"]["data"]["success"]    = 1;
            $_SESSION["client"]["data"]["successText"]= "Success";
          else:
            $conn->rollBack();
            $error    = 1;
            $errorText= "Failed";
          endif;
        endif;
      endif; endif;
      
 
 
      
  endif;

  require admin_view('appearance');
