<?php
/*
 * IMPLEMENT WIDGET
 * */

// Creating the widget
class sv_voting_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            // Base ID of your widget
            'sv_voting_widget',

            // Widget name will appear in UI
            __('Social Voting Widget', 'sv_voting_widget_domain'),

            // Widget description
            array( 'description' => __( 'Social Voting Widget', 'sv_voting_widget_domain' ), )
        );
    }

    // Creating widget front-end
    // This is where the action happens
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];
        // This is where you run the code and display the output
        sv_voting();
        echo $args['after_widget'];
    }

} // Class wpb_widget ends here

// Register and load the widget
function sv_voting_load_widget() {
    register_widget( 'sv_voting_widget' );
}
add_action( 'widgets_init', 'sv_voting_load_widget' );
?>