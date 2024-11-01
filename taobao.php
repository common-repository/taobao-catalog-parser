<?php
/*
Plugin Name: Taobao Catalog
Description: Taobao Catalog parser 
Version: 1.0.0
Author: Shabrov Vitaliy, Sheglov Alexey
Author : http://taobaowoocommerce.dropshop.pro/
Plugin URI: http://taobaowoocommerce.dropshop.pro/
Licence: GPLv2 - See LICENCE.txt
*/

define('SELLRY_DIR', plugin_dir_path(__FILE__));
define('SELLRY_URL', plugin_dir_url(__FILE__));
define('TAOBAO_URL', 'http://openapi.dropshop.pro/dsapi/exec?');  // API URL



require_once(SELLRY_DIR.'/includes/Observer.php');


 
$plugname = "Taobao Catalog Sync";   

$shortname = "tbc";                            

$plugoptions = array (
 
        array(  "name" => "Taobao Catalog Settings",
		  "type" => "title"),
          
            array(  "name" => "Taobao Catalog Credentials",
              "desc" => "<em>(Get these from your Taobao Catalog)</em>", 
    		  "type" => "description"), 
            array(  "name" => "API User",
			"desc" => "",
                        "id" => $shortname."_user",
                        "std" => "apidemo@dropshop.pro",
                        "type" => "text"),          

              array(  "name" => "API PSWD",
			"desc" => "",
                        "id" => $shortname."_password",
                        "std" => "apidemo",
                        "type" => "text"),         
              
            array(
            "name" => "Lang Taobao Catalog",
            "id" => $shortname."_lang",
            "type" => "selectbox",
            "options" => array(
            "en" => "English",
            "ru" => "Russian",
            "zh" => "Chinese"
            ) ),

        
);



function taobao_form_for_admin() {                               
    global $plugname, $shortname, $plugoptions;
?>
 
<div class="wrap">
<form method="post">                            
<table class="admin_table"> 
    <?php foreach ($plugoptions as $value) {
	if ($value['type'] == "title") { ?>      
	   <tr valign="top">                     
	      <td colspan="2" class="head">
                    <h3 ><?php echo $value['name']; ?></h3>
              </td>
	   </tr>
	<?php } elseif ($value['type'] == "description") { ?>
 
	   <tr valign="middle" height="50">
 
	      <th scope="row"><?php echo $value['name']; ?></th>
 
	      <td><?php echo $value['desc'] ; ?>       

              </td>
 
	   </tr>
       
	<?php } elseif ($value['type'] == "selectbox") { ?>
 
	   <tr valign="top">
 
	      <th scope="row"><?php echo $value['name']; ?>:</th>
 
	      <td>
          
          <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
          <?php 
          if(get_option($value['id']))$ch_sel = get_option($value['id']);
          else $ch_sel = '';
          foreach ($value['options'] as $key=>$value)echo '<option value="'.$key.'" '.(($ch_sel == $key)?'selected="selected"':'').'>'.$value.'</option>';
          ?>
          </select>
          
          
          
          
          
          <?php if( get_option($value['id']) ) {      

                        $checked = "checked=\"checked\""; 

                     } else { $checked = ""; } ?>

      

              </td>
 
	   </tr>
 
 
	<?php } elseif ($value['type'] == "checkbox") { ?>
 
	   <tr valign="top">
 
	      <th scope="row"><?php echo $value['name']; ?>:</th>
 
	      <td><?php if( get_option($value['id']) ) {      

                        $checked = "checked=\"checked\""; 

                     } else { $checked = ""; } ?>
 
		    <input type="checkbox" name="<?php echo $value['id']; ?>" 
                           id="<?php echo $value['id']; ?>" value="true" 
                           <?php echo $checked; ?> />
 
		     <br />
 
		     <?php echo $value['desc'] ; ?>       

              </td>
 
	   </tr>
 
	<?php } elseif ($value['type'] == "text") { ?>
 
	   <tr valign="top">
 
	      <th scope="row"><?php echo $value['name']; ?>:</th>
 
	      <td><input name="<?php echo $value['id']; ?>" 
                         id="<?php echo $value['id']; ?>" 
                         type="<?php echo $value['type']; ?>" 
                         value="<?php if (get_option( $value['id'] ) != "") { 
                                                echo htmlspecialchars(
                                                     get_option( $value['id'] ) ); 
                                      } else { echo $value['std']; } ?>" />
 
		   <br />
 
		   <?php echo $value['desc'] ; ?> 
               </td>
 
	    </tr>
	<?php
 }
 
 
    } ?>                                         

 
  </table>
 
  <div class="submit">                    
