<?php if($_SESSION["glycon_manager"] != "logined"): ?>

<!DOCTYPE html>
<html lang="tr" <?php if($user['admin_theme'] == 2){ echo 'class="dark"'; } ?>>

    <head>
        <meta charset="utf-8">
        <link href="https://res.cloudinary.com/glycon/image/upload/v1600124924/Favicon_e3zlyq.png" rel="shortcut icon">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Giriş - Glycon Manager</title>
        <!-- BEGIN: CSS Assets-->
        <link rel="stylesheet" href="https://res.cloudinary.com/glycon/raw/upload/v1600016066/app_fzk4bs_dsbdau.css" />
        <!-- END: CSS Assets-->
      <style>
          .modal-body iframe {
    background: #f7f7f7;
}

.p-5 {
    padding: 3rem;
}

img.intro-y.mx-auto.w-16 {
    text-align: center;
    margin: auto;
    margin-bottom: 3rem;
}

.intro-y.auth {
    text-align: center;
}

.intro-y.auth__title.text-2xl.font-medium.text-center.mt-16 {
    font-size: 30px;
    font-weight: bold;
    color: #000000;
    margin-bottom: 3rem;
    font-family: Nunito;
}

input.intro-y.auth__input.input.input--lg.w-full.border.block {
    display: block;
    padding: .375rem .75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    -webkit-appearance: none;
    -moz-appearance: none;
    margin: auto;
    min-width: 350px;
    appearance: none;
    margin-bottom: 1rem;
    border-radius: .25rem;
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}

button.intro-y.button.button--lg.button--primary.w-full.xl\:mr-3 {
    display: inline-block;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    text-align: center;
    text-decoration: none;
    vertical-align: middle;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    user-select: none;
    background-color: transparent;
    border: 1px solid transparent;
    padding: .375rem .75rem;
    font-size: 1rem;
    border-radius: .25rem;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    color: #fff;
    background-color: #3dd5f3;
    border-color: #25cff2;
    min-width: 375px;
    font-weight: b;
}
      </style>
    </head>
    <!-- END: Head -->
    <body>
        <div class="w-full min-h-screen p-5 md:p-20 flex items-center justify-center">
            <div class="intro-y auth">
                <img class="intro-y mx-auto w-16" alt="Glycon" src="https://www.glycon.com.tr/resources/uploads/logo/2021-06-08/glycon-bilgi-teknolojileri-yazilim-hosting-alan-adi-ddos-koruma-hizmetleri.png">
                <div class="intro-y auth__title text-2xl font-medium text-center mt-16">Glycon Manager - Giriş</div>
                <div class="intro-y box px-5 py-8 mt-8">
                    <form method="post" action="/admin/manager/login">
                    <div class="intro-y">
                        <input type="text" class="intro-y auth__input input input--lg w-full border block" name="key" placeholder="Lisans Kodu">
                    </div>
                    <div class="intro-y mt-5 xl:mt-8 text-center xl:text-left">
                        <button class="intro-y button button--lg button--primary w-full xl:mr-3">Giriş Yap</button>
                    
                    </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- BEGIN: JS Assets-->
        <script src="https://res.cloudinary.com/glycon/raw/upload/v1600016066/app_arjhlo_kuhi2u.js"></script>
        <!-- END: JS Assets-->
    </body>
</html>


<?php  else:

include 'manager.header.php';

