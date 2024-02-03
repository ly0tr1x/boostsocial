<html lang="tr" <?php if($user['admin_theme'] == 2){ echo 'class="dark"'; } ?> >
    <!-- BEGIN: Head -->
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Glycon System Manager</title>
        <!-- BEGIN: CSS Assets-->
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <!-- END: CSS Assets-->        
    </head>
  
  <style>
    img.rounded-full.shadow-md {
    max-width: 50px;
}
.manager-active {
    padding: 8px;
    background: #21b9bd;
    border-radius: 10px;
    color: white!important;
    margin: 0px 10px;
}
  </style>
    <body class="app">
      
      <nav class="navbar navbar-expand-lg navbar-light bg-light shadow mb-5">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav mx-auto">        
        <li class="nav-item border-end">
          <a class="nav-link <?php if(!route(2)){ $landing = true;} if( route(2) == 'update' || $landing): echo 'manager-active'; endif; ?>" href="/admin/manager">Güncellemeler</a>
        </li>
        <li class="nav-item border-end">
          <a class="nav-link <?php if( route(2) == 'guard' ): echo 'manager-active'; endif; ?>" href="/admin/manager/guard">Koruma Ayarları</a>
        </li>
        <li class="nav-item border-end">
          <a class="nav-link <?php if( route(2) == 'details' ): echo 'manager-active'; endif; ?>" href="/admin/manager/details">Detaylar</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php if( route(2) == 'info' ): echo 'manager-active'; endif; ?>" href="/admin/manager/info">Panel Hakkında</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
      
      
      
      
      <!--
        <div class="top-bar top-0 left-0 fixed w-full h-16">
            <div class="-intro-y top-bar__content border-b w-full h-full flex px-5">
                
                <a class="hidden md:flex items-center h-full mr-auto" href="/admin/manager">
                    <img alt="Glycon" class="h-8" src="https://res.cloudinary.com/glycon/image/upload/v1600124924/Favicon_e3zlyq.png">
                    <div class="text-base font-light ml-4"> <span class="font-medium">Glycon</span> Manager </div>
                </a>
                <a class="mobile-menu-toggler flex md:hidden items-center h-full mr-auto px-5 -ml-5" href="javascript:;"> <i data-feather="bar-chart-2" class="w-5 h-5 transform rotate-90"></i> </a>

                <div class="account-dropdown dropdown relative">
                    <a href="javascript:;" class="h-full dropdown-toggle flex items-center pl-5">
                        <div class="w-8 h-8 image-fit">
                            <img alt="Glycon" class="rounded-full shadow-md" src="https://image.flaticon.com/icons/png/512/3237/3237472.png">
                        </div>
                    </a>
                    <div class="dropdown-content dropdown-box absolute w-56 top-0 right-0 z-20">
                        <div class="dropdown-box__content box">
                            <div class="dropdown-content__footer p-2 border-t">
                                <a href="/admin/manager/logout" class="flex items-center block p-2 transition duration-300 ease-in-out rounded-md"> <i data-feather="log-out" class="w-4 h-4 mr-2"></i> Manger'ı Sonlandır</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
        <div class="side-menu hidden md:block top-0 left-0 fixed w-16 h-screen">
            <div class="side-menu__content -intro-x border-r w-full h-full pt-16 flex flex-col justify-center overflow-hidden">
                <a class="-intro-x side-menu__content__link relative tooltip py-5 <?php if(!route(2)){ $landing = true;} if( route(2) == 'update' || $landing): echo 'side-menu__content__link--active'; endif; ?>" href="/admin/manager/update" data-side="right" title="Güncellemeler" data-content="chats"> <i data-feather="download-cloud" class="w-5 h-5 mx-auto"></i> </a>
                <a class="-intro-x side-menu__content__link relative tooltip py-5  <?php if( route(2) == 'guard' ): echo 'side-menu__content__link--active'; endif; ?>" href="/admin/manager/guard" data-side="right" title="Koruma Ayarları"> <i data-feather="shield" class="w-5 h-5 mx-auto"></i> </a>          
                <a class="-intro-x side-menu__content__link relative tooltip py-5 <?php if( route(2) == 'details' ): echo 'side-menu__content__link--active'; endif; ?>" href="/admin/manager/details" data-side="right" title="Detaylar"> <i data-feather="bar-chart" class="w-5 h-5 mx-auto"></i> </a>
                <a class="-intro-x side-menu__content__link relative tooltip py-5 <?php if( route(2) == 'info' ): echo 'side-menu__content__link--active'; endif; ?>" href="/admin/manager/info" data-side="right" title="Panel Hakkında"> <i data-feather="info" class="w-5 h-5 mx-auto"></i> </a>
            </div>
        </div>
        -->