<?php include 'header.php'; ?>

<style>
    .custom-disabled-input {
  position: relative;
}

.custom-disabled-input:disabled {
  background-color: #f9f9f9; /* Color de fondo para el input deshabilitado */
}

.custom-disabled-input:disabled::after {
  content: '✕'; /* Carácter 'x' para la apariencia de la x roja */
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: red; /* Color rojo para la x */
}/* Estilos para el span redondo */
.round-span {
    display: inline-block;
    width: 10px; /* Ancho del span */
    height: 10px; /* Altura del span */
    border-radius: 50%; /* Hace que el span sea redondo */
}

/* Estilos para el span redondo verde */
.green {
    background-color: green; /* Color de fondo verde */
}
.red {
    background-color: red; /* Color de fondo verde */
}
</style>

<div class="container-fluid">
            <?php if( $success ): ?>
          <div class="alert alert-success "><?php echo $successText; ?></div>
        <?php endif; ?>
           <?php if( $error ): ?>
          <div class="alert alert-danger "><?php echo $errorText; ?></div>
        <?php endif; ?>
   <ul class="nav nav-tabs nav-tabs__service">
      <li class="p-b"><button class="btn btn-default" data-toggle="modal" data-target="#modalDiv" data-action="new_service">Añadir Servicio</button></li>
      <li class="p-b"><button class="btn btn-default m-l" data-toggle="modal" data-target="#modalDiv" data-action="new_subscriptions">Añadir suscripcion</button></li>
      <li class="p-b"><button class="btn btn-default m-l" data-toggle="modal" data-target="#modalDiv" data-action="new_category">Crear Categoria</button></li>

	  
      <li class="pull-right">
         <div class="form-inline"><label for="service-search-input" class="service-search__icon"></label>
           <input class="form-control" placeholder="Search for service..." id="priceService" type="text" value="">
         </div>
      </li>
      <li class="pull-right">
        <a data-toggle="modal" data-target="#modalDiv" data-action="import_services"><i class="fas fa-plus-circle"></i> Importar Servicios Sin Categorias </a>
      </li>
