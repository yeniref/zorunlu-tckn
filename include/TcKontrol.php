<?php

class TCValidation
{
    public function __construct()
    {
        add_shortcode('tc_kayit_form', array($this, 'tc_verification_form_shortcode'));
        add_action('show_user_profile', array($this, 'show_extra_profile_fields'));
        add_action('edit_user_profile', array($this, 'show_extra_profile_fields'));
        add_action('register_form', array($this, 'harew_register_form'));
        add_filter('user_register', array($this, 'harew_user_register'), 10, 1); // user_register kancasını kullanıyoruz
        add_action('register_post', array($this, 'before_register_check'), 10, 3); // kayıt öncesi kontrol için
        add_action('wp_ajax_harew_register_tc', array($this, 'harew_register_user'));
        add_action('wp_ajax_nopriv_harew_register_tc', array($this, 'harew_register_user'));
    }

    public function before_register_check($login, $email, $errors)
    {
        $tc_kimlik_no = (isset($_POST['tc_kimlik_no']) ? sanitize_text_field($_POST['tc_kimlik_no']) : '');
        $ad = (isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '');
        $soyad = (isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '');
        $dogum_yili = (isset($_POST['dogum_yili']) ? sanitize_text_field($_POST['dogum_yili']) : '');

        $sorgula = $this->tcKimlikSorgula($tc_kimlik_no, $ad, $soyad, $dogum_yili);

        if ($sorgula->TCKimlikNoDogrulaResult != 1) {
            $errors->add('registration_error', 'Kimlik bilgileri doğrulanmadı. Lütfen kontrol edip tekrar deneyin.');
        }
    }

    public function harew_user_register($user_id)
    {
        $tc_kimlik_no = (isset($_POST['tc_kimlik_no']) ? sanitize_text_field($_POST['tc_kimlik_no']) : '');
        $ad = (isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '');
        $soyad = (isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '');
        $dogum_yili = (isset($_POST['dogum_yili']) ? sanitize_text_field($_POST['dogum_yili']) : '');

        // TC kimlik doğrulama başarılı, kayıt işlemine devam et
        if (!empty($tc_kimlik_no)) {
            update_user_meta($user_id, 'tc_kimlik_no', $tc_kimlik_no);
        }

        if (!empty($dogum_yili)) {
            update_user_meta($user_id, 'dogum_yili', $dogum_yili);
        }

        if (!empty($ad)) {
            update_user_meta($user_id, 'first_name', $ad);
        }

        if (!empty($soyad)) {
            update_user_meta($user_id, 'last_name', $soyad);
        }
    }

    public function show_extra_profile_fields($user)
    {
?>
        <h3>Yeni Yasa Zorunlu Bilgiler</h3>
        <table class="form-table">
            <tr>
                <th><label for="tc_kimlik_no">TC Kimlik No</label></th>
                <td>
                    <input type="text" name="tc_kimlik_no" id="tc_kimlik_no" value="<?php echo esc_attr(get_the_author_meta('tc_kimlik_no', $user->ID)); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="dogum_yili">Doğum Yılı</label></th>
                <td>
                    <input type="text" name="dogum_yili" id="dogum_yili" value="<?php echo esc_attr(get_the_author_meta('dogum_yili', $user->ID)); ?>" class="regular-text" />
                </td>
            </tr>
        </table>
<?php
    }

