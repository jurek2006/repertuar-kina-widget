<?php
/*
Plugin Name: Repertuar Kina Widget JS
Description: Widżet do strony Centrum Sztuki wyświetlający z odnośnik do repertuaru kina
Version: 0.3
Author: Jurek Skowron
*/

class repertuarKinaWidgetJS extends WP_Widget {

        // constructor
        function __construct() {

			parent::__construct(
				'repertuar-kina-widget', // Base ID
				__( 'Repertuar kina', 'repertuar-kina-widget' ), // Name
				array( 'description' => __( 'Widżet repertuaru kina' ), ) // Args
			);
		}

        // widget form creation
        function form($instance) {      
        // Check values
			if( $instance) {
				 $title = esc_attr($instance['title']);
			} else {
				 $title = '';
			}
			?>
			
			<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'wp_widget_plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>

			<?php
        }

        // widget update
        function update($new_instance, $old_instance) {
                // update widget

			  $instance = $old_instance;
			  // Fields
			  $instance['title'] = strip_tags($new_instance['title']);
			 return $instance;

        }

        // widget display
        function widget($args, $instance) {
                extract( $args );
				
				// these are the widget options
			   $title = apply_filters('widget_title', $instance['title']);
			   
				//-------------------------------------------------------------------------------------
				echo $before_widget;
				?>

			   	<div class="repertuar-kina-widget clearfix">
                <a href="<?php echo home_url()?>/kino/"><img src="<?php echo plugin_dir_url( __FILE__ );?>img/kino-repertuar.png" alt="Repertuar Kina Odra w Oławie" /></a>
                <!--<img src="<?php //echo get_stylesheet_directory_uri()?>/_repertuar.jpg" />-->


                
                <?php
					//wyświetlenie obrazków aktualnych filmów
					//bierze obrazki z czterech najbliższych zaplanowanych projekcji - oczywiście unikatowo
					$dzien_poczatku = date("Y-m-d");
					
					$params = array( 	'limit' => -1,
										'where' => 'DATE( termin_projekcji.meta_value ) >= "'.$dzien_poczatku.'"',
										'orderby'  => 'termin_projekcji.meta_value');

					
					//get pods object
	
					$pods = pods( 'projekcje', $params );
					//pętla po wszystkich znalezionych (przyszłych) projekcjach - jeśli takie są
					//ID istniejących obrazków jest zapisywane do tabeli $picturesIDs
					//filmy dla których nie ma ustawionego obrazka są pomijane
					if ( $pods->total() > 0 ) {
							while ( $pods->fetch() ) {
								$tytul_filmu =  $pods->display('film');
								$picture = $pods->field('film.obraz');
								if (( !is_null($picture) )&&(!empty($picture))){
									$picturesIDs[]=$picture['ID'];
								}//if (( !is_null($picture) )&&(!empty($picture)))
							
							}//while ( $pods->fetch() 
						

						
						//pozbycie się duplikatów z tabeli $picturesIDs żeby obrazek danego filmu się nie powtarzał
					  	$picturesIDs = array_unique($picturesIDs);
						
						$licznik = 0; //licznik obrazków do wyświetlenia - zastosowany zamiast $i, ponieważ niektóre obrazki mogą okazać się false
						foreach ($picturesIDs as $key => $img){
							//przegląda wszystkie wpisy w tabeli $picturesIDs (jest ona niekolejna, dlatego foreach
							//jeśli znalezione zostaną 3 obrazki następuje break, jeśli mniej, to kolejna część doda "wypełniacze"
							echo wp_get_attachment_image( $img, 'film-zapowiedz', false, array('class'	=> "repertuar-okladka ") );
							$licznik++;
							if($licznik == 3)
								break;
						}

					}//if ( $pods->total() > 0 )
					else{
					// jeśli nie ma żadnej przyszłej projekcji licznik ustawiany jest na sztywno na 0 - awaryjnie traktowane jest to jak gdyby był tylko jeden film ($licznik==1)
					// wyświetla się zajmujący "dwa miejsca okładki"
						$licznik = 0;
					}


					
					if($licznik<=1){
					//jeśli jest tylko jeden obrazek aktywnych filmów zaplanowanych projekcji to banner dopełniany jest obrazkiem 
					//loga na dwa pola
					?>
                   		<img src="<?php echo plugin_dir_url( __FILE__ );?>img/pegaz_166x123.png" class="repertuar-wypelniacz" />
<?php
					}//if($i==1)
					else if($licznik==2){
					//jeśli są dwa obrazki aktywnych filmów zaplanowanych projekcji to banner dopełniany jest obrazkiem 
					//loga na jedno pole
						?>
                   		<img src="<?php echo plugin_dir_url( __FILE__ );?>img/pegaz_83x123.png" class="repertuar-okladka" />
<?php
					}//else if($i==2)
					
					
				?>
                
				</div><!--.repertuar-kina-widget-->
				<?php
				
				echo $after_widget;
				
				

				//--------------------------------------------------------------------------------------------------------
				

			
			   
        }
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("repertuarKinaWidgetJS");'));
?>