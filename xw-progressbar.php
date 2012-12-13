<?php
/*
Plugin Name: xwolf Progress Bar
Plugin URI: http://piratenkleider.xwolf.de/plugins/
Description: Displays a textbox with progressbars into a widget or a textbox. 
Content for those bars will get by a simple csv-file on a given URL. 
Version: 1.2
Author: xwolf
Author URI: http://blog.xwolf.de
License: GPL2
*/


/**
 * Define update URL
 */
define("XW_PROGRESSBAR_URL", '');
define("XW_PROGRESSBAR_CACHETIME", 30 * 60);
define("XW_PROGRESSBAR_SUM", 0);
define("XW_PROGRESSBAR_HTML5", 0);
define("XW_PROGRESSBAR_DISPLAYNUMBER", 1);
define("XW_PROGRESSBAR_COLOR", 'green');
define("XW_PROGRESSBAR_ROUNDED", 1);
define("XW_PROGRESSBAR_NUMBERBAR",1);



function xw_progressbar_init() {
	$xw_progressbar_path = plugin_dir_url( __FILE__ );
	if ( !is_admin() ) { // don't load this if we're in the backend
		wp_register_style( 'xw_progressbar_css', $xw_progressbar_path . 'css/xw_progressbar.css' );
		wp_enqueue_style( 'xw_progressbar_css' );
	}
        load_plugin_textdomain('xw-progressbar', '', dirname(plugin_basename(__FILE__)) . '/lang' ); 
}
add_action( 'init', 'xw_progressbar_init' );


/**
 * Install or update plugin
 */
function xw_progressbar_install() {
	
}
/**
 * deactivate plugin
 */
function xw_progressbar_uninstall() {
	delete_option( "xw_progressbar_data" );
}
/*
 * Create progress bars by array
 */
