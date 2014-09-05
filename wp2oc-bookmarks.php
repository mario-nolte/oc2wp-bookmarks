<?php
/* 
Plugin Name: WP2OC Bookmarks
Version: 0.0.5
Plugin URI: http://momind.eu/
Description: Use bookmarks that are managed by ownCloud in WordPress posts, pages and widgets
Author: Mario Nolte
Author URI: http://www.momind.eu
Licenc:  GPLv2
*/ 

/*import the class file for Bookmark Class*/
require_once( plugin_dir_path( __FILE__ ) . 'bookmark.inc.php' );

  /* get bookmarks in accordance to the defined tag out of the database and return an array of bookmarks*/
  function getBMfromSQL($tag){
    /*configure SQL Server connection data*/
    $sql_server=get_option('wpocbm_sql_server');
    $sql_user =$sqlserver=get_option('wpocbm_sql_user');;
    $sql_password =$sqlserver=get_option('wpocbm_sql_password');;
    $oc_database=$sqlserver=get_option('wpocbm_sql_database');;

    $bm_term=$tag;
    /* Filter bookmarks of a certain user or display all bookmarks of the database*/
    if (get_option('wpocbm_sql_bmOwner')=='all'){
    $bm_user='%';}
    else {$bm_user=get_option('wpocbm_sql_bmOwner');};

    /*connect to MySQL*/
    mysql_connect($sql_server, $sql_user, $sql_password);
    mysql_query("SET NAMES 'utf8'");
    mysql_select_db($oc_database);
    /*query*/
    $res = mysql_query("select b.url, b.title, b.description from oc_bookmarks b INNER JOIN oc_bookmarks_tags t on t.bookmark_id=b.id WHERE t.tag LIKE '%$bm_term%' AND b.user_id LIKE '$bm_user' ORDER BY b.lastmodified DESC;");
    $num = mysql_num_rows($res);
    
    
    /*create array containing BM objects*/
    $i=0;
    while ($dsatz = mysql_fetch_assoc($res)){
	  $bookmarks[$i]=new bookmark($dsatz["title"], $dsatz["url"], $dsatz["description"]);
	  $i++;
	  }
	    
      return $bookmarks;
  }

function getBMfromOC($tag){
echo "DAS TAG IST:" . $tag ;
$response = wp_remote_post( get_option('wpocbm_oc_server'), array(
	'method' => 'POST',
	'timeout' => 45,
	'redirection' => 5,
	'httpversion' => '1.0',
	'blocking' => true,
	'headers' => array(),
	'body' => array( 'user' => get_option('wpocbm_oc_user'), 'password' => get_option('wpocbm_oc_password'), 'tags' => array($tag), 'description' => true),
	'cookies' => array()
    )
);

$result = json_decode($response['body']);

for ($i=0; $i<count($result); $i++){
$bookmarks[$i]=new bookmark($result[$i] ->title, $result[$i] ->url, $result[$i] ->description);
}
return $bookmarks;
}


function wp_oc_bm_tablegenerator($bookmarks){

$tablepre=stripslashes(get_option('wpocbm_table_styling'));
$table_number=get_option('wpocbm_table_number');
$table_title=get_option('wpocbm_table_title');
$table_description=get_option('wpocbm_table_description');
$tablescript=stripslashes(get_option('wpocbm_table_script'));

$tableoutput ="";

$tableoutput .= "<table " . $tablepre .">";
$tableoutput .= "<thead> <tr> <th class='column-1'> ".$table_number ." </th> <th class='column-2'>" .$table_title ." </th><th class='column-3'> ".$table_description. " </th></tr></thead>";
$tableoutput .= "<tbody>";
	  
  for ($i=0; $i<count($bookmarks); $i++){
  $tableoutput .= "<tr>";
  $tableoutput .= "<td class='column-1'>" . ($i+1) . "</td>";
  $tableoutput .= "<td class='column-2'> <a href ='" . $bookmarks[$i]->link . "' target='_blank'> ".$bookmarks[$i]->title . "</a> </td>";
  $tableoutput .= "<td class='column-3'>" . $bookmarks[$i]->description . " </td>";
  $tableoutput .= "</tr>";
  }

$tableoutput .= "</tbody>";
$tableoutput .= "</table>";
$tableoutput .= $tablescript;

return $tableoutput;
}

