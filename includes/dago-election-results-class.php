<?php

defined ( 'ABSPATH' ) or die();  //Makes sure that the plugin is inizialized by WP.

/**
 * Adds DAGO Election Results widget.
 */	
class DAGO_Election_Results_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'DAGO_election_results_widget', // Base ID
			esc_html__( 'DAGO Election Results Widget', 'dago_election_results_domain' ), // Name
			array( 'description' => esc_html__( 'Displays Election Results', 'dago_election_results_domain' ), ) // Args
		);
	}

	static function dago_election_results_install() {
		
		global $wpdb;
		global $dago_db_version;
		$dago_db_version = '1.0';

		$table_name = $wpdb->prefix . 'dago_election_races_xx';
		
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			name tinytext NOT NULL,
			text text NOT NULL,
			url varchar(55) DEFAULT '' NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		//echo $wpdb->last_error;

		add_option( 'dago_db_version', $dago_db_version );

	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		//echo 'The Widget','<br />';
		if ( ! empty( $instance['active'] ) ) {

		$linkURL = ! empty( $instance['linkURL'] ) ? $instance['linkURL'] : esc_html__( '/election/', 'dago_election_results_domain' );
		$linkLabel = ! empty( $instance['linkLabel'] ) ? $instance['linkLabel'] : esc_html__( 'Full Results', '
			dago_election_results_domain' );
		$noCandidates = ! empty( $instance['noCandidates'] ) ? $instance['noCandidates'] : esc_html__( '99', 'dago_election_results_domain' );	

			echo $args['before_widget'];

			if ( ! empty( $instance['banner'] ) ) {
				echo '<img class="img-fluid" src="', plugins_url(), '/dago-election-results/assets/img/client/', apply_filters( 'widget_banner', $instance['banner'] ), '" />';
			}

			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}
				
			if ( ! empty( $instance['races'] ) ) {

				echo $args['before_race'];
				
				$races = explode(',', $instance['races']);
				foreach ($races as $race) {
					//echo $race, '<br />';
					$this->dago_election_results_create_race_html( $race, $noCandidates, $linkURL, $linkLabel );
				}

				echo $args['after_race'];
			}

			//echo esc_html__( 'Hello, World!', 'DAGO_election_results_domain' );
			
			echo $args['after_widget'];
	
		}

	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'dago_election_results_domain' );

		$banner = ! empty( $instance['banner'] ) ? $instance['banner'] : esc_html__( '', 'dago_election_results_domain' );

		$races = ! empty( $instance['races'] ) ? $instance['races'] : esc_html__( '', 'dago_election_results_domain' );

		$noCandidates = ! empty( $instance['noCandidates'] ) ? $instance['noCandidates'] : esc_html__( '99', 'dago_election_results_domain' );	

		$linkURL = ! empty( $instance['linkURL'] ) ? $instance['linkURL'] : esc_html__( '/election/', 'dago_election_results_domain' );

		$linkLabel = ! empty( $instance['linkLabel'] ) ? $instance['linkLabel'] : esc_html__( 'Full Results', 'dago_election_results_domain' );

		$active = ! empty( $instance['active'] ) ? $instance['active'] : esc_html__( '', 'dago_election_results_domain' );
		
		?>
		<p>
		<label 
			for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'dago_election_results_domain' ); ?>			
		</label> 
		<input 
			class="widefat" 
			id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
			type="text" 
			value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
		<label 
			for="<?php echo esc_attr( $this->get_field_id( 'banner' ) ); ?>"><?php esc_attr_e( 'Banner:', 'dago_election_results_domain' ); ?>			
		</label> 
		<input 
			class="widefat" 
			id="<?php echo esc_attr( $this->get_field_id( 'banner' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'banner' ) ); ?>" 
			type="text" 
			value="<?php echo esc_attr( $banner ); ?>">
		</p>

		<p>
		<label 
			for="<?php echo esc_attr( $this->get_field_id( 'races' ) ); ?>"><?php esc_attr_e( 'Races:', 'dago_election_results_domain' ); ?>			
		</label> 
		<input 
			class="widefat" 
			id="<?php echo esc_attr( $this->get_field_id( 'races' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'races' ) ); ?>" 
			type="text" 
			value="<?php echo esc_attr( $races ); ?>">
		</p>	

		<p>
		<label 
			for="<?php echo esc_attr( $this->get_field_id( 'noCandidates' ) ); ?>"><?php esc_attr_e( 'No. Candidates:', 'dago_election_results_domain' ); ?>	<small>Candiates to display</small>		
		</label> 
		<input 
			class="widefat" 
			id="<?php echo esc_attr( $this->get_field_id( 'noCandidates' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'noCandidates' ) ); ?>" 
			type="text" 
			value="<?php echo esc_attr( $noCandidates ); ?>">
		</p>		
		
		<p>
		<label 
			for="<?php echo esc_attr( $this->get_field_id( 'linkURL' ) ); ?>"><?php esc_attr_e( 'Link URL:', 'dago_election_results_domain' ); ?> <small>If blank, default URL.</small>			
		</label> 
		<input 
			class="widefat" 
			id="<?php echo esc_attr( $this->get_field_id( 'linkURL' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'linkURL' ) ); ?>" 
			type="text" 
			value="<?php echo esc_attr( $linkURL ); ?>">
		</p>	

		<p>
		<label 
			for="<?php echo esc_attr( $this->get_field_id( 'linkLabel' ) ); ?>"><?php esc_attr_e( 'Link Label:', 'dago_election_results_domain' ); ?> <small>If blank, Full Result.</small>			
		</label> 
		<input 
			class="widefat" 
			id="<?php echo esc_attr( $this->get_field_id( 'linkLabel' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'linkLabel' ) ); ?>" 
			type="text" 
			value="<?php echo esc_attr( $linkLabel ); ?>">
		</p>	

		<p>
		<label 
			for="<?php echo esc_attr( $this->get_field_id( 'acive' ) ); ?>"><?php esc_attr_e( 'Active:', 'dago_election_results_domain' ); ?>			
		</label> 
		<input 
			class="widefat" 
			id="<?php echo esc_attr( $this->get_field_id( 'active' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'active' ) ); ?>" 
			type="checkbox" 
			value="1"
			<?php echo ($active) ? 'checked' : ''; ?> >
		</p>		
			
		<?php 
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

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';

		$instance['banner'] = ( ! empty( $new_instance['banner'] ) ) ? sanitize_text_field( $new_instance['banner'] ) : '';	
		
		$instance['races'] = ( ! empty( $new_instance['races'] ) ) ? sanitize_text_field( $new_instance['races'] ) : '';

		$instance['noCandidates'] = ( ! empty( $new_instance['noCandidates'] ) ) ? sanitize_text_field( $new_instance['noCandidates'] ) : '99';

		$instance['linkURL'] = ( ! empty( $new_instance['linkURL'] ) ) ? sanitize_text_field( $new_instance['linkURL'] ) : '/elections';

		$instance['linkLabel'] = ( ! empty( $new_instance['linkLabel'] ) ) ? sanitize_text_field( $new_instance['linkLabel'] ) : 'Full Results';

		$instance['active'] = sanitize_text_field( $new_instance['active'] );

		return $instance;
	}

	private function dago_election_results_create_race_html( $param_race, $param_cands, $param_link_URL, $param_link_label ) {

		global $wpdb;
		$raceUniqueID  = filter_var( sanitize_text_field( $param_race ), FILTER_SANITIZE_STRING ) ;
		$candidates = filter_var( sanitize_text_field( $param_cands ), FILTER_SANITIZE_NUMBER_INT ) ; 
		$linkURL = filter_var( sanitize_text_field( $param_link_URL ), FILTER_SANITIZE_STRING ) ;
		$linkLabel = filter_var( sanitize_text_field( $param_link_label ), FILTER_SANITIZE_STRING ) ;

		echo $instance['linkLabel'];

		$query = "SELECT raceUniqueID, title1, title2, lastUpdated, precintsPercentage FROM dago_election_races WHERE raceUniqueID = %s";
		$race = $wpdb->get_row( $wpdb->prepare( $query, $raceUniqueID) ); // or die( $wpdb->last_error );

		$query = "SELECT * FROM dago_election_candidates WHERE raceUniqueID = %s ORDER BY numberVotes Desc LIMIT %d";
		$params = array( $raceUniqueID, $candidates );
		$candidates = $wpdb->get_results( $wpdb->prepare( $query, $params ) ); //or die( $wpdb->last_error );

		?>

		<div class="row" style="border: 1px solid black;margin: 5px 5px 5px 5px;">
			<div class="col-12" style=""><h6><?php echo esc_html($race->title1); ?></h6></div>
			<div class="col-12"><small><?php echo 'Reporting: ', esc_html($race->precintsPercentage), '%'; ?></small></div>
			<?php foreach ($candidates as $cand) { ?>
				<div class="col-1"><small><?php echo ($cand->winner) ? 'X' : ' '; ?></small></div>
				<div class="col-9">
					<small><?php echo esc_html($cand->lastName), '(', esc_html($cand->affiliation), ')'; ?></small>
				</div>
				<div class="col-2">
					<small><?php echo esc_html($cand->percentageVotes), '%'; ?></small>
				</div>
			<?php } ?>
			<?php $qparams = array( 'race' => $race->raceUniqueID ); ?>
			<div class="col-11">
				<a href="<?php echo add_query_arg($qparams, $linkURL); ?>"><small><?php echo $linkLabel; ?></small></a>
			</div>
		</div>

		<?php
		
	}

} // class DAGO_Election_Results_Widget

register_activation_hook( __FILE__, array( 'DAGO_Election_Results_Widget', 'dago_election_results_install12' ) );