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

/*import the class file for Bookmark Class*/
require_once( plugin_dir_path( __FILE__ ) . 'bookmark.inc.php' );
require_once( plugin_dir_path( __FILE__ ) . 'config_page.inc.php' );

/* get bookmarks in accordance to the defined tag and the specified user (as owner of the bookmarks) out of the database and return an array of bookmarks*/
function getBMfromSQL($tag){
  /*configure SQL Server connection data*/
  $sql_server=get_option('oc2wpbm_sql_server');
  $sql_user =$sqlserver=get_option('oc2wpbm_sql_user');
  $sql_password =$sqlserver=get_option('oc2wpbm_sql_password');
  $oc_database=$sqlserver=get_option('oc2wpbm_sql_database');
  
  $dsort = 'b.lastmodified ASC';
  if (get_option('oc2wpbm_table_sort')=='date_desc'){$dsort='b.lastmodified DESC';}
  
  echo 'Sortierung' .$dsort;
  

  $bm_term="%". $tag."%";
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
  $query=$OCdb->prepare("select b.url, b.title, b.description from oc_bookmarks b INNER JOIN oc_bookmarks_tags t on t.bookmark_id=b.id WHERE t.tag COLLATE UTF8_GENERAL_CI LIKE %s AND b.user_id LIKE %s ORDER BY %s", $bm_term, $bm_user, $dsort);
  
  $res = $OCdb->get_results($query);
      
  /*create array containing BM objects*/
  for ($i=0; $i<count($res); $i++){
	$bookmarks[$i]=new bookmark($res[$i] ->title, $res[$i] ->url, $res[$i] ->description );
      }
  
    return $bookmarks;
}

/* get bookmarks in accordance to the defined tag out of ownCloud via the Bookmarks App*/
function getBMfromOC($tag){
$sort = 'asc';
if (get_option('oc2wpbm_table_sort')=='date_desc'){$sort='desc';}
echo "DAS TAG IST:" . $tag ;
echo "<br> sort:" .$sort;

$response = wp_remote_post( get_option('oc2wpbm_oc_server'), array(
	'method' => 'POST',
	'timeout' => 45,
	'redirection' => 5,
	'httpversion' => '1.0',
	'blocking' => true,
	'headers' => array(),
	'body' => array( 'user' => get_option('oc2wpbm_oc_user'), 'password' => get_option('oc2wpbm_oc_password'), 'tags' => array($tag), 'description' => true, 'datesort'=>$sort),
	'cookies' => array()
    )
);

$result = json_decode($response['body']);

for ($i=0; $i<count($result); $i++){
$bookmarks[$i]=new bookmark($result[$i] ->title, $result[$i] ->url, $result[$i] ->description);
}
return $bookmarks;
}

/* Generates the HTML Code for a table containing bookmark information*/
function oc2wpbm_tablegenerator($bookmarks){

$tablepre=stripslashes(get_option('oc2wpbm_table_styling'));
$table_number=get_option('oc2wpbm_table_number');
$table_title=get_option('oc2wpbm_table_title');
$table_description=get_option('oc2wpbm_table_description');
$tablescript=stripslashes(get_option('oc2wpbm_table_script'));

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

/* Coordinates the mehod call related to the operation mode and returns the HTML code which replaces the shortcode in pages and posts*/
function oc2wpbm_shortcode($atts) {
  $tagArray = shortcode_atts( array('tag' => 'public',), $atts );
  $bmArray;
  
  if(get_option('oc2wpbm_op_type')=='sql'){
    $bookmarks = getBMfromSQL("{$tagArray['tag']}");
    $output = $output . oc2wpbm_tablegenerator($bookmarks);
  return $output;
  }
  
  if(get_option('oc2wpbm_op_type')=='ocApp'){
    $bookmarks = getBMfromOC("{$tagArray['tag']}");
    $output = $output . oc2wpbm_tablegenerator($bookmarks);
  return $output;
  }
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