function xw_progressbar_create($data,$attr) {
    if (!is_array($data)) {
        return "No Data";
    }
    if (is_array($attr)) {
        if (isset($attr['total'])) {
            $display_sum = $attr['total'];
        } 
        if (isset($attr['color'])) {
           $display_barcolor = $attr['color'];
        } 
        if (isset($attr['numbers'])) {
           $displaynumber = intval($attr['numbers']);
        } 
        if (isset($attr['numberbar'])) {
           $numberbar = intval($attr['numberbar']);
        }
         if (isset($attr['unitstr'])) {
           $unit = $attr['unitstr'];
        } else {
            $unit = '';
        }
         if (isset($attr['html5'])) {
            $htmltyp = intval($attr['html5']);       
        }
        if (isset($attr['rounded'])) {
            $rounded = intval($attr['rounded']);       
        }
    }   
    if (!isset($display_sum)) $display_sum = XW_PROGRESSBAR_SUM;
    if (!isset($display_barcolor)) $display_barcolor = XW_PROGRESSBAR_COLOR;
    if (!isset($htmltyp)) $htmltyp = XW_PROGRESSBAR_HTML5;
    if (!isset($displaynumber)) $displaynumber = XW_PROGRESSBAR_DISPLAYNUMBER;    
    if (!isset($rounded)) $rounded = XW_PROGRESSBAR_ROUNDED;    
    if (!isset($numberbar)) $numberbar = XW_PROGRESSBAR_NUMBERBAR;
    
    if ($displaynumber==0) {$numberbar =0;}
    
    
    $result = '';
    $result .=  "<div class=\"xw-progressbar";   
  
    if ((isset($rounded)) && ($rounded==1)) {
        $result .=  " rounded";
    }
    if ((isset($numberbar)) && ($numberbar==1)) {
        $result .=  " numberbar";
    }
    $result .=  "\"";   
    if ((isset($attr['width'])) && (intval($attr['width'])>0)) {
        $result .=  " style=\"width: ".$attr['width']."\"";
    }
    
    $result .=  ">";
        $summe = 0;
        $wert = 0;
        foreach ($data as $value)  {                       
            $value = trim($value);
            if (strlen($value) > 1) { 
                
                $parts = mb_split(";", $value);
                
                $parts[0] = strip_tags($parts[0]);
                $result .=  "<h3>$parts[0]</h3>";
                $parts[1] = floatval($parts[1]);
                $parts[2] = floatval($parts[2]);
                
                $number =  intval($parts[1]);
                $barmax = intval($parts[2]);
                $summe = $summe + $parts[2];
                $wert = $wert + $parts[1];
                        
                if ($barmax < $number) {$number=$barmax;}
               
                if ($htmltyp==1) {
                     $result .=  "<div>";
                    $result .= "<progress value=\"$number\" max=\"$barmax\"></progress>";
                    if ($displaynumber==1) {
                        $result .=  "<span class=\"number\">$parts[1] / $parts[2] $unit</span>";
                    }
                    $result .=  "</div>";    

                } else {
                    if ($parts[2]==0) $parts[2]=1;                    
                    $percent = intval( ($parts[1] * 100) / $parts[2]);
                    if ($percent>100) {$percent = 100;}
                    $result .= "<div class=\"meter";
                    if (isset($display_barcolor)) {
                       $result .= " $display_barcolor";
                    }
                    $result .= "\">";
                    $result .= "<span style=\"width: $percent%";
        
                    $result .= "\" ></span>";
                    $result .=  "</div>";
                    if ($displaynumber==1) {
                        $result .=  "<span class=\"number\">$parts[1] / $parts[2] $unit</span>";
                    }    
                }
                
            }                                                                                            
        }
        if ($display_sum ==1) {
            $wertint = intval($wert);
            if ($wertint>$summe) { $wertint = $summe; }
            $result .=  "<div class=\"gesamt\">";
            $result .=  "<h3>".__( 'Total', 'xw-progressbar' )."</h3>";
            if ($htmltyp==1) {
                $result .=  "<div><progress value=\"$wertint\" max=\"$summe\"></progress>"; 
                if ($displaynumber==1) {
                    $result .=  "<span class=\"number\">$wert / $summe $unit</span>";
                }
                $result .=  "</div>";
            } else {
                $percent = intval($wert * 100 / $summe);
                if ($percent>100) {$percent = 100;}
                $result .= "<div class=\"meter";
                    if (isset($display_barcolor)) {
                       $result .= " $display_barcolor";
                    }
                    $result .= "\">";
                $result .= "<span style=\"width: $percent%";               
                $result .= "\" ></span>";
                $result .=  "</div>";
                if ($displaynumber==1) {
                    $result .=  "<span class=\"number\">$wert / $summe $unit</span>"; 
                }    
            }
            $result .=  "</div>";
        }
    $result .=  "</div>";
    return $result;
}

/*
 * Get Data by URL
 */
function xw_progressbar_getdata($url = XW_PROGRESSBAR_URL) {       
  
      $data =  get_option("xw_progressbar_data");
      $cacheddata = $data["$url"]['data'];
      $lastcheck = $data["$url"]['time'];
       
    if (!isset($data) || !is_array($cacheddata) || (!isset($lastcheck )) || (($lastcheck + XW_PROGRESSBAR_CACHETIME) < time())) {                    
        
        $response = wp_remote_get($url); 
        // Check for errors
        if ( false == is_wp_error( $response ) && 200 == $response['response']['code'] && isset( $response['body'] ) ):	
                $thisstring =  $response['body'];
                if ( seems_utf8( $thisstring ) == false ) $thisstring = utf8_encode( $thisstring );
                $balken =  split("[\n\r]",$thisstring);               
                if (is_array($balken)) {
                    $cacheddata = $balken;                                       
                    $lastcheck = time();
                    $data["$url"]['data'] =  $balken;      
                    $data["$url"]['time'] =  time(); 
                    update_option( "xw_progressbar_data", $data );

                } 
        endif;

    }
    return $cacheddata;
}

/* 
 * Define Shortcodes
 */
