<?php
/* 
Plugin Name: OC2WP Bookmarks
Version: 0.0.5
Plugin URI: http://momind.eu/
Description: Use bookmarks that are managed by ownCloud in WordPress posts and pages as table
Author: Mario Nolte
Author URI: http://www.momind.eu
Licenc:  GPLv2
*/ 

/* set some default options */
function oc2wpbm_plugin_install ()
{
	add_option('oc2wpbm_table_title_display', '1');
	add_option('oc2wpbm_table_title_label', 'Title');
	add_option('oc2wpbm_table_number_display', '-1');
	add_option('oc2wpbm_table_number_label', 'Entry');
	add_option('oc2wpbm_table_description_display', '1');
	add_option('oc2wpbm_table_description_label', 'Description');
	add_option('oc2wpbm_table_tags_display', '-1');
	add_option('oc2wpbm_table_tags_label', 'Tags');
	add_option('oc2wpbm_table_lastmodified_display', '-1');
	add_option('oc2wpbm_table_lastmodified_label', 'Last change');
	add_option('oc2wpbm_op_type', 'ocApp');
	add_option('oc2wpbm_oc_server', 'https://REPLACE-THIS-WHITH-YOUR-SERVER.com/owncloud/index.php/apps/bookmarks/public/bookmark');
	
	}
	
register_activation_hook(__FILE__,'oc2wpbm_plugin_install');

/* deleting passwords for security reasons while deactivating the plugin */
function oc2wpbm_plugin_deactivate ()
{
delete_site_option('oc2wpbm_oc_password');
delete_site_option('oc2wpbm_sql_password');
}
register_deactivation_hook( __FILE__, 'oc2wpbm_plugin_deactivate' );

/* Delete all settings while uninstalling the plugin */
function oc2wpbm_plugin_uninstall ()
{
	// All Values
      delete_site_option('oc2wpbm_op_type');
      delete_site_option('oc2wpbm_oc_server');
      delete_site_option('oc2wpbm_oc_user');
      delete_site_option('oc2wpbm_oc_password');
      delete_site_option('oc2wpbm_sql_server');
      delete_site_option('oc2wpbm_sql_user');
      delete_site_option('oc2wpbm_sql_password');
      delete_site_option('oc2wpbm_sql_database');
      delete_site_option('oc2wpbm_sql_bmOwner');
      delete_site_option('oc2wpbm_table_styling');
      delete_site_option('oc2wpbm_table_number_display');
      delete_site_option('oc2wpbm_table_number_label');
      delete_site_option('oc2wpbm_table_title_display');
      delete_site_option('oc2wpbm_table_title_label');
      delete_site_option('oc2wpbm_table_description_display');
      delete_site_option('oc2wpbm_table_description_label');
      delete_site_option('oc2wpbm_table_tags_display');
      delete_site_option('oc2wpbm_table_tags_label');
      delete_site_option('oc2wpbm_table_lastmodified_display');
      delete_site_option('oc2wpbm_table_lastmodified_label');
      delete_site_option('oc2wpbm_table_script');

	}
register_uninstall_hook(__FILE__,'oc2wpbm_plugin_uninstall');


/*import the class file for Bookmark Class*/
require_once( plugin_dir_path( __FILE__ ) . 'bookmark.inc.php' );
require_once( plugin_dir_path( __FILE__ ) . 'config_page.inc.php' );

