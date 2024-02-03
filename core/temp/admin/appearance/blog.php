<?php if( !route(4) ): ?>
<div class="col-md-8">
              <?php if( $success ): ?>
          <div class="alert alert-success "><?php echo $successText; ?></div>
        <?php endif; ?>
           <?php if( $error ): ?>
          <div class="alert alert-danger "><?php echo $errorText; ?></div>
        <?php endif; ?>
       <ul class="nav nav-tabs">
   <a href="javascript:;" onclick="showMe('gizlebeni');" ><li class="p-b"><button class="btn btn-default">Create New Blog Post</button></li></a></ul>
        <table class="table">
            <thead>
            <tr>
                <th>Blog Name</th>
                <th>Release date</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
         <?php foreach($postList as $post): ?>

                            <tr>
                <td>
                   <?php echo $post["blog_title"]; ?> <a href="/blog/<?php echo $post["url"]; ?> " class="order-link" target="_blank">
                                <span class="fa fa-external-link"></span>
                            </a> 

                </td>
                   <td>
                   <?php echo $post["blog_created"]; ?>

                </td>
                
                <td class="service-block__action">
                   <div class="dropdown pull-right">
                     <button type="button" class="btn btn-default btn-xs dropdown-toggle btn-xs-caret" data-toggle="dropdown">Transactions <span class="caret"></span></button>
                     <ul class="dropdown-menu">
                      
                         <li><a href="<?php echo site_url('admin/appearance/blog/edit/'.$post["id"]) ?>">Edit</a></li>
                     
                         <li><a href="<?php echo site_url('admin/appearance/blog/delete/'.$post["id"]) ?>">delete</a></li>

               
                     </ul>
                   </div>
                 </td>

            </tr> 

         <?php endforeach; ?>           
          
                        </tbody>
        </table>
      </tbody>
   </table>
<br>
          <div class="panel panel-default" id="gizlebeni" style="display: none;">
    <div class="panel-body">

         <form action="<?php echo site_url('admin/appearance/blog') ?>" method="post" enctype="multipart/form-data">
             
                     <div class="form-group relative">
          <div class="row">
            <div class="col-md-10">
              <label for="preferenceLogo" class="control-label">Blog Picture</label>
              <input type="file" name="logo" id="preferenceLogo">
                        <p class="help-block">800 x 450px are recommended sizes</p>
            </div>
           
          </div>
        </div>
             
        <div class="form-group">
          <label for="" class="control-label">Blog Name</label>
          <input type="text" class="form-control" name="name">
        </div>

            <div class="form-group">
               <label class="control-label">Blog Content</label>
               <textarea class="form-control" id="summernote" rows="5" name="content" placeholder=""></textarea>
            </div>	  

            <button type="submit" class="btn btn-primary">Publish the Blog</button>
                <a href="/admin/appearance/blog" class="btn btn-default">Back</a>
         </form>

</div> </div>



</div>

<script type="text/javascript">
// göster/gizle
function showMe(blockId) {
     if ( document.getElementById(blockId).style.display == 'none' ) {
          document.getElementById(blockId).style.display = ''; }
else if ( document.getElementById(blockId).style.display == '' ) {
          document.getElementById(blockId).style.display = 'none'; }
}
</script>

<?php elseif( route(3) == "edit" ): ?>
<div class="col-md-8">
            <?php if( $success ): ?>
          <div class="alert alert-success "><?php echo $successText; ?></div>
        <?php endif; ?>
           <?php if( $error ): ?>
          <div class="alert alert-danger "><?php echo $errorText; ?></div>
        <?php endif; ?>
          <div class="panel panel-default">
    <div class="panel-body">

         <form action="<?php echo site_url('admin/appearance/blog/edit/'.route(4)) ?>" method="post" enctype="multipart/form-data">
             
                     <div class="form-group relative">
          <div class="row">
            <div class="col-md-10">
              <label for="preferenceLogo" class="control-label">Blog Resmi</label>
              <input type="file" name="logo" id="preferenceLogo">
                        <p class="help-block">800 x 450px önerilen boyutlardır</p>
            </div>
            <div class="col-md-2">
              <?php if( $post["blog_image"] ):  ?>
                <div class="setting-block__image">
                      <img class="img-thumbnail" src="<?=$post["blog_image"]?>">
                    <div class="setting-block__image-remove">
                      <a href="" data-toggle="modal" data-target="#confirmChange" data-href="<?=site_url("admin/settings/blog/delete-image")?>"><span class="fa fa-remove"></span></a>
                    </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
             
        <div class="form-group ">
          <label for="" class="control-label">Blog Adı</label>
          <input type="text" class="form-control" name="name" value="<?=$post["blog_title"]?>">
        </div>

            <div class="form-group">
               <label class="control-label">Blog İçeriği</label>
               <textarea class="form-control" id="summernote" rows="5" name="content" placeholder=""><?php echo $post["blog_content"]; ?></textarea>
            </div>	  

            <button type="submit" class="btn btn-primary">Güncelle</button>
                <a href="/admin/appearance/blog" class="btn btn-default">Geri</a>
         </form>

</div> </div> </div> 


<?php endif; ?>
 