<li class="pull-right">
        <a data-toggle="modal" data-target="#modalDiv" data-action="import_service"><i class="fas fa-plus-circle"></i> Importar Servicios Con Categorias </a>
      </li>
   </ul>

   <div class="sticker-head">
      <table class="service-block__header" id="sticker">
         <thead>
             <th class="checkAll-th service-block__checker null">
                 <div class="checkAll-holder">
                    <input type="checkbox" id="checkAll">
                    <input type="hidden" id="checkAllText" value="order">
                  </div>
                  <div class="action-block">
                    <ul class="action-list">
                       <li><span class="countOrders"></span> Seleccionados</li>
                       <li>
                         <div class="dropdown">
                           <button type="button" class="btn btn-default btn-xs dropdown-toggle btn-xs-caret" data-toggle="dropdown"> batch operations <span class="caret"></span></button>
                           <ul class="dropdown-menu">
                             <li>
                                <a class="bulkorder" data-type="active">Activar </a>
                                <a class="bulkorder" data-type="deactive">Desactivar </a>
                                <a class="bulkorder" data-type="del_price">Resetear  </a>
                                <a class="bulkorder" data-type="del_service">Borrar </a>
                                </li>
                           </ul>
                         </div>
                       </li>
                     </ul>
                  </div>
               </th>
               <th class="service-block__id">ID</th>
               <th class="service-block__service">Nombre de Servicio</th>
               <th class="service-block__type">
               Service Type
               </th>
               <th class="service-block__provider">Proveedor</th>
               <th class="service-block__rate">Precio</th>
               <th class="service-block__minorder">Min</th>
               <th class="service-block__minorder">Max</th>
               <th class="service-block__visibility">Estado</th>
               <th class="service-block__action text-right"><span id="allServices" class="service-block__hide-all fa fa-compress"></span></th>
            </tr>
         </thead>
      </table>
   </div>

   <div class="service-block__body">
      <div class="service-block__body-scroll">
            <div style="width: 100%; height: 0px;"></div>
            <form action="<?php echo site_url("admin/services/multi-action") ?>" method="post" id="changebulkForm">
            <div style="" class="category-sortable">
              <?php $c=0;foreach($serviceList as $category => $services ): $c++; ?>
                <div class="categories" data-id="<?=$services[0]["category_id"]?>">
                  <div class="<?php if( $services[0]["category_type"] == 1 ): echo 'grey'; endif;?>  service-block__category ">
                     <div class="service-block__category-title"  class="categorySortable" data-category="<?=$category?>" id="category-<?=$c?>">
                        <div class="service-block__drag handle">
                           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                              <title>Drag-Handle</title>
                              <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"></path>
                           </svg>
                        </div>
                        <?php if( $services[0]["category_secret"] == 1 ): echo '<small data-toggle="tooltip" data-placement="top" title="" data-original-title="gizli kategori"><i class="fa fa-lock"></i></small> '; endif; echo $category; ?>
                        <div class="dropdown-inline">
                          <div class="dropdown">
                            <button type="button" class="btn btn-default btn-xs dropdown-toggle btn-xs-caret" data-toggle="dropdown">Opciones <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                              <li><a style="cursor:pointer;"  data-toggle="modal" data-target="#modalDiv" data-action="edit_category" data-id="<?=$services[0]["category_id"]?>">Editar Categoria</a></li>
                                <?php if( $services[0]["category_type"] == 1 ): $type = "category-active"; else: $type = "category-deactive"; endif; ?>
                              <li><a href="<?php echo site_url("admin/services/".$type."/".$services[0]["category_id"]) ?>"><?php if( $services[0]["category_type"] == 1 ): echo "Activar Categoria"; else: echo "Desactivar Categoria"; endif; ?></a></li>
                             
                              <li><a href="<?php echo site_url("admin/services/del_cate/".$services[0]["category_id"]) ?>">Eliminar Categoria</a></li>
                             
                            </ul>
                          </div>
                          </div>
                        <?php if( !empty($services[0]["service_id"]) ): 
                        
                        ?>

                          <div class="service-block__collapse-block"><div id="collapedAdd-<?=$c?>" class="service-block__collapse-button" data-category="category-<?=$c?>"></div></div>
                        <?php endif; ?>
                     </div>
                     <div class="collapse in">
                        <div class="service-block__packages">
                           <table id="servicesTableList" class="Servicecategory-<?=$c?>">
                              <tbody class="service-sortable">
                                  <div class="serviceSortable" id="Servicecategory-<?=$c?>" data-id="category-<?=$c?>">
                                    <?php for($i=0;$i<count($services);$i++): $api_detail = json_decode($services[$i]["api_detail"],true); ?>
                                       <tr id="serviceshowcategory-<?=$c?>" class="ui-state-default <?php if( $services[$i]["service_type"] == 1 ): echo "grey"; endif; ?>"  data-category="category-<?=$c?>" data-id="service-<?php echo $services[$i]["service_id"] ?>" data-service="<?php echo $services[$i]["service_name"] ?>">
                                         <?php if( !empty($services[0]["service_id"])  ):?>
                                            <td class="service-block__checker">
                                              <?php if($services[$i]["api_servicetype"]==1): echo '<div class="service-block__danger"></div>'; endif;?>
                                               <span></span>
                                               <div class="service-block__checkbox">
                                                  <div class="service-block__drag handle">
                                                     <svg>
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                           <title>Drag-Handle</title>
                                                           <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"></path>
                                                        </svg>
                                                     </svg>
                                                  </div>
                                                  <input type="checkbox" class="selectOrder" name="service[<?php echo $services[$i]["service_id"] ?>]" value="1" style="border:1px solid #fff">
                                               </div>
                                            </td>
                                            <td class="service-block__id"><?php echo $services[$i]["service_id"] ?></td>
                                            <td class="service-block__service"><?php if( $services[$i]["service_secret"] == 1 ): echo '<small data-toggle="tooltip" data-placement="top" title="" data-original-title="secret service"><i class="fa fa-lock"></i></small> '; endif; echo $services[$i]["service_name"]; ?></td>
                                            <td class="service-block__type" nowrap=""><?php echo servicePackageType($services[$i]["service_package"]); if( $services[$i]["service_dripfeed"] == 2 ): echo ' <i class="fa fa-tint"></i>'; endif; ?></td>
                                            <td class="service-block__provider"><?php if( $services[$i]["service_api"] != 0 ): echo $services[$i]["api_name"]; else: echo "Manuel"; endif;  ?> <?php if( $services[$i]["service_api"] != 0 ): echo '<div class="service-block__provider-value">'.$services[$i]["api_service"].'</div>'; endif; ?></td>
                                            <td class="service-block__rate">
                                              <?php
                                           
                                                  $api_price  = $api_detail["rate"];
                                          
                                              ?>
                                               <div style="<?php if( !$api_detail["rate"] ): echo ""; elseif( $services[$i]["service_api"] != 0 && $services[$i]["service_price"] > $api_price ): echo "color: blue"; elseif( $services[$i]["service_api"] != 0 && $services[$i]["service_price"] < $api_price ): echo "color: blue"; endif ?>"><?php echo conrate($services[$i]["service_price"]) ?></div>

                                               <?php if( $services[$i]["service_api"] != 0 && $api_detail["rate"] ): echo '<div class="service-block__provider-value"><i class="fa fa-'.strtolower($api_detail["currency"]).'"></i> '.priceFormat($api_detail["rate"]).'</div>'; endif; ?>
                                            </td>
                                            <td class="service-block__minorder">
                                               <div><span class="service-sync__wrap"><?php if( $services[$i]["sync_min"] == 1): echo'<div class="service-sync__icon"></div>'; endif; echo $services[$i]["service_min"] ?></span></div>
                                               <?php 
                                              if( $services[$i]["sync_min"] == 0):
                                               if( $services[$i]["service_api"] != 0 ): echo '<div class="service-block__provider-value">'.$api_detail["min"].'</div>'; endif;
                                               endif;
                                               ?>
                                            </td>
                                            <td class="service-block__minorder">
                                               <div><span class="service-sync__wrap"><?php if( $services[$i]["sync_max"] == 1): echo'<div class="service-sync__icon"></div>'; endif; echo $services[$i]["service_max"] ?></span></div>
                                               <?php 
                                               if( $services[$i]["sync_max"] == 0):
                                               if( $services[$i]["service_api"] != 0 ): echo '<div class="service-block__provider-value">'.$api_detail["max"].'</div>'; endif;
                                               endif; ?>
                                            </td>
                                          <td class="service-block__visibility">
    <?php 
    if ($services[$i]["service_type"] == 1) {
        echo "Caido ";
        echo '<span class="round-span red" title="Servicio caido"></span>';

    } else {
        echo "Activo";
    }
    
    if ($services[$i]["api_servicetype"] == 1) {
        // Agregar un span rojo en caso de servicio caído
        echo '<span class="round-span red" title="Service is down"></span>';
    } else {
        // Agregar un span verde en caso de servicio activo
        echo '<span class="round-span green" style="    margin-left: 4px;
" title="Service is active"></span>';
    }
    ?>