if($e_route == "update"){ ?>
    <style>
    .alert-message
{
    margin: 20px 0;
    padding: 20px;
    border-left: 3px solid #eee;
}
.alert-message h4
{
    margin-top: 0;
    margin-bottom: 5px;
}
.alert-message p:last-child
{
    margin-bottom: 0;
}
.alert-message code
{
    background-color: #fff;
    border-radius: 3px;
}
.alert-message-success
{
    background-color: #F4FDF0;
    border-color: #3C763D;
}
.alert-message-success h4
{
    color: #3C763D;
}
.alert-message-danger
{
    background-color: #fdf7f7;
    border-color: #d9534f;
}
.alert-message-danger h4
{
    color: #d9534f;
}
.alert-message-warning
{
    background-color: #fcf8f2;
    border-color: #f0ad4e;
}
.alert-message-warning h4
{
    color: #f0ad4e;
}
.alert-message-info
{
    background-color: #f4f8fa;
    border-color: #5bc0de;
}
.alert-message-info h4
{
    color: #5bc0de;
}
.alert-message-default
{
    background-color: #EEE;
    border-color: #B4B4B4;
}
.alert-message-default h4
{
    color: #000;
}
.alert-message-notice
{
    background-color: #FCFCDD;
    border-color: #BDBD89;
}
.alert-message-notice h4
{
    color: #444;
}
</style>

        <div class="container">
            <div class="w-full md:w-full lg:w-3/4 p-5 mx-auto card shadow">
                <div class="intro-y text-xl font-medium fs-2">Güncellemeler</div>
                <div class="settings grid grid-cols-12 gap-6 mt-5">
                    <div class="intro-y col-span-12">

                        <div class="intro-y box">
                            <?php  
                            $site_version = VERSION_NUMBER;
$lisan_key_dinamik = DINAMICLISANCE; 
                            
                            //  Initiate curl
    $ch = curl_init();
    // Will return the response, if false it print the response
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Set the url
    curl_setopt($ch, CURLOPT_URL, "https://lisans.glycon.com.tr/version_check?key=$lisan_key_dinamik");
    // Execute
    $result = curl_exec($ch);
    // Closing
    curl_close($ch);
    
    // Will dump a beauty json :3
    $sonuc = json_decode($result, true);

if (isset($sonuc['status']) && $sonuc['status'] == 200): $version = $sonuc['version']; if($site_version != $version): ?>
                            
Devam etmeden önce tam bir yedek aldığınızdan emin olun. <br> Eğer yedek almadan güncelleme yaparsanız herhangi bir hata veya veri kaybı ile karşılaştığınızda sorumlusu siz olursunuz. <p> <br>

                                             Neden yedek almam gerekiyor? <br>
                                            * Tüm scriptler web tabanlıdır. Her şeyi kontrol etme gücüne sahip değil. <br>
                                            * Sunucunuzun internet bağlantısında bir kesinti olduğunda, otomatik güncelleme işlevleri devam edemez. Bu durumda dosyalar eksik olarak aktarılır. <br>
                                            * Zaman aşımı süreleri çok sınırlıdır, Bu nedenle timeout verebilir. <br><br>
                                            ** Bu etkenler scriptten kaynaklanan bir durum değildir.  <br>
                                            ** Hosting sağlayıcınızın koyduğu limitlerden kaynaklıdır. <br>
                                            <br>
<div class="flex items-center mr-auto">
Yukarıdaki bilgilendirmeleri okudum ve onaylıyorum.
                        </div>

                        </div>
    <form method="post" action="/admin/manager/update">
    <input type="hidden" name="_csrf" value="123">
<button type="submit" class="button button--primary w-full mt-3">Güncelle</button>

                                            </form>
                                            
            <?php  else: ?>

                      <p>Şu an v1 için en güncel sürümü kullanmaktasınız.</p>

                    <?php endif; endif; ?>
                                            
                                            
                    </div>
                </div>
            </div>
        </div>
        

<?php }elseif($e_route == "guard"){ ?>
<style>
    .alert-message
{
    margin: 20px 0;
    padding: 20px;
    border-left: 3px solid #eee;
}
.alert-message h4
{
    margin-top: 0;
    margin-bottom: 5px;
}
.alert-message p:last-child
{
    margin-bottom: 0;
}
.alert-message code
{
    background-color: #fff;
    border-radius: 3px;
}
.alert-message-success
{
    background-color: #F4FDF0;
    border-color: #3C763D;
}
.alert-message-success h4
{
    color: #3C763D;
}
.alert-message-danger
{
    background-color: #fdf7f7;
    border-color: #d9534f;
}
.alert-message-danger h4
{
    color: #d9534f;
}
.alert-message-warning
{
    background-color: #fcf8f2;
    border-color: #f0ad4e;
}
.alert-message-warning h4
{
    color: #f0ad4e;
}
.alert-message-info
{
    background-color: #f4f8fa;
    border-color: #5bc0de;
}
.alert-message-info h4
{
    color: #5bc0de;
}
.alert-message-default
{
    background-color: #EEE;
    border-color: #B4B4B4;
}
.alert-message-default h4
{
    color: #000;
}
.alert-message-notice
{
    background-color: #FCFCDD;
    border-color: #BDBD89;
}
.alert-message-notice h4
{
    color: #444;
}
</style>

       <div class="container">
            <div class="w-full md:w-full lg:w-3/4 p-5 mx-auto card shadow border">
                <div class="intro-y text-xl font-medium fs-2">Koruma Ayarları</div>
                <div class="settings grid grid-cols-12 gap-6 mt-5">
                    <div class="intro-y col-span-12">
                                   <?php if( $success ): ?>
 <div class="alert-message alert-message-success">
                <h4>İşlem Başarılı!</h4>
             
            </div>        <?php endif; ?>
           <?php if( $error ): ?>
          <div class="alert alert-danger "><?php echo $errorText; ?></div>
        <?php endif; ?>
                        <div class="intro-y box">
                            <form method="post" action="/admin/manager/guard">
             <div class="m*j*r">

              <label>Guard Sistemi</label>
              <select class="form-select" name="guard_system_status">
                <option value="2" <?php if( $settings["guard_system_status"] == 2 ): echo "selected"; endif; ?>>Aktif</option>
                <option value="1"  <?php if( $settings["guard_system_status"] == 1 ): echo "selected"; endif; ?>>Pasif</option>
              </select>
            </div>
            
        <hr>
        <div class="mt-3">

              <label>Servis Silme Koruması</label>
              <select class="form-select" name="guard_services_status">
                <option value="2"  <?php if( $settings["guard_services_status"] == 2 ): echo "selected"; endif; ?>>Aktif</option>
                <option value="1"  <?php if( $settings["guard_services_status"] == 1 ): echo "selected"; endif; ?>>Pasif</option>
              </select>
            </div>
        <div class="mt-3">

              <label>Eğer Servis Silerse</label>
              <select class="form-select" name="guard_services_type">
                <option value="2"  <?php if( $settings["guard_services_type"] == 2 ): echo "selected"; endif; ?>>Tüm Yetkilerini Al</option>
                <option value="1"  <?php if( $settings["guard_services_type"] == 1 ): echo "selected"; endif; ?>>Oturumunu Sonlandır</option>
              </select>
            </div>
<hr>
       <div class="mt-3">

              <label>Toplu Bildirim Koruması</label>
              <select class="form-select" name="guard_notify_status">
                <option value="2"  <?php if( $settings["guard_notify_status"] == 2 ): echo "selected"; endif; ?>>Aktif</option>
                <option value="1"  <?php if( $settings["guard_notify_status"] == 1 ): echo "selected"; endif; ?>>Pasif</option>
              </select>
            </div>
        <div class="mt-3">

              <label>Eğer Toplu Bildirim Gönderilirse</label>
              <select class="form-select" name="guard_notify_type">
                <option value="2"  <?php if( $settings["guard_notify_type"] == 2 ): echo "selected"; endif; ?>>Tüm Yetkilerini Al</option>
                <option value="1"  <?php if( $settings["guard_notify_type"] == 1 ): echo "selected"; endif; ?>>Oturumunu Sonlandır</option>
              </select>
            </div>
<hr>
       <div class="mt-3">

              <label>Yetki Koruması</label>
              <select class="form-select" name="guard_roles_status">
                <option value="2"  <?php if( $settings["guard_roles_status"] == 2 ): echo "selected"; endif; ?>>Aktif</option>
                <option value="1"  <?php if( $settings["guard_roles_status"] == 1 ): echo "selected"; endif; ?>>Pasif</option>
              </select>
            </div>
        <div class="mt-3">

              <label>Eğer Yetki Düzenlenirse</label>
              <select class="form-select" name="guard_roles_type">
                <option value="2"  <?php if( $settings["guard_roles_type"] == 2 ): echo "selected"; endif; ?>>Tüm Yetkilerini Al</option>
                <option value="1"  <?php if( $settings["guard_roles_type"] == 1 ): echo "selected"; endif; ?>>Oturumunu Sonlandır</option>
              </select>
            </div>
<hr>
       <div class="mt-3">

              <label>API Key Görüntüleme <small>(6 farklı şekilde şifrelenir ve kırılması imkansızdır.)</small></label>
              <select class="form-select" name="guard_apikey_type">
                <option value="2"  <?php if( $settings["guard_apikey_type"] == 2 ): echo "selected"; endif; ?>>Şifreli olarak göster</option>
                <option value="1"  <?php if( $settings["guard_apikey_type"] == 1 ): echo "selected"; endif; ?>>Direkt göster</option>
              </select>
            </div>
</div>
                            <button type="submit" class="btn btn-success mt-3">Güncelle</button>
                            </form>
                        </div>
                </div>
        </div>

<?php }elseif($e_route == "info"){ ?>
    
           <div class="container">
            <div class="w-full md:w-full lg:w-3/4 p-6 mx-auto card shadow border p-5">
                <div class="intro-y text-xl font-medium fs-2">Panel Hakkında</div>
                <div class="settings grid grid-cols-12 gap-6 mt-5">
                    <div class="intro-y col-span-12">
                        <div class="intro-y box">
                           Kullandığınız Sürüm; <?=$updateDetails['current']?><br><br>
                           Güncel Sürüm; <?=$updateDetails['last']?> 
                        </div>
                </div>

                    <div class="intro-y col-span-12">
                        <div class="intro-y box p-4">
** BİZİM ADIMIZA SİZE YAZAN KİŞİLERE İTİBAR ETMEYİNİZ HİÇBİR ZAMAN SİZDEN BİLGİLERİNİZİ VEYA DOSYALARINIZI İSTEMEYİZ!! <br><br>
* Script " GLYCON " dışında hiç bir yerde kişi/kuruluşlar tarafından iznimiz dışında satışı yapılamaz. <br> <br>
* Scripti satın alan kişiler 2. bir kişi ile paylaşırsa yada dağıtırsa sistem tarafından otomatik olarak tespit edilmektedir lisansları iptal edilir ve "ÜCRET İADESİ" yapılmaz. 
                        </div>
                </div>
            </div>
        </div>
        
<?php }elseif($e_route == "details"){ ?>      

               <div class="container">
            <div class="w-full md:w-full lg:w-3/4 p-6 mx-auto card shadow border p-5">
                <div class="intro-y text-xl font-medium fs-2">Detaylar</div>
                <div class="settings grid grid-cols-12 gap-6 mt-5  row">
                    <div class="col-md-4">
                        <div class="card shadow border p-2 mb-2" style="border-radius:10px;">
                           Toplam Servis; <?php echo countRow(["table"=>"services"]); ?><br><br>
                           Toplam Kategori; <?php echo countRow(["table"=>"categories"]); ?> <br><br>
                           Toplam Aktif Servis; <?php echo countRow(["table"=>"services","where"=>["service_type"=>2]]); ?> <br><br>
                           Toplam Pasif Servis; <?php echo countRow(["table"=>"services","where"=>["service_type"=>1]]); ?> 
                        </div>
                </div>
          <div class="col-md-4">
                        <div class="card shadow border p-2 mb-2" style="border-radius:10px;">
                           Toplam Üye; <?php echo countRow(["table"=>"clients"]); ?><br><br>
                           Yükleme Yapan Üyeler; <?=$count9?> <br><br>
                           Bu Ayki Üyeler; <?=$count?> <br><br>
                           Bugünkü Üyeler; <?=$count2?> 
                        </div>
                </div>
          <div class="col-md-4">
                        <div class="card shadow border p-2 mb-2" style="border-radius:10px;">
                           Toplam Ödeme; <?php echo round($query['SUM(payment_amount)']); ?><br><br>
                           Toplam Harcanan; <?php echo round($query2['order_charge']); ?> <br><br>
                           Bugünkü Kazanç; <?=$kazanc2['SUM(payment_amount)'];?> <br><br>
                           Bu Ayki Kazanç; <?=$kazanc['SUM(payment_amount)'];?> 
                        </div>
                </div>
          <div class="col-md-4">
                        <div class="card shadow border p-2 mb-2" style="border-radius:10px;">
                           Toplam Sipariş; <?php echo countRow(["table"=>"orders"]); ?><br><br>
                           API İle Gelen Sipariş; <?php echo countRow(["table"=>"orders","where"=>["order_where"=>"api"]]); ?><br><br>
                           Bu Ayki Sipariş; <?=$count3?> <br><br>
                           Bugünkü Sipariş; <?=$count4?> 
                        </div>
                </div>
          <div class="col-md-4">
                        <div class="card shadow border p-2 mb-2" style="border-radius:10px;">
                           Toplam Destek Talebi; <?php echo countRow(["table"=>"tickets"]); ?> <br><br>
                           Toplam Çözümlenen; <?php echo countRow(["table"=>"tickets","where"=>["status"=>"closed"]]); ?>  <br><br>
                           Bugünkü Destek Talepleri; <?=$count6?> <br><br>
                           Bu Ayki Destek Talepleri; <?=$count5?> 

                        </div>
                </div>
               <div class="col-md-4">
                        <div class="card shadow border p-2 mb-2" style="border-radius:10px;">
                           Toplam Etkileşim; <?php echo countRow(["table"=>"client_report"]); ?> <br><br>
                           Bugünkü Etkileşim; <?=$count8?> <br><br>
                           Bu Ayki Etkileşim; <?=$count7?> <br><br>
                           Sipariş Sıklığı; N/A 
                        </div>
                </div>
                  
            </div>
        </div>
                </div>
                

                
            </div>
        </div>
        

<?php } ?>

<?php include 'manager.footer.php'; endif; ?>