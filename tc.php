<?php
/**
 * Plugin Name: TCKN Zorunluluk
 * Plugin URI: https://qnot.net/blog/ilan-sitelerinde-tc-kimlik-no-zorunlulugu-wordpress
 * Description: Bu eklenti, kullanıcı kaydı sırasında Türk Kimlik Numarası (TCKN) zorunluluğunu zorunlu kılmaktadır ve kayıtlı kullanıcılar için Türk Kimlik Numarası (TCKN) güncelleme olanağı sağlamaktadır.Kısa kod kullanım : [tc_kayit_form]
 * Version: 1.0
 * Author: harew
 * Author URI: https://qnot.net
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// SOAP Uyarısı
function add_soap_warning() {
    echo '<div class="notice notice-warning is-dismissible">
        <p><strong>Önemli Bilgi:</strong> Bu eklenti SOAP protokolünü kullanarak Türk Kimlik Numarası (TCKN) doğrulaması yapmaktadır. SOAP protokolü, sunucunuzun SOAP istemcisini desteklemesini gerektirir. Lütfen sunucunuzun SOAP istemcisini etkinleştirdiğinizden ve SOAP istemcisini kullanabilecek yetkilere sahip olduğunuzdan emin olun.</p>
    </div>';
}
add_action('admin_notices', 'add_soap_warning');

require_once plugin_dir_path(__FILE__) . 'include/TcKontrol.php';