<?php
 
    $url=TAOBAO_URL.'user='.get_option('tbc_user').'&password='.get_option('tbc_password').'&command=checkAccess';
  $response =  wp_remote_get( $url, array(
'timeout'     => 30,
    'redirection' => 5,
    'httpversion' => '1.0',
    'user-agent'  => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
    'blocking'    => true,
    'headers'     => array(),
    'cookies'     => array(),
    'body'        => null,
    'compress'    => false,
    'decompress'  => true,
    'sslverify'   => true,
    'stream'      => false,
    'filename'    => null
	
    )
);
?>
  <p><strong>Access: <?php echo  $response['body'] ?></strong></p>
    <input name="save_3pl" type="submit" value="Save" />
    <input name="reset_3pl" type="submit" value="Reset" />
  </div>
</form>
</div>
<?php
} 



function taobao_settings() {
 
    global $plugname, $shortname, $plugoptions;
 
      if ( isset($_POST['save_3pl']) ) {
 
          foreach ($plugoptions as $value) {   

             update_option( $value['id'], $_REQUEST[ $value['id'] ] ); 
          }
 
          echo '<div id="message" class="updated fade"><p><strong>' .
               'Settings "'.$plugname.'" has been saved.' .
               '</strong></p></div>';
 
      } else if ( isset($_POST['reset_3pl']) ) {   
 
 
	  foreach ($plugoptions as $value) {
	     delete_option( $value['id'] ); 
          }
 
	  echo '<div id="message" class="updated fade"><p><strong>' . 
               'Settings "'.$plugname.'" has been reseted.' .
               '</strong></p></div>';
 
      } 
 
    add_options_page($plugname.": settings", "Taobao Catalog Settings", 'manage_options',basename( __FILE__ ), 'taobao_form_for_admin');

 
}


function taobao_create_custom_panel() {
    add_menu_page('Taobao Catalog Sync', 'Taobao Catalog Sync', 'manage_options', 'taobao', 'taobao_custom_panel');
}
function taobao_custom_panel(){

   
$mdl = new TBC_Observer();    
    
if($_REQUEST['do']){
    $do = $_REQUEST['do'];
    
switch($do){
     case 'forcesync':

            $result = $mdl->_orderExport();
            	  echo '<div id="message" class="updated fade"><p><strong>' . 
               $result['msg'] .
               '</strong></p></div>';
        break;        
    
        
        
}    
    
}   

?> 
<div class="wrap"><div id="icon-options-general" class="icon32">
<br />
</div>
<h2>Taobao Catalog Sync</h2></div>
<p>&nbsp;</p>               

              
<script type="text/javascript">
function do_sync(p){
    if(p){
        document.getElementById('do').value = p;
        document.getElementById('syncform').submit();
    }
}
</script>                
<form method="post"  id="syncform">
<div style='float: left; margin-right: 45px;'>
<input type="button" name="force-sync" value="Force Sync" onClick="if(confirm('Are you sure?'))do_sync('forcesync');" />
</div>
<input type="hidden" name="do" id="do" value="" />
</form>
<?php                
}



// settings  
add_action('admin_menu', 'taobao_settings');  
// sync page
add_action('admin_menu', 'taobao_create_custom_panel');
