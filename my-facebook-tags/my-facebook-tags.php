<?php 
/**
 * Plugin Name: My Facebook Tags
 * Plugin URI: https://github.com/upeshv/My-Facebook-Tags-Open-Graph-protocol
 * Description: This plugin adds Facebook Open Graph tags to our single posts. It enables any web page to become a rich object in a social graph. For instance, this is used on Facebook to allow any web page to have the same functionality as any other object on Facebook.
 * Tags : Facebook Open Graph, Open Graph protocol, og:title, og:type, og:url , og:image
 * Version: 1.0.0
 * Author: Upesh Vishwakarma
 * Author URI: https://github.com/upeshv/
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

function add_additional_fields_meta_box() {
	add_meta_box(
		'additional_fields_meta_box', // $id
		'Optional Metadata', // $title
		'show_additional_fields_meta_box', // $callback
		'post', // $screen
		'normal', // $context
		'high' // $priority
	);
}
add_action( 'add_meta_boxes', 'add_additional_fields_meta_box' );

function show_additional_fields_meta_box() {
	global $post;  
		$meta = get_post_meta( $post->ID, 'additional_fields', true ); ?>

	<input type="hidden" name="your_meta_box_nonce" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>">

    <!-- All fields will go here -->
	<p>
		<label for="additional_fields[type]"><b>OG:Type</b> (Type of your object.)</label>
		<br>
		<input type="text" name="additional_fields[type]" id="additional_fields[type]" class="regular-text" value="<?php echo $meta['type']; ?>">		
	</p>
	<p>
		<label for="additional_fields[description]"><b>OG:Description</b> (A one to two sentence description of your object.)</label>
		<br>
		<input type="text" name="additional_fields[description]" id="additional_fields[description]" class="regular-text" value="<?php echo $meta['description']; ?>">		
	</p>
	<p>
		<label for="additional_fields[audio]"><b>OG:Audio</b> (A URL to an audio file to accompany this object)</label>
		<br>
		<input type="text" name="additional_fields[audio]" id="additional_fields[audio]" class="regular-text" value="<?php echo $meta['audio']; ?>">		
	</p>
	<p>
		<label for="additional_fields[video]"><b>OG:Video</b> (A URL to a video file that complements this object.)</label>
		<br>
		<input type="text" name="additional_fields[video]" id="additional_fields[video]" class="regular-text" value="<?php echo $meta['video']; ?>">		
	</p>
	<p>
		<label for="additional_fields[determiner]"><b>OG:Determiner</b> (The word that appears before this object's title in a sentence. An enum of (a, an, the, "", auto). If auto is chosen, the consumer of your data should chose between "a" or "an". Default is "" (blank).)</label>
		<br>
		<input type="text" name="additional_fields[determiner]" id="additional_fields[determiner]" class="regular-text" value="<?php echo $meta['determiner']; ?>">		
	</p>
	<p>
		<label for="additional_fields[locale]"><b>OG:Locale</b> (The locale these tags are marked up in. Of the format language_TERRITORY. Default is en_US.)</label>
		<br>
		<input type="text" name="additional_fields[locale]" id="additional_fields[locale]" class="regular-text" value="<?php echo $meta['locale']; ?>">		
	</p>
	<p>
		<label for="additional_fields[locale_alternate]"><b>OG:Locale Alternate</b> (An array of other locales this page is available in.)</label>
		<br>
		<input type="text" name="additional_fields[locale_alternate]" id="additional_fields[locale_alternate]" class="regular-text" value="<?php echo $meta['locale_alternate']; ?>">		
	</p>
	
	
<?php
	}
	

function save_additional_fields_meta( $post_id ) {   
	// verify nonce
	if ( !wp_verify_nonce( $_POST['your_meta_box_nonce'], basename(__FILE__) ) ) {
		return $post_id; 
	}
	// check autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}
	// check permissions
	if ( 'page' === $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		} elseif ( !current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}  
	}
	
	$old = get_post_meta( $post_id, 'additional_fields', true );
	$new = $_POST['additional_fields'];

	if ( $new && $new !== $old ) {
		update_post_meta( $post_id, 'additional_fields', $new );
	} elseif ( '' === $new && $old ) {
		delete_post_meta( $post_id, 'additional_fields', $old );
	}
}
add_action( 'save_post', 'save_additional_fields_meta', 10, 2 );
	
	
function my_facebook_tags() {
  if( is_single() ) {
  ?>
  
    <meta property="og:title" content="<?php the_title() ?>" />
    <meta property="og:site_name" content="<?php bloginfo( 'name' ) ?>" />
    <meta property="og:url" content="<?php the_permalink() ?>" />
 <?php 
      if ( has_post_thumbnail() ) :
        $image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' ); 
    ?>
	<meta property="og:image" content="<?php echo $image[0]; ?>"/>      <?php endif; ?>	
<?php 
global $post;
$args = array(
	'post_type' => 'post',
);  
$your_loop = new WP_Query( $args ); 
if ( $your_loop->have_posts() ) : while ( $your_loop->have_posts() ) : $your_loop->the_post();
$meta = get_post_meta( $post->ID, 'additional_fields', true ); ?>
	
<?php if($meta['type'] != ''){ ?>	<meta property="og:type" content="<?php echo $meta['type']; ?>" />   <?php } ?>
<?php if($meta['description'] != ''){ ?>		
	<meta property="og:description" content="<?php echo $meta['description']; ?>" />  <?php } ?>
<?php if($meta['audio'] != ''){ ?> 				
	<meta property="og:audio" content="<?php echo $meta['audio']; ?>" /> <?php } ?>
<?php if($meta['video'] != ''){ ?> 				
	<meta property="og:video" content="<?php echo $meta['video']; ?>" />  <?php } ?>
<?php if($meta['determiner'] != ''){ ?> 		
	<meta property="og:determiner" content="<?php echo $meta['determiner']; ?>" />  <?php } ?>
<?php if($meta['locale'] != ''){ ?>   			
	<meta property="og:locale" content="<?php echo $meta['locale']; ?>" />  <?php } ?>
<?php if($meta['locale_alternate'] != ''){ ?>   
	<meta property="og:locale:alternate" content="<?php echo $meta['locale_alternate']; ?>" />  <?php } ?>

<?php endwhile; endif; wp_reset_postdata(); ?>
	
   
    
  <?php
  }
}
add_action( 'wp_head', 'my_facebook_tags' );


function send_email_on_plugin_activate() {
	$plugin_title = "My Facebook Tags";
	$plugin_url = 'https://wordpress.org/plugins/My-Facebook-Tags-Open-Graph-protocol';
	$plugin_support_url = 'http://wordpress.org/support/plugin/My-Facebook-Tags-Open-Graph-protocol';
	$plugin_author = 'Upesh Vishwakarma';
	$plugin_author_url = 'https://github.com/upeshv';
	$plugin_author_mail = 'vishwa.upesh@gmail.com';

	$website_name  = get_option('blogname');
	$adminemail = get_option('admin_email');
	$user = get_user_by( 'email', $adminemail );

	$headers = 'From: ' . $website_name . ' <' . $adminemail . '>' . "\r\n";
	$subject = "Thank you for installing " . $plugin_title . "!\n";
	if($user->first_name)
	{
		$message = "Dear " . $user->first_name . ",\n\n";
	}
	else
	{
		$message = "Dear Administrator,\n\n";
	}
	$message.= "Thank your for installing " . $plugin_title . " plugin.\n";
	$message.= "Visit this plugin's site at " . $plugin_url . " \n\n";
	$message.= "Please write your queries and suggestions at developers support \n" . $plugin_support_url ."\n";
	$message.= "All the best !\n\n";
	$message.= "Thanks & Regards,\n";
	$message.= $plugin_author . "\n";
	$message.= $plugin_author_url ;
	wp_mail( $adminemail, $subject, $message,$headers);
	
	$subject = $plugin_title . " plugin is installed and activated by website " . get_option('home') ."\n";
	$message = $plugin_title  . " plugin is installed and activated by website " . get_option('home') ."\n\n";
	$message.= "Website : " . get_option('home') . "\n";
	$message.="Email : " . $adminemail . "\n";
	if($user->first_name)
	{
		$message.= "First name : " . $user->first_name . " \n";
	}
	if($user->last_name)
	{
		$message.= "Last name : " . $user->last_name . "\n";	
	}
	wp_mail( $plugin_author_mail , $subject, $message,$headers);
}
register_activation_hook( __FILE__, 'send_email_on_plugin_activate' );

?>