/* get bookmarks in accordance to the defined tag and the specified user (as owner of the bookmarks) out of the database and return an array of bookmarks*/
function getBMfromSQL($tags, $order){
  /*configure SQL Server connection data*/
  $sql_server=get_option('oc2wpbm_sql_server');
  $sql_user =$sqlserver=get_option('oc2wpbm_sql_user');
  $sql_password =$sqlserver=get_option('oc2wpbm_sql_password');
  $oc_database=$sqlserver=get_option('oc2wpbm_sql_database');
  

  $bm_term= implode("','", $tags);
  /* Filter bookmarks of a certain user or display all bookmarks of the database*/
  if (get_option('oc2wpbm_sql_bmOwner')=='all'){
      $bm_user='%';}
  else {$bm_user=get_option('oc2wpbm_sql_bmOwner');};

  /* connect to MySQL*/
  /* Instead of using the PHP SQL connection (following comment) the WordPress WPDB connection is used to sanitise the query.
  /*	mysql_connect($sql_server, $sql_user, $sql_password);
      mysql_query("SET NAMES 'utf8'");
      mysql_select_db($oc_database);*/
  
  $OCdb = new wpdb($sql_user, $sql_password, $oc_database, $sql_server); 
  /* Sanitise the query to avoid code & SQL injection. COLLATE UTF8_GENERAL_CI is used so that tags are used caseinsensitive*/
  $query=$OCdb->prepare("select b.url, b.title, b.description, GROUP_CONCAT(t.tag SEPARATOR ', ') as tags, b.lastmodified from oc_bookmarks b LEFT JOIN oc_bookmarks_tags t on b.id=t.bookmark_id WHERE t.tag COLLATE UTF8_GENERAL_CI IN (%s) AND b.user_id LIKE %s group by id ORDER BY b.lastmodified ASC", $bm_term, $bm_user);
  if (strcasecmp($order,'desc')==0){
  /* Due to the prepare() function sorting cannot be handeld as variable */
  $query=$OCdb->prepare("select b.url, b.title, b.description, GROUP_CONCAT(t.tag SEPARATOR ', ') as tags, b.lastmodified from oc_bookmarks b LEFT JOIN oc_bookmarks_tags t on b.id=t.bookmark_id WHERE t.tag COLLATE UTF8_GENERAL_CI IN (%s) AND b.user_id LIKE %s group by id ORDER BY b.lastmodified DESC", $bm_term, $bm_user);
  }
  $query=stripslashes($query);
  $res = $OCdb->get_results($query);
      
  /*create array containing BM objects*/
  for ($i=0; $i<count($res); $i++){
	$bookmarks[$i]=new bookmark($res[$i] ->title, $res[$i] ->url, $res[$i] ->description, explode(', ', $res[$i] ->tags), $res[$i] ->lastmodified);
	}
	
  
    return $bookmarks;
}

/* get bookmarks in accordance to the defined tag out of ownCloud via the Bookmarks App*/
function getBMfromOC($tags, $order){
//setting sorting options

echo "<br> Die TAGs sind: " ;
for($i=0; $i<count($tags);$i++){echo " ". $i . ".tag=" . $tags[$i];};
echo "<br> sort:" .$order;

$response = wp_remote_post( get_option('oc2wpbm_oc_server'), array(
	'method' => 'POST',
	'timeout' => 45,
	'redirection' => 5,
	'httpversion' => '1.0',
	'blocking' => true,
	'headers' => array(),
	'body' => array( 'user' => get_option('oc2wpbm_oc_user'), 'password' => get_option('oc2wpbm_oc_password'), 'tags' => $tags, 'description' => true, 'datesort'=>$order),
	'cookies' => array()
    )
);

$result = json_decode($response['body']);

for ($i=0; $i<count($result); $i++){
$bookmarks[$i]=new bookmark($result[$i] ->title, $result[$i] ->url, $result[$i] ->description, $result[$i] ->tags, $result[$i] ->lastmodified);
}
return $bookmarks;
}

/* Generates the HTML Code for a table containing bookmark information*/
function oc2wpbm_tablegenerator($bookmarks){

$tablepre=stripslashes(get_option('oc2wpbm_table_styling'));
$table_number=get_option('oc2wpbm_table_number_label');
$table_title=get_option('oc2wpbm_table_title_label');
$table_description=get_option('oc2wpbm_table_description_label');
$table_tags=get_option('oc2wpbm_table_tags_label');
$table_lastmodified = get_option('oc2wpbm_table_lastmodified_label');
$tablescript=stripslashes(get_option('oc2wpbm_table_script'));

$tableoutput ="";

$tableoutput .= "<table " . $tablepre .">";
$tableoutput .= "<thead> <tr> ";
if(get_option('oc2wpbm_table_number_display')=='1'){
  $tableoutput .= "<th class='column-1'> ".$table_number ." </th>"; 
  }
if(get_option('oc2wpbm_table_title_display')=='1'){
  $tableoutput .= "<th class='column-2'>" .$table_title ." </th>";
  }
if(get_option('oc2wpbm_table_description_display')=='1'){
  $tableoutput .= "<th class='column-3'> ".$table_description. " </th>";
  }
if(get_option('oc2wpbm_table_tags_display')=='1'){
  $tableoutput .= "<th class='column-4'> ".$table_tags. " </th>";
  }
if(get_option('oc2wpbm_table_lastmodified_display')=='1'){
  $tableoutput .= "<th class='column-5'> ".$table_lastmodified. " </th>";
  }
$tableoutput .= "</tr></thead>";
$tableoutput .= "<tbody>";
	  
  for ($i=0; $i<count($bookmarks); $i++){
  $tags = $bookmarks[$i] ->tags;
  
  $tableoutput .= "<tr>";
    if(get_option('oc2wpbm_table_number_display')=='1'){
      $tableoutput .= "<td class='column-1'>" . ($i+1) . "</td>";
      }
    if(get_option('oc2wpbm_table_title_display')=='1'){
      $tableoutput .= "<td class='column-2'> <a href ='" . $bookmarks[$i]->link . "' target='_blank'> ".$bookmarks[$i]->title . "</a> </td>";
      }
    if(get_option('oc2wpbm_table_description_display')=='1'){
      $tableoutput .= "<td class='column-3'>" . $bookmarks[$i]->description . " </td>";
      }
    if(get_option('oc2wpbm_table_tags_display')=='1'){
      $tableoutput .= "<td class='column-4'>"; 
	// ensure that the tag 'public' is not displayed and a commata is used as separator
	for($j=0; $j<count($tags); $j++){
	if (strtolower($tags[$j])!='public') {$tableoutput .= $tags[$j];
	if ($j+1<count($tags)){$tableoutput .= ", "; }}
	}
      $tableoutput .= " </td>";
      }
    if(get_option('oc2wpbm_table_lastmodified_display')=='1'){
      $tableoutput .= "<td class='column-5'>" . date("Y-m-d", $bookmarks[$i]->dateLastModified) . " </td>";
      }
  $tableoutput .= "</tr>";
  }

$tableoutput .= "</tbody>";
$tableoutput .= "</table>";
$tableoutput .= $tablescript;

return $tableoutput;
}