</td>
                                            <td class="service-block__action">
                                              <div class="dropdown pull-right">
                                                <button type="button" class="btn btn-default btn-xs dropdown-toggle btn-xs-caret" data-toggle="dropdown">Options <span class="caret"></span></button>
                                                <ul class="dropdown-menu">
                                                  <li><a style="cursor:pointer;"  data-toggle="modal" data-target="#modalDiv" data-action="edit_service" data-id="<?=$services[$i]["service_id"]?>">Editar Servicio</a></li>
                                                  <li><a style="cursor:pointer;"  data-toggle="modal" data-target="#modalDiv" data-action="edit_description" data-id="<?=$services[$i]["service_id"]?>">Editar Description</a></li>
                                                    <?php if( $services[$i]["service_type"] == 1 ): $type = "service-active"; else: $type = "service-deactive"; endif; ?>
                                                  <li><a href="<?php echo site_url("admin/services/".$type."/".$services[$i]["service_id"]) ?>"><?php if( $services[$i]["service_type"] == 1 ): echo "Activate Service"; else: echo "Disable Service"; endif; ?></a></li>
                                                  <li><a href="<?php echo site_url("admin/services/del_price/".$services[$i]["service_id"]) ?>">Resetear Descuentos</a></li>
                                                  <li><a href="#"data-toggle="modal" data-target="#confirmChange" data-href="<?php echo site_url("admin/services/delete/".$services[$i]["service_id"]) ?>">Eliminar Servicio</a></li>
                                                     
                                                </ul>
                                              </div>
                                            </td>
                                          <?php endif; ?>
                                       </tr>
                                     <?php endfor; ?>
                                   </div>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
                </div>
              <?php endforeach; ?>

            

              <input type="hidden" name="bulkStatus" id="bulkStatus" value="-1">

            </form>
         </div>
      </div>
   </div>

</div>

<div class="modal modal-center fade" id="confirmChange" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
 <div class="modal-dialog modal-dialog-center" role="document">
   <div class="modal-content">
     <div class="modal-body text-center">
       <h4>Estas seguro de continuar ?</h4>
       <div align="center">
         <a class="btn btn-primary" href="" id="confirmYes">Yes</a>
         <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
       </div>
     </div>
   </div>
 </div>
</div>

  <div class="modal fade in" id="modalDiv" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog__import modal-dialog" role="document" id="modalSize">
     <div class="modal-content">
       <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
         <h4 class="modal-title" id="modalTitle"></h4>
       </div>
       <div id="modalContent">
       </div>
     </div>
   </div>
  </div>


<?php include 'footer.php'; ?>
