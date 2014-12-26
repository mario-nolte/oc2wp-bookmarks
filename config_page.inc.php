<?php
function oc2wpbm_configuration_page(){

    if (isset($_POST['info_update']))
    {
        echo '<div id="message" class="updated fade"><p><strong>';

        update_option('oc2wpbm_op_type', (string)$_POST["oc2wpbm_op_type"]);
        update_option('oc2wpbm_oc_server', (string)$_POST["oc2wpbm_oc_server"]);
        update_option('oc2wpbm_oc_user', (string)$_POST["oc2wpbm_oc_user"]);
        update_option('oc2wpbm_oc_password', (string)$_POST["oc2wpbm_oc_password"]);
        update_option('oc2wpbm_sql_server', (string)$_POST["oc2wpbm_sql_server"]);
        update_option('oc2wpbm_sql_user', (string)$_POST["oc2wpbm_sql_user"]);
        update_option('oc2wpbm_sql_password', (string)$_POST["oc2wpbm_sql_password"]);
        update_option('oc2wpbm_sql_database', (string)$_POST["oc2wpbm_sql_database"]);
        update_option('oc2wpbm_sql_bmOwner', (string)$_POST["oc2wpbm_sql_bmOwner"]);
        update_option('oc2wpbm_table_styling', $_POST["oc2wpbm_table_styling"]);
        update_option('oc2wpbm_table_number_display', ($_POST['oc2wpbm_table_number_display']=='1') ? '1':'-1' );
        update_option('oc2wpbm_table_number_label', $_POST["oc2wpbm_table_number_label"]);
        update_option('oc2wpbm_table_title_display', ($_POST['oc2wpbm_table_title_display']=='1') ? '1':'-1' );
        update_option('oc2wpbm_table_title_label', $_POST["oc2wpbm_table_title_label"]);
        update_option('oc2wpbm_table_description_label', $_POST["oc2wpbm_table_description_label"]);
        update_option('oc2wpbm_table_description_display', ($_POST['oc2wpbm_table_description_display']=='1') ? '1':'-1' );
        update_option('oc2wpbm_table_tags_label', $_POST["oc2wpbm_table_tags_label"]);
        update_option('oc2wpbm_table_tags_display', ($_POST['oc2wpbm_table_tags_display']=='1') ? '1':'-1' );
        update_option('oc2wpbm_table_lastmodified_label', $_POST["oc2wpbm_table_lastmodified_label"]);
        update_option('oc2wpbm_table_lastmodified_display', ($_POST['oc2wpbm_table_lastmodified_display']=='1') ? '1':'-1' );
        update_option('oc2wpbm_table_script', $_POST["oc2wpbm_table_script"]);
                                
        echo 'Options Updated!';
        echo '</strong></p></div>';
    }
    
    $oc2wpbm_op_type = stripslashes(get_option('oc2wpbm_op_type'));
									  
?>

<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" id="oc2wpoptions" class="validate">
<input type="hidden" name="info_update" id="info_update" value="true" />
 
<script language="javascript" type="text/javascript">
  function toggleDisableOC(radio) {
    var toggle1 = document.getElementById("ocAppOptions");
    radio.checked ? toggle1.disabled = false : toggle1.disabled = true;
    var toggle2 = document.getElementById("sqlOptions");
    radio.checked ? toggle2.disabled = true : toggle2.disabled = false;
  }
    function toggleDisableSQL(radio) {
    var toggle1 = document.getElementById("ocAppOptions");
    radio.checked ? toggle1.disabled = true : toggle1.disabled = false;
    var toggle2 = document.getElementById("sqlOptions");
    radio.checked ? toggle2.disabled = false : toggle2.disabled = true;
  }
</script>


<h2>ownCloud2WordPress Bookmarks sharing options</h2>
     <p>
    <h3>Plugin Usage:</h3>

    <p>To make use of this plugin please consider the following steps:</p>
    <ol>
    <li>Chose the operation mode:
    <ol>
      <li> OC App mode is recommended and for those that have the <a href="https://github.com/owncloud/Bookmarks" target="_blank"> ownCoud Bookmark App supporting REST </a> running on their owncloud.</li>
      <li> MySQL mode is for those who have access to the MySQL Database of their ownCloud instance and that wish to make use of bookmarks of several users.</li>
    </li></ol>
    <li>Enter the data to connect to the ownCloud Bookmarks App or to the MySQL Database. </li>
    <li>Add the shortcode <strong>[oc2wpbm]</strong> to a post or page that should contain a table with those bookmarks that have the tag 'public' or add the shortcode <strong>[oc2wpbm tags="public, example"]</strong> to display Bookmarks that have one of those tags. Bookmarks that have both tags can be selected via <strong>[oc2wpbmtags=”public, example” connector=”AND”]</strong>
    <li>Configure the design of the table e. g. like explained <a href="http://www.nolte-netzwerk.de/oc2wp-bookmarks-configuration/#configure the table layout" target="_blank"> in this tutorial </a>.</li>
    </ol>
    </p>
  <br>
  <HR>
  <br>
<fieldset>
<legend><h3>Operation mode</h3></legend>
<p>Please chose if you use the Owncloud APP or if Bookmarks should be retrieved by using the MySQL database of owncloud.</p>
<table width="100%" border="0" cellspacing="0" cellpadding="6">
<tr valign="top">
    <td width="25%" align="right">
      OC App:
    </td>
    <td align="left">
    <?php _e('<input type="radio" name="oc2wpbm_op_type" value="ocApp" onchange="toggleDisableOC(this);" id="opOcApp"') ?>
    <?php if ($oc2wpbm_op_type == "ocApp") echo " checked " ?>
    <?php _e('/>') ?>
  <tr valign="top">
    <td width="25%" align="right">
      MySQL:
    </td>
    <td align="left">
    <?php _e('<input type="radio" name="oc2wpbm_op_type" value="sql" onchange="toggleDisableSQL(this);" id="opSQL"') ?>
    <?php if ($oc2wpbm_op_type == "sql") echo " checked "  ?>
    <?php _e('/>') ?>
</td>
</tr>
</table>
</fieldset>

<?php _e('<fieldset id="ocAppOptions"') ?>
<?php if ($oc2wpbm_op_type == "sql") echo " disabled" ?>
<?php _e('/>') ?>

<legend><h3>Owncloud App Options</h3></legend>

<table width="100%" border="0" cellspacing="0" cellpadding="6">
    
<tr valign="top">
    <td width="25%" align="right">
      OC App URL:
    </td>
    <td align="left">
      <input name="oc2wpbm_oc_server" type="text" size="100" value="<?php echo get_option('oc2wpbm_oc_server'); ?>"/>
    </td>
</tr>
<tr valign="top">
    <td width="25%" align="right">
      User:
    </td>
    <td align="left">
      <input name="oc2wpbm_oc_user" type="text" size="25" value="<?php echo get_option('oc2wpbm_oc_user'); ?>"/>
    </td>
</tr>
<tr valign="top">
    <td width="25%" align="right">
      Password:
    </td>
    <td align="left">
      <input name="oc2wpbm_oc_password" type="password" size="25" value="<?php echo get_option('oc2wpbm_oc_password'); ?>"/>
    </td>
</tr>
</tr>
<tr>
<td>
</td>
  <td>
  <?php
  $response = wp_remote_post( get_option('oc2wpbm_oc_server'), array(
	  'method' => 'POST',
	  'timeout' => 45,
	  'redirection' => 5,
	  'httpversion' => '1.0',
	  'blocking' => true,
	  'headers' => array(),
	  'body' => array( 'user' => get_option('oc2wpbm_oc_user'), 'password' => get_option('oc2wpbm_oc_password')),
	  'cookies' => array()
      )
  );
  
  if(is_array($response)){
  $result = json_decode($response['body']);
  
  /*echo $result[0] ->url;*/
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
  }
?>
  </td>
</tr>
</table>
</fieldset>

        
<?php _e('<fieldset id="sqlOptions"') ?>
<?php if ($oc2wpbm_op_type == "ocApp") echo " disabled" ?>
<?php _e('/>') ?>

<label> <h3>SQL-Options</h3></label>
<p>To access the owncloud database it is highly recommended to create an own user that has limited access to the database like described in this  tutorial. Please fill the following fields to enter the access data for the database. </p>
<table width="100%" border="0" cellspacing="0" cellpadding="6">

<tr valign="top">
    <td width="25%" align="right">
      SQL server:
    </td>
    <td align="left">
      <input name="oc2wpbm_sql_server" type="text" size="25" value="<?php echo get_option('oc2wpbm_sql_server'); ?>"/>
    </td>
</tr>

<tr valign="top">
    <td width="25%" align="right">
      SQL user:
    </td>
    <td align="left">
      <input name="oc2wpbm_sql_user" type="text" size="25" value="<?php echo get_option('oc2wpbm_sql_user'); ?>"/>
    </td>
</tr>

<tr valign="top">
    <td width="25%" align="right">
      SQL password:
    </td>
    <td align="left">
      <input name="oc2wpbm_sql_password" type="password" size="25" value="<?php echo get_option('oc2wpbm_sql_password'); ?>"/>
    </td>
</tr>

<tr valign="top">
    <td width="25%" align="right">
      Name of database:
    </td>
    <td align="left">
      <input name="oc2wpbm_sql_database" type="text" size="25" value="<?php echo get_option('oc2wpbm_sql_database'); ?>"/>
    </td>
</tr>

<tr valign="top">
    <td width="25%" align="right">
      Bookmark owner:
    </td>
    <td align="left">
      <input name="oc2wpbm_sql_bmOwner" type="text" size="25" value="<?php echo get_option('oc2wpbm_sql_bmOwner'); ?>"/><br>
      <i>To display only the bookmarks of a certain owner please enter the username here. Otherwise please enter "all": Bookmarks of all users containing the specified tag will be displayed. </i>
    </td>
    </tr>

</table>
</fieldset>

<fieldset>
<legend><h3>Table layout options</h3></legend>
<table width="100%" border="0" cellspacing="0" cellpadding="6">

<tr valign="top">
    <td width="25%" align="right">
      Table styling options:
    </td>
    <td align="left">
      <input name="oc2wpbm_table_styling" type="text" size="100" value="<?php echo stripslashes(get_option('oc2wpbm_table_styling')); ?>" />
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="6">


<tr valign="top">
    <td width="25%" align="right">
      Display:
    </td>
    <td>
      <input name="oc2wpbm_table_number_display" type="checkbox"<?php if(get_option('oc2wpbm_table_number_display')!='-1') echo ' checked="checked"'; ?> value="1"/>
    </td>
    <td>
      <input name="oc2wpbm_table_title_display" type="checkbox"<?php if(get_option('oc2wpbm_table_title_display')!='-1') echo ' checked="checked"'; ?> value="1"/>
    </td>
        <td>
      <input name="oc2wpbm_table_description_display" type="checkbox"<?php if(get_option('oc2wpbm_table_description_display')!='-1') echo ' checked="checked"'; ?> value="1"/>
    </td>
        <td>
      <input name="oc2wpbm_table_tags_display" type="checkbox"<?php if(get_option('oc2wpbm_table_tags_display')!='-1') echo ' checked="checked"'; ?> value="1"/>
    </td>
        <td>
      <input name="oc2wpbm_table_lastmodified_display" type="checkbox"<?php if(get_option('oc2wpbm_table_lastmodified_display')!='-1') echo ' checked="checked"'; ?> value="1"/>
    </td>
</tr>

<tr valign="top">
    <td width="25%" align="right">
      Labeling:
    </td>
    
    <td align="left" style="width:20px;">
      <input name="oc2wpbm_table_number_label" type="text" size="15" value="<?php echo get_option('oc2wpbm_table_number_label'); ?>"/><br>
      <i>Number</i>
    </td>

    <td align="left" style="width:100px;">
      <input name="oc2wpbm_table_title_label" type="text" size="15" value="<?php echo get_option('oc2wpbm_table_title_label'); ?>"/><br>
      <i>Title</i>
    </td>

    <td align="left" style="width:100px;">
      <input name="oc2wpbm_table_description_label" type="text" size="15" value="<?php echo get_option('oc2wpbm_table_description_label'); ?>"/><br>
      <i>Description</i>
    </td>
    <td align="left" style="width:100px;">
      <input name="oc2wpbm_table_tags_label" type="text" size="15" value="<?php echo get_option('oc2wpbm_table_tags_label'); ?>"/><br>
      <i>Tags</i>
    </td>
    <td align="left" style="width:100px;">
      <input name="oc2wpbm_table_lastmodified_label" type="text" size="15" value="<?php echo get_option('oc2wpbm_table_lastmodified_label'); ?>"/><br>
      <i>Date last modified</i>
    </td>
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="6">
<tr valign="top">
    <td width="25%" align="right">
      Table script:
    </td>
    <td align="left">
      <textarea name="oc2wpbm_table_script" cols="100" rows="10"> <?php echo stripslashes(get_option('oc2wpbm_table_script')); ?> </textarea>
    </td>

</tr>

</table>
</fieldset>    
  

  <p class="submit"><input type="submit" name="inf_update" id="submit" class="button" value="<?php _e('Update options'); ?> &raquo;"></p>
   </form>
   Please visit<a href="http://www.nolte-netzwerk.de/oc2wp-bookmarks-configuration/" target="_blank"> the documentation </a> to read more about the use and configuration of this plugin.<br/>


<?php
} 
