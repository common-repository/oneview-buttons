<?php
/*  Copyright 2008  oneviewGMBH  (email : kontakt@oneview.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
Plugin Name: oneview Buttons
Plugin URI: http://apps.oneview.de/wordpress/oneviewButtons.zip
Description: oneview Buttons
Author: oneview GmbH
Version: 0.1
Author URI: http://oneview.de/
*/

  /**
  *Returns the right URL and title. If the user is on the home page returns the URL and title of the post. If the user is on the post page returns the URL and title of the post
  *@return Javascript string for the onckick attribute
  */  
  function oneviewGetTarget(){
  	//test if the user on the home page
    if (is_home()){
    
      //get the URL and Title of the post
  		$url = urlencode(get_permalink($post->ID));
  		$title	= urlencode(get_the_title($post->ID));

      $target =  "location.href='http://www.oneview.de/quickadd/neu/addBookmark.jsf?URL=$url&title=$title';";
  	} else {
  		//get the URL and Title of the post page
      $target =  "location.href='http://www.oneview.de/quickadd/neu/addBookmark.jsf?URL=' + encodeURIComponent(location.href)+ '&title=' + encodeURIComponent(document.title);";
  	}
    
    //output
  	return $target;
  
  }  
  
  /**
  *Bild the makeup fot the bookmarklet button
  *@param content the content that already exist and will be filterd
  *@return the existing content plst the bookmarkbutton makeup
  */
  
  function oneviewBookmarklet($content) {
        //using the global variable for the buttons
   			$buttons = $GLOBALS['buttons'];  		        
        $options = oneviewGetButtonSettings();
        
        //bild the HTML makeup
        $bookmarklet = "<br /><br /><a onclick=\"".oneviewGetTarget()."  return false;\" href='http://www.oneview.de' target='_top'>";
        
        //taking the button style pendently from the settings
        for($i = 0; $i < sizeof($buttons); $i++){                
            if(htmlspecialchars($options['buttonStyle'], ENT_QUOTES) == $buttons[$i][0]){
                 if ($buttons[$i][1] == "img"){
                       $bookmarklet .= '<img src="wp-content/plugins/oneview-buttons/img/ov_bm_'.$buttons[$i][0].'.gif" style="border: 0" alt="oneview - das merk ich mir!" title="oneview - das merk ich mir!" />';
                 }
                 if ($buttons[$i][1] == "text"){
                       $bookmarklet .= $buttons[$i][2];
                 }   
            }        
        }
        
        
        $bookmarklet .= "</a>";        
        
        //output
        return $content.$bookmarklet;
  }

  /**
  *Adding the oneview setting's Page under the plugin page if it is possible
  */
    
  function oneviewSettingsPage(){
        if ( function_exists('add_submenu_page') ){
        		add_submenu_page('plugins.php', __('oneview Plugin'), __('oneview Plugin'), 1, __FILE__, 'oneviewSettings');
        }
  }
  
  function oneviewSettings(){
      //using the global variable for the buttons
 			$buttons = $GLOBALS['buttons'];  		
      $options = oneviewGetButtonSettings();
      
      //if the user have clicked on "Save", the changes will be saved
  		if ( $_POST['oneviewSubmit'] ) {
  			$options['buttonStyle'] = strip_tags(stripslashes($_POST['oneviewButton']));
        
  			update_option('oneviewButtonOptions', $options);
  		}
     
     $buttonStyle = htmlspecialchars($options['buttonStyle'], ENT_QUOTES);
        ?>
        
              <style type="text/css">
              <!--
              
                  .oneviewBox fieldset{
                  	border: 1px solid #ccc;
                  	padding: .5em 2em;
                  }
                  
                  .oneviewBox fieldset input{
                  	font-size: 20px;
                  	padding: 2px;
                  }
                  
                  .oneviewBox fieldset textarea{
                  	width: 100%;
                  	padding: 2px;
                  }
                  
                  .oneviewBox legend{
                  	font-family: Georgia, "Times New Roman", Times, serif;
                  	font-size: 22px;
                  }
                  
                  .oneviewBox label{
                  	font-weight:bold;
                  }
                  
                  .oneviewBox .oneviewButtonItem{
                    padding-top: 16px;
                  }
                  
                  .oneviewBox .oneviewButtonItem img,
                  .oneviewBox .oneviewButtonItem a
                  {
                    cursor: pointer;
                  }
              
              -->
              </style>

            <div class="wrap">
                <h2>oneview Plugin Einstellungen</h2>
                <p>
                </p>
                <form action="" method="post" class="oneviewBox">
                    <fieldset>
                        <legend>
                            Einstellungen
                        </legend>
              					<p>
              					<!--Hier kommt bisschen Text -->
              					</p>
                        <div>
                            <label>
                                Bitte w&auml;hle ein Design aus:
                            </label>
                            
           <?php
                  
                  //the HTML fot the button choice menu is writen
                  for($i = 0; $i < sizeof($buttons); $i++){
                       echo '<div class="oneviewButtonItem">';
                       echo '<input type="radio" name="oneviewButton" value="'.$buttons[$i][0].'" ';
                       if ($buttons[$i][0] == $buttonStyle){
                          echo 'checked="checked" ';
                       }
                       echo '/>&#160;';
                       if ($buttons[$i][1] == "img"){
                          echo '<img src="../wp-content/plugins/oneview-buttons/img/ov_bm_'.$buttons[$i][0].'.gif"/>';
                       }
                       if ($buttons[$i][1] == "text"){
                          echo '<a>'.$buttons[$i][2].'</a>';
                       }                       
                       echo '</div>';                							                             
                  }
           ?>                                                                                                                                                                                                                                                                                                 
                        <br/>
                        </div>
                        <br/>
                    </fieldset>
                    <p class="submit">
                        <input type="submit" value="Speichern" name="oneviewSubmit"/>
                    </p>
                </form>
            </div>        
        
        <?php
        
  }
  function oneviewGetButtonSettings(){
      //get button option from datebase
      $options = get_option('oneviewButtonOptions');
      //if there is no data already saved, the default date will be used
  		if ( !is_array($options) )
  			$options = array(
  			    'buttonStyle'=>'005',
  			);
       return $options;
  } 
 
//buttons Array
global $buttons;
$buttons = array(
                 array("001", "img"),
                 array("002", "img"),
                 array("003", "img"),
                 array("004", "img"),
                 array("005", "img"),
                 array("text1", "text", "merken!"),
                 array("text2", "text", "Das merk ich mir!"),
                 array("text3", "text", "zu oneview hinzuf&uuml;gen")                                                                                                
);  

//adding the oneview settings page
add_action('admin_menu', 'oneviewSettingsPage');

//filtering the "the_content": adding the bookmarklet button
add_filter("the_content", "oneviewBookmarklet", 1);
?>