<?php
// Kullanıcı giriş yapmış mı diye kontrol edin
if (is_user_logged_in()) {
    // Kullanıcı giriş yapmışsa, kullanıcı bilgilerini alın
    $current_user = wp_get_current_user();
    // Kullanıcı bilgilerini doldurmak için uygun form alanlarının değerlerini ayarlayın
    $username_value = esc_attr($current_user->user_login);
    $email_value = esc_attr($current_user->user_email);
    $ad_value = get_user_meta($current_user->ID, 'first_name', true);
    $soyad_value = get_user_meta($current_user->ID, 'last_name', true);
    $dogumYili_value = get_user_meta($current_user->ID, 'dogum_yili', true);
    $tcKimlikNo_value = get_user_meta($current_user->ID, 'tc_kimlik_no', true);
    $username_value = esc_attr($current_user->user_login);
    $readonly_attribute = 'readonly';

} else {
    // Kullanıcı giriş yapmamışsa, değerleri boş bırakabilirsiniz
    $username_value = '';
    $email_value = '';
    $ad_value = '';
    $soyad_value = '';
    $dogumYili_value = '';
    $tcKimlikNo_value = '';
    $readonly_attribute = '';
}
?>
<link rel="stylesheet" type="text/css" href="<?php echo plugin_dir_url(__DIR__) . 'css/style.css?v=4'; ?>">
<form id="tc-verification-form">
    <div>
        <label for="username">Kullanıcı Adı:</label>
        <input type="text" id="username" name="username" required value="<?php echo $username_value; ?>" <?php echo $readonly_attribute; ?>>
    </div>

    <div>
        <label for="email">E-Posta Adresi:</label>
        <input type="email" id="email" name="email" required value="<?php echo $email_value; ?>">
    </div>

    <div>
        <label for="ad">Adınız:</label>
        <input type="text" id="ad" name="ad" required value="<?php echo $ad_value; ?>">
    </div>

    <div>
        <label for="soyad">Soyadınız:</label>
        <input type="text" id="soyad" name="soyad" required value="<?php echo $soyad_value; ?>">
    </div>

    <div>
        <label for="dogumYili">Doğum Yılı:</label>
        <input type="text" id="dogumYili" name="dogumYili" required value="<?php echo $dogumYili_value; ?>">
    </div>

    <div>
        <label for="tcKimlikNo">TC Kimlik Numarası:</label>
        <input type="text" maxlength="11" id="tcKimlikNo" name="tcKimlikNo" required value="<?php echo $tcKimlikNo_value; ?>">
    </div>
    <input type="hidden" name="ajaxurl" value="<?php echo admin_url('admin-ajax.php'); ?>">
    <input type="hidden" name="action" value="harew_register_tc">
    <?php wp_nonce_field('harew-register', 'harew-register-nonce'); ?>
    <div>
    <?php if (is_user_logged_in()) : ?>
        <input type="hidden" name="durum" value="güncelle">
        <input type="hidden" name="user_id" value="<?php echo $current_user->ID;?>">

        <div>
            <button type="submit">Güncelle</button>
        </div>
    <?php else : ?>
        <input type="hidden" name="durum" value="kayit">

        <div>
            <button type="submit">Kayıt Ol</button>
        </div>
    <?php endif; ?>
    </div>
</form>
<script src="<?php echo plugin_dir_url(__DIR__) . 'js/script.js'; ?>"></script>