    public function harew_register_form()
    {
        // İsim alanını oluşturun
        echo '<p><label for="first_name">İsim<br>';
        echo '<input type="text" name="first_name" id="first_name" class="input" value="' . (isset($_POST['first_name']) ? esc_attr($_POST['first_name']) : '') . '" size="25" /></label></p>';

        // Soyisim alanını oluşturun
        echo '<p><label for="last_name">Soyisim<br>';
        echo '<input type="text" name="last_name" id="last_name" class="input" value="' . (isset($_POST['last_name']) ? esc_attr($_POST['last_name']) : '') . '" size="25" /></label></p>';

        // TC Kimlik No alanını oluşturun
        echo '<p><label for="tc_kimlik_no">TC Kimlik No<br>';
        echo '<input type="text" maxlength="11" name="tc_kimlik_no" id="tc_kimlik_no" class="input" value="' . (isset($_POST['tc_kimlik_no']) ? esc_attr($_POST['tc_kimlik_no']) : '') . '" size="25" /></label></p>';

        // Doğum Yılı alanını oluşturun
        echo '<p><label for="dogum_yili">Doğum Yılı<br>';
        echo '<input type="text" name="dogum_yili" id="dogum_yili" class="input" value="' . (isset($_POST['dogum_yili']) ? esc_attr($_POST['dogum_yili']) : '') . '" size="25" /></label></p>';
    }

    public function karakterDuzelt($yazi)
    {
        $ara = array("ç", "i", "ı", "ğ", "ö", "ş", "ü");
        $degistir = array("Ç", "İ", "I", "Ğ", "Ö", "Ş", "Ü");
        $yazi = str_replace($ara, $degistir, $yazi);
        $yazi = strtoupper($yazi);
        return $yazi;
    }

    public function tcKimlikSorgula($tcKimlikNo, $ad, $soyad, $dogumYili)
    {
        try {
            $veriler = array(
                'TCKimlikNo' => $tcKimlikNo,
                'Ad' => $ad,
                'Soyad' => $soyad,
                'DogumYili' => $dogumYili
            );

            $baglan = new SoapClient("https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx?WSDL");
            $sonuc = $baglan->TCKimlikNoDogrula($veriler);

            return $sonuc;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function tc_verification_form_shortcode()
    {
        ob_start();
        include plugin_dir_path(__DIR__) . 'template/form.php'; // Dosya yolunu düzgün şekilde belirttik
        return ob_get_clean();
    }

    public function harew_register_user()
    {

        check_ajax_referer('harew-register', 'harew-register-nonce');

        // Gelen verileri alın
        $username = sanitize_text_field($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $ad = sanitize_text_field($_POST['ad']);
        $soyad = sanitize_text_field($_POST['soyad']);
        $dogumYili = intval($_POST['dogumYili']);
        $tcKimlikNo = sanitize_text_field($_POST['tcKimlikNo']);
        $durum =  sanitize_text_field($_POST['durum']);
        // TC kimlik doğrulaması yapın
        $tcSorguSonuc = $this->tcKimlikSorgula($tcKimlikNo, $ad, $soyad, $dogumYili);

        if ($tcSorguSonuc->TCKimlikNoDogrulaResult == 1) {
            if ($durum == 'kayit') {
                // TC kimlik doğrulaması başarılı, kullanıcıyı kaydedin
                $user_id = wp_create_user($username, wp_generate_password(), $email);
            }
            if ($durum == 'güncelle') {
                // TC kimlik doğrulaması başarılı, kullanıcı bilgilerini güncelle
                $user_id = sanitize_text_field($_POST['user_id']);
            }
            if (!is_wp_error($user_id)) {
                // Kullanıcıyı kaydettik, ekstra kullanıcı meta verilerini kaydedebilirsiniz
                update_user_meta($user_id, 'first_name', $ad);
                update_user_meta($user_id, 'last_name', $soyad);
                update_user_meta($user_id, 'dogum_yili', $dogumYili);
                update_user_meta($user_id, 'tc_kimlik_no', $tcKimlikNo);

                // Başarılı yanıtı döndürün
                wp_send_json(array('success' => true, 'message' => "Kayıt başarıyla oluşturuldu."));
            } else {
                // Kullanıcı kaydı sırasında bir hata oluştu
                wp_send_json(array('success' => false, 'message' => 'Kullanıcı kaydedilirken bir hata oluştu.'));
            }
        } else {
            // TC kimlik doğrulaması başarısız
            wp_send_json(array('success' => false, 'message' => 'Kimlik bilgileri doğrulanmadı. Lütfen kontrol edip tekrar deneyin.'));
        }
    }
}

$tc_kayit = new TCValidation;
