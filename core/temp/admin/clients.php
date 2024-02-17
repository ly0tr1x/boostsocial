<?php include 'header.php'; ?>

<div class="container-fluid">
            <?php if( $success ): ?>
          <div class="alert alert-success "><?php echo $successText; ?></div>
        <?php endif; ?>
           <?php if( $error ): ?>
          <div class="alert alert-danger "><?php echo $errorText; ?></div>
        <?php endif; ?>
  <ul class="nav nav-tabs nav-tabs__service">
   <li class="p-b"><button class="btn btn-default" type="button" data-toggle="modal" data-target="#modalDiv" data-action="new_user">Añadir usuario</button></li>
   <li class="p-b"><button class="btn btn-default  m-l" type="button" data-toggle="modal" data-target="#modalDiv" data-action="alert_user">Enviar notifiacion</button></li>
     <li class="p-b"><button class="btn btn-default  m-l" type="button" data-toggle="modal" data-target="#modalDiv" data-action="all_numbers">Contacto</button></li>
     
   <li class="pull-right p-b">
          <form class="form-inline" action="" method="get" enctype="multipart/form-data">
       <div class="input-group">
         <input type="text" name="search" class="form-control" value="<?=$search_word?>" placeholder="Buscar usuario...">
         <span class="input-group-btn search-select-wrap">
             <select class="form-control search-select" name="search_type">
               <option value="username" <?php if( $search_where == "username" ): echo 'selected'; endif; ?> >Usuario</option>
               <option value="name" <?php if( $search_where == "name" ): echo 'selected'; endif; ?> >Nombre</option>
               <option value="email" <?php if( $search_where == "email" ): echo 'selected'; endif; ?> >Email</option>
               <option value="telephone" <?php if( $search_where == "telephone" ): echo 'selected'; endif; ?> >Telefono</option>
             </select>
             <button type="submit" class="btn btn-default"><span class="fa fa-search" aria-hidden="true"></span></button>
           </span>
       </div>
     </form>
    </li>
       <li class="pull-right p-b"><a data-toggle="modal" data-target="#modalDiv" data-action="export_user">Exportar</a></li>
 </ul>
    <table class="table">
      <thead>
      <tr>
        <th class="column-id">ID</th>
         <th>Uusario</th> 
         <th>Email</th>
    <?php if( $settings["skype_area"] == 2 ): echo " <th>Telefono</th>"; endif; ?>
        <th>Balance</th>
        <th>Gastado</th>
        <th>Estado</th>
        <th>Fecha Registro</th>
        <th nowrap="">Ultimo inicio</th>
        <th>Precio Especial</th>
        <th>Bulk Discount</th>
        <th>Accion</th>
      </tr>
      </thead>
        <tbody>

          <?php foreach($clients as $client ):  ?>
            <tr class="<?php if( $client["client_type"] == 1 ): echo "grey"; endif; ?>">
               <td><?php echo $client["client_id"] ?></td>
               <td><?php echo $client["username"] ?></td>
               <td><?php echo $client["email"] ?></td>
        <?php if( $settings["skype_area"] == 2 ): echo "<td>"; echo $client["telephone"]; echo "</td>"; endif; ?>
               <td><?php echo conrate($client["balance"]) ?></td>
               <td><?php echo conrate($client["spent"]) ?></td>
               <td><?php if( $client["client_type"] == 2 ): echo "Activate"; else: echo "Suspend"; endif; ?></td>
               <td><?php echo $client["register_date"] ?></td>
               <td><?php echo $client["login_date"] ?></td>
               
                <td><button type="button" class="btn btn-default btn-xs <?php if( !countRow(["table"=>"clients_price","where"=>["client_id"=>$client["client_id"]] ]) ): echo "disabled"; endif; ?> " style="cursor:pointer"  data-toggle="modal" data-target="#modalDiv" data-id="<?php echo $client["client_id"] ?>" data-action="price_user">Set Discount
               
                             <?php if( countRow(["table"=>"clients_price","where"=>["client_id"=>$client["client_id"]] ]) ): ?>  <span class="badge badge-xs"><?php echo countRow(["table"=>"clients_price","where"=>["client_id"=>$client["client_id"]] ]) ?></span><?php endif; ?>
 
               
            </button></td>
               
               <td>
                                                        <button type="button" class="btn btn-default btn-xs disabled " style="cursor:pointer"href="#" class="dcs-pointer" data-toggle="modal" data-target="#modalDiv" data-id="<?php echo $client["client_id"] ?>" data-action="coustm_rate">Discount (<?php echo $client ["coustm_rate"]; ?>%)</button>
                                                    </td>
               
               
               
               
               <td class="td-caret">
                 <div class="dropdown pull-right">
                   <button type="button" class="btn btn-default btn-xs dropdown-toggle btn-xs-caret" data-toggle="dropdown">Options <span class="caret"></span></button>
                   <ul class="dropdown-menu">
                     <li><a style="cursor:pointer;"  data-toggle="modal" data-target="#modalDiv" data-action="edit_user" data-id="<?=$client["client_id"]?>">Editar Usuario</a></li>
                   <?php if( $client["client_type"] == 2 ): ?>  <li><a href="<?php echo site_url("admin/clients/login/".$client["client_id"]) ?>">Abrir como</a></li> <?php endif; ?>

                     <li><a style="cursor:pointer;"  data-toggle="modal" data-target="#modalDiv" data-action="pass_user" data-id="<?=$client["client_id"]?>">Cambiar Contraseña</a></li>
                     <li><a style="cursor:pointer;"  data-toggle="modal" data-target="#modalDiv" data-action="secret_user" data-id="<?=$client["client_id"]?>">Special Categories</a></li>
                 
                     <?php if( $client["client_type"] == 1 ): $type = "active"; else: $type = "deactive"; endif; ?>
                     <li><a href="<?php echo site_url("admin/clients/".$type."/".$client["client_id"]) ?>"><?php if( $client["client_type"] == 1 ): echo "Activate account"; else: echo "Suspend account"; endif; ?> </a></li>
                     <li><a href="<?php echo site_url("admin/clients/del_price/".$client["client_id"]) ?>">Eliminar Descuentos</a></li>
                   </ul>
                 </div>
               </td>
             </tr>
          <?php endforeach; ?>

        </tbody>
    </table>

    <?php if( $paginationArr["count"] > 1 ): ?>
      <nav>
        <ul class="pagination">
          <?php if( $paginationArr["current"] != 1 ): ?>
            <li class="page-item"><a class="page-link" href="<?php echo site_url("admin/clients/1".$search_link) ?>">&laquo;</a></li>
            <li class="page-item"><a class="page-link" href="<?php echo site_url("admin/clients/".$paginationArr["previous"].$search_link) ?>">&lsaquo;</a></li>
          <?php
              endif;
              for ($page=1; $page<=$pageCount; $page++):
                if( $page >= ($paginationArr['current']-9) and $page <= ($paginationArr['current']+9) ):
          ?>
            <li class="page-item <?php if( $page == $paginationArr["current"] ): echo "active"; endif; ?> ">
              <a class="page-link" href="<?php echo site_url("admin/clients/".$page.$search_link) ?>"><?php echo $page ?></a>
            </li>
          <?php endif; endfor;
                if( $paginationArr["current"] != $paginationArr["count"] ):
          ?>
            <li class="page-item"><a class="page-link" href="<?php echo site_url("admin/clients/".$paginationArr["next"].$search_link) ?>">&rsaquo;</a></li>
            <li class="page-item"><a class="page-link" href="<?php echo site_url("admin/clients/".$paginationArr["count"].$search_link) ?>">&raquo;</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    <?php endif; ?>


</div>

<?php include 'footer.php'; ?>
