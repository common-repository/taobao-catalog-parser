<?php
class TBC_Observer
{

    public function _orderExport()
    {
        $url = TAOBAO_URL . 'user=' . get_option('tbc_user') . '&password=' . get_option('tbc_password') . '&command=getCatalogueList&params[lang]=' . get_option('tbc_lang');
        $response = wp_remote_get($url, array(
            'timeout' => 30,
            'redirection' => 5,
            'httpversion' => '1.0',
            'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url'),
            'blocking' => true,
            'headers' => array(),
            'cookies' => array(),
            'body' => null,
            'compress' => false,
            'decompress' => true,
            'sslverify' => true,
            'stream' => false,
            'filename' => null));

        $temp = json_decode($response['body']);

        if ($temp and count($temp) > 0) {
            foreach ($temp as $tmp) {
                $tmp = (array )$tmp;
                $this->_Import($tmp);
              
            }
              return array('msg' => 'Sync done');
        }
        return array('msg' => 'Sync Faled');
    }

    protected function _Import($item = array())
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT count(1) FROM $wpdb->term_taxonomy WHERE taxonomy='product_cat' AND description LIKE %s", ('%{' . $item['pkid'] . '}%'));
        if ($wpdb->get_var($sql) == 0) {

            if ($item['parent'] == 1) {
                $parent = 0;
            } else {
                $sql = $wpdb->prepare("SELECT term_id FROM $wpdb->term_taxonomy WHERE taxonomy='product_cat' AND description LIKE %s", ('%{' . $item['parent'] . '}%'));
                $parent = $wpdb->get_var($sql);

            }

            wp_insert_term($item['view_text'], 'product_cat', array(
                'description' => ('{' . $item['pkid'] . '}' . $item['url']),
                'slug' => wp_unique_term_slug($item['view_text']),
                'parent' => $parent,
                ));

        }
    }

}
