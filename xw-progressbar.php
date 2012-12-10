<?php
/*
Plugin Name: xwolf Progress Bar
Plugin URI: http://piratenkleider.xwolf.de/plugins/
Description: Displays a textbox with progressbars into a widget or a textbox. 
Content for those bars will get by a simple csv-file on a given URL. 
Version: 1.0
Author: xwolf
Author URI: http://blog.xwolf.de
License: GPL2
*/


/**
 * Define update URL
 */
define("XW_PROGRESSBAR_URL", "http://blog.tokenbus.de/stand.txt");
define("XW_PROGRESSBAR_CACHETIME", 30*60);
define("XW_PROGRESSBAR_SUM", 1);


function xw_progressbar_init() {
	$xw_progressbar_path = plugin_dir_url( __FILE__ );
	if ( !is_admin() ) { // don't load this if we're in the backend
		wp_register_style( 'xw_progressbar_css', $xw_progressbar_path . 'css/xw_progressbar.css' );
		wp_enqueue_style( 'xw_progressbar_css' );
	}
}
add_action( 'init', 'xw_progressbar_init' );


/**
 * Install or update plugin
 */
function xw_progressbar_install() {
	
}

/*
 * Create progress bars by array
 */
function xw_progressbar_create($data,$attr) {
    if (!is_array($data)) {
        return "No Data";
    }
    if (is_array($attr)) {
        if (isset($attr['sum'])) {
            $display_sum = $attr['sum'];
        } 
        if (isset($attr['barcolor'])) {
            $display_barcolor = $attr['barcolor'];
        } 
    }   
    if (!isset($display_sum)) $display_sum = XW_PROGRESSBAR_SUM;
    
    
    $result = '';
    $result .=  "<div id=\"xw-progressbar\">";   
        $summe = 0;
        $wert = 0;
        foreach ($data as $value)  {                       
            $value = trim($value);
            if (strlen($value) > 1) { 
                $parts = mb_split(";", $value);
                $parts[0] = strip_tags($parts[0]);
                $result .=  "<h3>$parts[0]</h3>";
                $parts[1] = floatval(trim($parts[1]));
                $parts[2] = intval(trim($parts[2]));
                $number =  intval($parts[1]);

                $summe = $summe + $parts[2];
                $wert = $wert + $parts[1];

                $result .=  "<div><progress value=\"$number\" max=\"$parts[2]\"";
                if (isset($display_barcolor)) {
                    $result .= " background-color: \"$display_barcolor\"";
                }
                $result .= "></progress><br>";
                $result .=  "<span>$parts[1] / $parts[2]</span>";
                $result .=  "</div>";
            }                                                                                            
        }
        if ($display_sum ==1) {
            $wertint = intval($wert);
            $result .=  "<div class=\"gesamt\">";
            $result .=  "<h3>".__( 'Total', 'xw_progressbar' )."</h3>";
            $result .=  "<div><progress value=\"$wertint\" max=\"$summe\"></progress><br>";
            $result .=  "<span>$wert / $summe</span>";
            $result .=  "</div>";
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
                        __( 'Progress Bar', 'xw_progressbar' ),
			array( 'description' => __( 'Displays one or more progress bars defined by an URL', 'xw_progressbar' ), ) // Args
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
             $html = xw_progressbar_create($balken); 
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
			$title = __( 'Progress Bar', 'xw_progressbar' );
		}
                if ( isset( $instance[ 'url' ] ) ) {
			$url = $instance[ 'url' ];
		} else {
			$url = '';
		}
                ?>
                 <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'xw_progressbar' ); ?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
                </p>
                <p>
                <label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e( 'URL:', 'xw_progressbar' ); ?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" type="text" value="<?php echo esc_attr( $url ); ?>" />
                </p>
                 <?php               
	}
 
        

} // End class xw_progressbar_Widget Widget
//
// register widget
add_action( 'widgets_init', create_function( '', 'register_widget( "xw_progressbar_Widget" );' ) );


/**
 * Add filters and actions
 */

register_activation_hook(__FILE__, 'xw_progressbar_install');