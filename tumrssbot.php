<?php
/*
Plugin Name: RSS Veri Çekici
Description: Not!!! Sadece belirtilen RSS URL'sinden veri çeker bir WordPress eklentisi yayınlama yapmaz. Yapımcı: umiT
Version: 1.0
*/

function rss_veri_cekici_shortcode() {
    ob_start();
    ?>
	   <style>
        .rss-veri-cekici-form {
            display: flex;
            align-items: center;
        }
        .rss-input {
            padding: 10px;
            margin-right: 10px;
        }
        .rss-button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .rss-result {
            margin-top: 20px;
        }
    </style>
    <form action="<?php echo esc_url(admin_url('admin-post.php?action=rss_veri_cek')); ?>" method="post" enctype="multipart/form-data">
        <input type="text" name="url" placeholder="">
        <button type="submit">Verileri Getir</button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('rss_veri_cekici', 'rss_veri_cekici_shortcode');

function feedMe($feed) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $feed);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $rss = curl_exec($ch);
    curl_close($ch);

    $rss = str_replace("<content:encoded>", "<contentEncoded>", $rss);
    $rss = str_replace("</content:encoded>", "</contentEncoded>", $rss);
    $rss = simplexml_load_string($rss);

    $siteTitle = $rss->channel->title;
    $cnt = count($rss->channel->item);

    for ($i = 0; $i < $cnt; $i++) {
        $url = $rss->channel->item[$i]->link;
        $title = $rss->channel->item[$i]->title;
        $desc = $rss->channel->item[$i]->description;
        $mak = $rss->channel->item[$i]->contentEncoded;

        echo "<br><br><br>------------------------------------------------------------";
        echo $title;
        echo "<br><br><br>";
        echo $mak;
    }
}

function rss_veri_cekici_action() {
    if (isset($_POST['url'])) {
        $veriurl = sanitize_text_field($_POST['url']);
        feedMe($veriurl);
    }
}
add_action('admin_post_nopriv_rss_veri_cek', 'rss_veri_cekici_action');
add_action('admin_post_rss_veri_cek', 'rss_veri_cekici_action');

function rss_veri_cekici_menu() {
    add_menu_page('RSS Veri Çekici by umiT', 'RSS Veri Çekici by umiT', 'manage_options', 'rss_veri_cekici_menu', 'rss_veri_cekici_admin_page');
}
add_action('admin_menu', 'rss_veri_cekici_menu');


function rss_veri_cekici_admin_page() {
    ?>
    <div class="wrap">
        <h2>RSS Veri Çekici</h2>
        <?php echo do_shortcode('[rss_veri_cekici]'); ?>
    </div>
    <?php
}
