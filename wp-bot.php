<?php
/*
Plugin Name: Wordpress Rss Bot
Plugin URI: https://github.com/umit-ozay/Wordpress-Rss-Bot/
Description: Wordpress sitelerde kullanabileceğiniz neredeyse bütün sitelerden veri çekebileceğiniz bir bottur.
Version: 1.0
Author: umiT
Author URI:  https://www.sohbettemalari.com
License: GNU
*/

add_action('admin_menu', 'umit_bot_menu');
function umit_bot_menu(){
    add_menu_page('Wordpress Rss Bot','Wordpress Rss Bot', 'manage_options', 'umit-wordpress-bot', 'umit_bot_yonetim');
}

function umit_bot_yonetim(){
    ?>
    <h1>Ümit Wordpress İçerik Botu</h1>
    <form method="post" enctype="multipart/form-data">
        <table class="form-table">
            <tr>
                <th style="width:20%"><label for="url">Rss Url Adresi Örnek Wp İçin: https://www.sohbettemalari.com/feed</label></th>
                <td><input type="text" name="url" size="30" style="width:97%" /></td>
                <td><button type="submit" class="button button-primary button-large">Verileri Gör</button></td>
            </tr>
        </table>
    </form>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST['url'])) {
            $veriurl = $_POST['url'];
			feedMe($veriurl);
            //feedMe($veriurl . "/feed");
        }
    }
}


function feedMe($feed) {
    $rss = fetch_feed($feed);
    
    if (!is_wp_error($rss)) {
        $cnt = $rss->get_item_quantity();

        if ($cnt > 0) {
            $items = $rss->get_items(0, $cnt);
            foreach ($items as $item) {
                $title = $item->get_title();
                $description = $item->get_content();
                ?>
                <br><br><br>
                <form id='new_post' name='new_post' method='post' action='#' enctype='multipart/form-data' novalidate='novalidate' onsubmit='return check();'>
                    <label>Makale Başlığı</label><br><br>
                    <input type='text' style='width:97%' id='websitetitle' name='websitetitle' required value='<?php echo strip_tags($title); ?>'>
                    <br><br><br>
                    <label>Makale İçeriği <font style='color:red;font-weight:bold;'>Makalelerin sonunda bulunan The post -MakaleBaşlık- first appeared on -SiteAdı- kısmını kaldırınız.</font></label><br><br>
                    <textarea style='width:97%;min-height:250px;' autocomplete='off' cols='40' name='description' id='description'><?php echo strip_tags($description); ?></textarea>
                    <br><br><br>
                    <button type='submit' class='button button-primary button-large' id='submit' name='submit'>Yazıyı Yayınla</button>
                    <input type='hidden' name='action' value='new_post' />
                    <?php echo wp_nonce_field('new-post'); ?>
                </form>
                <?php
            }
        } else {
            echo "RSS'te hiç öğe yok.";
        }
    } else {
        echo "RSS verileri alınamıyor.";
    }
}

// Yeni yazı oluştur
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['action']) && $_POST['action'] == 'new_post') {
    if (isset($_POST['submit'])) {
        $error = "";
        $success = "";

        if (!empty($_POST['websitetitle'])) {
            $title = $_POST['websitetitle'];
        } else {
            $error .= "Lütfen <b>Başlık</b> girin.<br />";
        }

        if (!empty($_POST['description'])) {
            $description = $_POST['description'];
        } else {
            $error .= "Lütfen <b>Tanım</b> girin.<br />";
        }

        $tags = $_POST['tags_input'];
        $cat = array($_POST['cat']);

        if (empty($error)) {
            $new_post = array(
                'post_title' => $title,
                'post_content' => $description,
                'post_category' => $cat,
                'post_status' => 'draft',
                'post_type' => 'post',
                'tags_input' => $tags,
            );

            $pid = wp_insert_post($new_post);

            if ($pid) {
                $success .= "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                <strong>Veriler güncelleniyor. Yazılar paylaşılıyor...</strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            } else {
                $error .= "Yazı oluşturulurken bir hata oluştu.";
            }
        }
    }
}
?>