function xw_progressbar_shortcode ($atts ) {
	extract( shortcode_atts( array(
		'url' => XW_PROGRESSBAR_URL,	// default url
		'color' => XW_PROGRESSBAR_COLOR,// Default color
		'total' => XW_PROGRESSBAR_SUM,	// Show total
                'html5' => XW_PROGRESSBAR_HTML5, // use HTML5 progress bar 
                'numbers' => XW_PROGRESSBAR_DISPLAYNUMBER, // Show numbers  
                'unitstr' => '',	// Optional Unit-String
                'width'  => '',
                'rounded' => XW_PROGRESSBAR_ROUNDED, // Show round corners and bars
                'numberbar' => XW_PROGRESSBAR_NUMBERBAR
        ), $atts ) );
        
       
	 $balken = xw_progressbar_getdata($atts['url']);
         $html = xw_progressbar_create($balken,$atts); 
        
         return $html;
        
}
add_shortcode('progressbar','xw_progressbar_shortcode');

/**
 * Adds xw_progressbar_Widget widget.
 */
class xw_progressbar_Widget extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'xw_progressbar_Widget', // Base ID
                        __( 'Progress Bar', 'xw-progressbar' ),
			array( 'description' => __( 'Displays one or more progress bars defined by an URL', 'xw-progressbar' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {            
           extract( $args );
	   $title = apply_filters( 'widget_title', $instance['title'] );
           $url =  $instance['url'];
           if (!isset($url)) {
               $url = XW_PROGRESSBAR_URL;
           }

            // Check if blacklist has entries
             echo $before_widget;                
             echo $before_title.$title.$after_title;
            
             $balken = xw_progressbar_getdata($url);
             $html = xw_progressbar_create($balken,$instance); 
             echo $html;

            echo $after_widget;

	}
        /**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
                $instance['url'] = strip_tags( $new_instance['url'] );
                $instance['color'] = strip_tags( $new_instance['color'] );
                $instance['unitstr'] = wp_filter_kses( $new_instance['unitstr'] );
                $instance['rounded'] = strip_tags( $new_instance['rounded'] );
                $instance['total'] = strip_tags( $new_instance['total'] );
                $instance['numbers'] = strip_tags( $new_instance['numbers'] );
                $instance['numberbar'] = strip_tags( $new_instance['numberbar'] );
                $instance['html5'] = strip_tags( $new_instance['html5'] );
		return $instance;
	}

	/**
	 * Back-end widget form.
	 * @see WP_Widget::form()
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'Progress Bar', 'xw-progressbar' );
		}
                if ( isset( $instance[ 'url' ] ) ) {
			$url = $instance[ 'url' ];
		} else {
			$url = XW_PROGRESSBAR_URL;
		}
                if ( isset( $instance[ 'unitstr' ] ) ) {
			$unitstr = $instance[ 'unitstr' ];
		} else {
			$unitstr = '';
		}
                if ( isset( $instance[ 'color' ] ) ) {
			$color = $instance[ 'color' ];		
                } else {
                    $color = XW_PROGRESSBAR_COLOR;
                }
                if ( isset( $instance[ 'rounded' ] ) ) {
			$rounded = $instance[ 'rounded' ];
		} 
                if ( isset( $instance[ 'total' ] ) ) {
			$total = $instance[ 'total' ];
		} else {
                    $total = XW_PROGRESSBAR_SUM;
                }
                if ( isset( $instance[ 'html5' ] ) ) {
			$html5 = $instance[ 'html5' ];
		}
                 if ( isset( $instance[ 'numbers' ] ) ) {
			$numbers = $instance[ 'numbers' ];
		} else {
                    $numbers = XW_PROGRESSBAR_DISPLAYNUMBER;
                }
                 if ( isset( $instance[ 'numberbar' ] ) ) {
			$numberbar = $instance[ 'numberbar' ];
		} else {
                    $numberbar = XW_PROGRESSBAR_NUMBERBAR;
                }
                ?>
                 <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'xw-progressbar' ); ?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
                </p>
                <p>
                <label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e( 'URL:', 'xw-progressbar' ); ?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" type="text" value="<?php echo esc_attr( $url ); ?>" />
                </p>
               
                <h5><?php _e( 'Optional Settings', 'xw-progressbar' ); ?></h5>
                 <p>
                <label for="<?php echo $this->get_field_id( 'unitstr' ); ?>"><?php _e( 'Text for Unit-Numbers:', 'xw-progressbar' ); ?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'unitstr' ); ?>" name="<?php echo $this->get_field_name( 'unitstr' ); ?>" type="text" value="<?php echo esc_attr( $unitstr ); ?>" />
                </p>
                <p>
                           
                    <input id="<?php echo $this->get_field_id( 'rounded' ); ?>" 
                            name="<?php echo $this->get_field_name( 'rounded' ); ?>" 
                            type="checkbox" value="1" <?php echo checked($rounded,1,false ); ?> />
                    <label for="<?php echo $this->get_field_id( 'rounded' ); ?>"><?php _e( 'Use rounded corners', 'xw-progressbar' ); ?></label> 
                </p>
                <p>        
                     <input id="<?php echo $this->get_field_id( 'total' ); ?>" 
                            name="<?php echo $this->get_field_name( 'total' ); ?>" 
                            type="checkbox" value="1" <?php echo checked($total,1,false ); ?> />
                    <label for="<?php echo $this->get_field_id( 'total' ); ?>"><?php _e( 'Show total bar', 'xw-progressbar' ); ?></label> 
                </p>
                 <p>        
                     <input id="<?php echo $this->get_field_id( 'numbers' ); ?>" 
                            name="<?php echo $this->get_field_name( 'numbers' ); ?>" 
                            type="checkbox" value="1" <?php echo checked($numbers,1,false ); ?> />
                    <label for="<?php echo $this->get_field_id( 'numbers' ); ?>"><?php _e( 'Show numbers', 'xw-progressbar' ); ?></label> 
                </p>
                <p>        
                     <input id="<?php echo $this->get_field_id( 'numberbar' ); ?>" 
                            name="<?php echo $this->get_field_name( 'numberbar' ); ?>" 
                            type="checkbox" value="1" <?php echo checked($numberbar,1,false ); ?> />
                    <label for="<?php echo $this->get_field_id( 'numberbar' ); ?>"><?php _e( 'No break between bars and numbers', 'xw-progressbar' ); ?></label> 
                </p>
                <p>        
                     <input id="<?php echo $this->get_field_id( 'html5' ); ?>" 
                            name="<?php echo $this->get_field_name( 'html5' ); ?>" 
                            type="checkbox" value="1" <?php echo checked($html5,1,false ); ?> />
                    <label for="<?php echo $this->get_field_id( 'html5' ); ?>"><?php _e( 'Use HTML5 progress bars', 'xw-progressbar' ); ?></label> 
                </p>
                
                <?php 
                 echo "<select name=\"".$this->get_field_name( 'color' )."\">\n";
                 $list = array("blue","orange","green","red");
                 
                foreach($list as $i) {   
                    echo "\t\t\t\t";
                    echo '<option value="'.$i.'"';
                    if ( $i == $color ) {
                        echo ' selected="selected"';
                    }                                                                                                                                                                
                    echo '>';
                    echo $i;                        
                    echo '</option>';                                                                                                                                                              
                    echo "\n";                                            
                }  
                echo "</select>\n";                                   
                echo "\t\t\t";
                echo "<label for=\"".$this->get_field_name( 'color' )."\">".__( 'Color for bars', 'xw-progressbar' )."</label>\n"; 
                
                
	}
 
        

} // End class xw_progressbar_Widget Widget
//
// register widget
add_action( 'widgets_init', create_function( '', 'register_widget( "xw_progressbar_Widget" );' ) );


/**
 * Add filters and actions
 */

register_activation_hook(__FILE__, 'xw_progressbar_install');
register_deactivation_hook(__FILE__, 'xw_progressbar_uninstall');