function wp_oc_bm_shortcode($atts) {
  $output ="<p>Output mode: ";
  
  $tagArray = shortcode_atts( array('tag' => 'public',), $atts );
  $bmArray;
  
  if(get_option('wpocbm_op_type')=='sql'){
  $output = $output . "<font color='green'> SQL mode </p> </font>" . get_option('wpocbm_sql_bmOwner');
  $bookmarks = getBMfromSQL("{$tagArray['tag']}");
  $output = $output . wp_oc_bm_tablegenerator($bookmarks);
  return $output;
  }
  
  if(get_option('wpocbm_op_type')=='ocApp'){
  $output = $output . "<font color='green'> OC mode </p> </font>";
  $bookmarks = getBMfromOC("{$tagArray['tag']}");
  $output = $output . wp_oc_bm_tablegenerator($bookmarks);
  return $output;
  }

}

add_shortcode('wp2ocbm', 'wp_oc_bm_shortcode');

function wpocbm_configuration_page()
{

    if (isset($_POST['info_update']))
    {
        echo '<div id="message" class="updated fade"><p><strong>';

        update_option('wpocbm_op_type', (string)$_POST["wpocbm_op_type"]);
        update_option('wpocbm_oc_server', (string)$_POST["wpocbm_oc_server"]);
        update_option('wpocbm_oc_user', (string)$_POST["wpocbm_oc_user"]);
        update_option('wpocbm_oc_password', (string)$_POST["wpocbm_oc_password"]);
        update_option('wpocbm_sql_server', (string)$_POST["wpocbm_sql_server"]);
        update_option('wpocbm_sql_user', (string)$_POST["wpocbm_sql_user"]);
        update_option('wpocbm_sql_password', (string)$_POST["wpocbm_sql_password"]);
        update_option('wpocbm_sql_database', (string)$_POST["wpocbm_sql_database"]);
        update_option('wpocbm_sql_bmOwner', (string)$_POST["wpocbm_sql_bmOwner"]);
        update_option('wpocbm_table_styling', $_POST["wpocbm_table_styling"]);
        update_option('wpocbm_table_number', $_POST["wpocbm_table_number"]);
        update_option('wpocbm_table_title', $_POST["wpocbm_table_title"]);
        update_option('wpocbm_table_description', $_POST["wpocbm_table_description"]);
        update_option('wpocbm_table_script', $_POST["wpocbm_table_script"]);
                                
        echo 'Options Updated!';
        echo '</strong></p></div>';
    }
    
    $wpocbm_op_type = stripslashes(get_option('wpocbm_op_type'));
									  

?>

<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" id="wpocbmoptions" class="validate">
<input type="hidden" name="info_update" id="info_update" value="true" />
<h2>WordPress2Owncloud Bookmarks sharing options</h2>
    
    <h3>Plugin Usage:</h3>

    <p>To make use of this plugin please consider the following steps:</p>
    <ol>
    <li>Configure the SQL Options for your owncloud instance.</li>
    <li>Add the shortcode <strong>[wp2ocbm]</strong> to a post or page that should contain a table with those bookmarks that have the tag 'public'.</li>
    <li>Add the shortcode <strong>[wp2ocbm tag="example"]</strong> to a post or page that should contain a table with those bookmarks that have the tag 'example'.</li>
    <li>Configure the design of the table e. g. like explained in this tutorial.</li>
    </ol>

<h3>Operation Mode</h3>
<p>Please chose if you use the Owncloud APP or if Bookmarks should be retrieved by using the MySQL database of owncloud.</p>
<table width="100%" border="0" cellspacing="0" cellpadding="6">
<tr valign="top">
    <td width="25%" align="right">
      OC App:
    </td>
    <td align="left">
    <?php _e('<input type="radio" name="wpocbm_op_type" value="ocApp"') ?>
    <?php if ($wpocbm_op_type == "ocApp") echo " checked " ?>
    <?php _e('/>') ?>
  <tr valign="top">
    <td width="25%" align="right">
      MySQL:
    </td>
    <td align="left">
    <?php _e('<input type="radio" name="wpocbm_op_type" value="sql"') ?>
    <?php if ($wpocbm_op_type == "sql") echo " checked "  ?>
    <?php _e('/>') ?>
</td>
</tr>
</table>

<h3>Owncloud App Options</h3>

<table width="100%" border="0" cellspacing="0" cellpadding="6">
    
<tr valign="top">
    <td width="25%" align="right">
      OC App URL:
    </td>
    <td align="left">
      <input name="wpocbm_oc_server" type="text" size="100" value="<?php echo get_option('wpocbm_oc_server'); ?>"/>
    </td>
</tr>
<tr valign="top">
    <td width="25%" align="right">
      User:
    </td>
    <td align="left">
      <input name="wpocbm_oc_user" type="text" size="25" value="<?php echo get_option('wpocbm_oc_user'); ?>"/>
    </td>
</tr>
<tr valign="top">
    <td width="25%" align="right">
      Password:
    </td>
    <td align="left">
      <input name="wpocbm_oc_password" type="password" size="25" value="<?php echo get_option('wpocbm_oc_password'); ?>"/>
    </td>
</tr>
<tr>
<td>
</td>
<td>
<?php
$response = wp_remote_post( get_option('wpocbm_oc_server'), array(
	'method' => 'POST',
	'timeout' => 45,
	'redirection' => 5,
	'httpversion' => '1.0',
	'blocking' => true,
	'headers' => array(),
	'body' => array( 'user' => get_option('wpocbm_oc_user'), 'password' => get_option('wpocbm_oc_password'), 'tags' => array("example")),
	'cookies' => array()
    )
);

$result = json_decode($response['body']);

if($result ->error==1){
echo "<font color='red'>";
echo $result ->message;
echo "</font>";
}
/*print_r($result);*/
if($result ->status=='error'){
echo "<font color='red'>";
echo "Check OC APP URL. OC Server response is: " . $result->data->message;
echo "</font>";
}
?>
</td>
</tr>
</table>


        
<h3>SQL Options</h3>
<p>To access the owncloud database it is highly recommended to create an own user that has limited access to the database like described in this  tutorial. Please fill the following fields to enter the access data for the database. </p>
<table width="100%" border="0" cellspacing="0" cellpadding="6">
    
<tr valign="top">
    <td width="25%" align="right">
      SQL server:
    </td>
    <td align="left">
      <input name="wpocbm_sql_server" type="text" size="25" value="<?php echo get_option('wpocbm_sql_server'); ?>"/>
    </td>
</tr>

<tr valign="top">
    <td width="25%" align="right">
      SQL user:
    </td>
    <td align="left">
      <input name="wpocbm_sql_user" type="text" size="25" value="<?php echo get_option('wpocbm_sql_user'); ?>"/>
    </td>
</tr>

<tr valign="top">
    <td width="25%" align="right">
      SQL password:
    </td>
    <td align="left">
      <input name="wpocbm_sql_password" type="password" size="25" value="<?php echo get_option('wpocbm_sql_password'); ?>"/>
    </td>
</tr>

<tr valign="top">
    <td width="25%" align="right">
      Name of database:
    </td>
    <td align="left">
      <input name="wpocbm_sql_database" type="text" size="25" value="<?php echo get_option('wpocbm_sql_database'); ?>"/>
    </td>
</tr>

<tr valign="top">
    <td width="25%" align="right">
      Bookmark owner:
    </td>
    <td align="left">
      <input name="wpocbm_sql_bmOwner" type="text" size="25" value="<?php echo get_option('wpocbm_sql_bmOwner'); ?>"/><br>
      <i>To display only the bookmarks of a certain owner please enter the username here. Otherwise please enter "all": Bookmarks of all users containing the specified tag will be displayed. </i>
    </td>
    </tr>

</table>


<h3>Configure Table Layout</h3>
<table width="100%" border="0" cellspacing="0" cellpadding="6">
    
<tr valign="top">
    <td width="25%" align="right">
      Table styling options:
    </td>
    <td align="left">
      <input name="wpocbm_table_styling" type="text" size="100" value="<?php echo stripslashes(get_option('wpocbm_table_styling')); ?>" />
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="6">
   
<tr valign="top">
    <td width="25%" align="right">
      Labeling:
    </td>
    
    <td align="left" style="width:20px;">
      <input name="wpocbm_table_number" type="text" size="25" value="<?php echo get_option('wpocbm_table_number'); ?>"/><br>
      <i>Number</i>
    </td>

    <td align="left" style="width:100px;">
      <input name="wpocbm_table_title" type="text" size="25" value="<?php echo get_option('wpocbm_table_title'); ?>"/><br>
      <i>Title</i>
    </td>

    <td align="left" style="width:100px;">
      <input name="wpocbm_table_description" type="text" size="25" value="<?php echo get_option('wpocbm_table_description'); ?>"/><br>
      <i>Description</i>
    </td>
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="6">
<tr valign="top">
    <td width="25%" align="right">
      Table script:
    </td>
    <td align="left">
      <textarea name="wpocbm_table_script" cols="100" rows="10"> <?php echo stripslashes(get_option('wpocbm_table_script')); ?> </textarea>
    </td>

</tr>

</table>
    
  

  <p class="submit"><input type="submit" name="inf_update" id="submit" class="button" value="<?php _e('Update options'); ?> &raquo;"></p>
   </form>
   Please visit <a href="http://www.momind.eu">the documentation</a> to read more about the use and configuration of this plugin.<br/>
   
   </div>
   

<?php	
}

function wp2ocbm_plugin_menu()
{
add_options_page('WP OC BM', 'WP2OC Bookmarks', 'manage_options', __FILE__, 'wpocbm_configuration_page');
}

add_action('admin_menu', 'wp2ocbm_plugin_menu');
?>