//copies those Bookmarks out of $bookmarks into a new array that have not all tags contained in $tagArray. Unfortunatley unset() left articfacts in the array so that this copy-into-a-new-array-approach was chosen.
function oc2wpbm_filterBookmarks($bookmarks, $tagArray) {
  $j=0;
  for ($i=0; $i<count($bookmarks); $i++){
    if(array_diff($tagArray, $bookmarks[$i]->tags)==null){
      $newBookmarks[$j]=$bookmarks[$i];
      $j=$j+1;      
     };
  }

return $newBookmarks;
}

/* Coordinates the mehod call related to the operation mode & the connector and returns the HTML code which replaces the shortcode in pages and posts
   Parameter: $tags = tags the Bookmark should contain
	      $connector: AND = Bookmarks that have one of the given tags (default case) | OR = Bookmarks that contain the set of given tags
	      $order: ASC = List of Bookmarks is ordered by 'last modified date' ascending (default case) | DESC = List of Bookmarks is ordered by 'last modified date' descending

*/
function oc2wpbm_shortcode($atts) {
  $shortcodeArray = shortcode_atts( array('tags' => 'public', 'connector' => 'OR','order' => 'asc',), $atts );
  // free shortcode from spaces next to the commata...
  $tagsText=$shortcodeArray['tags'];
  $tagsText = ereg_replace (', ', ',', $tagsText );
  $tagsText = ereg_replace (' , ', ',', $tagsText );
  $tagsText = ereg_replace (' ,', ',', $tagsText );
  //...  and transform the commata separated tags into an array  
  $tagArray = explode(',', $tagsText);
      
  if(get_option('oc2wpbm_op_type')=='sql'){
    $bookmarks = getBMfromSQL($tagArray, $shortcodeArray['order']);
  }
  
  if(get_option('oc2wpbm_op_type')=='ocApp'){
    $bookmarks = getBMfromOC($tagArray, $shortcodeArray['order']);
  }
  
  //while the OR connector needs no further operations (all Bookmarks can be deployed in the table), the AND connector requires to delete within the $bookmark array all those bookmarks that contain not all Bookmarks
  if(strcasecmp($shortcodeArray['connector'],'AND')==0){
  $bookmarks = oc2wpbm_filterBookmarks($bookmarks, $tagArray);
  }
  
      $output = $output . oc2wpbm_tablegenerator($bookmarks);
  return $output;
}

/* Hooks shortcode oc2wpbm into WordPress*/
add_shortcode('oc2wpbm', 'oc2wpbm_shortcode');


/* hook configuration page into the setting area of the wordpress backend*/
function oc2wpbm_plugin_menu()
{
add_options_page('owncCloud 2 WordPress Bookmarks', 'OC2WP Bookmarks', 'manage_options', __FILE__, 'oc2wpbm_configuration_page');
}

add_action('admin_menu', 'oc2wpbm_plugin_menu');
?>
