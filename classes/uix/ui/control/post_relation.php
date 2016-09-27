<?php
/**
 * UIX Controls - Post Relation
 *
 * @package   controls
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\ui\control;

/**
 * Standard text input field
 *
 * @since 1.0.0
 */
class post_relation extends \uix\ui\control{
    
    /**
     * The type of object
     *
     * @since       1.0.0
     * @access public
     * @var         string
     */
    public $type = 'post_relation';

    /**
     * Catch the ajax search and push results
     *
     * @since 1.0.0
     * @access public
     */
    public function init(){

        $defaults = array(
            'add_label' => __( 'Add Related Post', 'uix' ),
            'config' => array(
                'limit' => 1,
            ),
            'query' => array(
                'post_type' => 'any',
                'post_per_page' => 5,
            ),
        );

        $this->struct = array_merge( $defaults, $this->struct );

        $data = uix()->request_vars( 'post' );

        if( !empty( $data['uixId'] ) && $data['uixId'] === $this->id() )
            $this->do_lookup( $data );

    }
    public function do_lookup( $data ){

        $defaults = array(
            'post_type' => 'post',
            'posts_per_page' => 10,
            'paged' => 1,
        );

        if( !empty( $data['_value'] ) )
            $defaults['s'] = $data['_value'];

        $args = array_merge( $defaults, $this->struct['query'] );

        if( !empty( $data['page'] ) )
            $args['paged'] = (int) $data['page'];

        if( !empty( $data['selected'] ) )
            $args['post__not_in'] = explode(',', $data['selected'] );

        $the_query = new \WP_Query( $args );

        $return = array(
            'html' => '',
            'found_posts' => $the_query->found_posts,
            'max_num_pages' => $the_query->max_num_pages,
        );
        if ( $the_query->have_posts() ) {
            while ( $the_query->have_posts() ) {
                $the_query->the_post();

                $return['html'] .= '<div class="uix-post-relation-item">';
                $return['html'] .= '<span class="uix-post-relation-add dashicons dashicons-plus" data-id="' . esc_html( $this->id() ) . '"></span>';
                $return['html'] .= '<span class="uix-relation-name">' . get_the_title(). '</span>';
                $return['html'] .= '<input class="uix-post-relation-id" type="hidden" name="' . esc_html( $this->name() ) . '[]" value="' . esc_attr( get_the_ID() ) . '" disabled="disabled">';
                $return['html'] .= '</div>';

            }


            $return['html'] .= '<div class="uix-post-relation-pager">';
            if( $the_query->max_num_pages > 1 ){
                $return['html'] .= '<button type="button" class="uix-post-relation-page button button-small" data-page="' . esc_attr($args['paged'] - 1) . '">';
                $return['html'] .= '<span class="dashicons dashicons-arrow-left-alt2"></span>';
                $return['html'] .= '</button>';

                $return['html'] .= '<span class="uix-post-relation-count">' . $args['paged'] . ' ' . esc_html__('of', 'uix') . ' ' . $the_query->max_num_pages . '</span>';

                $return['html'] .= '<button type="button" class="uix-post-relation-page button button-small" data-page="' . esc_attr($args['paged'] + 1) . '">';
                $return['html'] .= '<span class="dashicons dashicons-arrow-right-alt2"></span>';
                $return['html'] .= '</button>';
            }
            $return['html'] .= '</div>';

        } else {
            $return['html'] .= '<div class="uix-post-relation-no-results">' . esc_html__('Nothing found', 'uix') . '</div>';
        }

        wp_send_json( $return );
    }

    /**
     * Sets styling colors
     *
     * @since 1.0.0
     * @access protected
     */
    protected function enqueue_active_assets(){

        echo '<style type="text/css">';
        echo '.' . $this->id() . ' .uix-post-relation-item .uix-post-relation-add:hover{color: ' . $this->base_color() . ';}';
        echo '.' . $this->id() . ' .uix-post-relation-item .uix-post-relation-remover:hover {color: ' . $this->base_color() . ';}';

        echo '</style>';

    }
    
    /**
     * Gets the classes for the control input
     *
     * @since  1.0.0
     * @access public
     * @return array
     */
    public function classes() {

        return array(
            'uix-post-relation'
        );

    }

    /**
     * Returns the main input field for rendering
     *
     * @since 1.0.0
     * @see \uix\ui\uix
     * @access public
     * @return string
     */
    public function input(){


        $data = (array) $this->get_data();
        $input  = '<div ' . $this->build_attributes() . '>';

        foreach( $data as $item ){

            $input .= $this->render_item( $item );

        }

        $input .= '</div>';

        $input .= '<div class="uix-post-relation-footer"><button class="button button-small uix-add-relation" type="button">' . esc_html( $this->struct['add_label'] ) . '</button></div>';
        $input .= '<div class="uix-post-relation-panel">';
        $input .= '<span class="uix-post-relation-spinner spinner"></span>';
        $input .= '<input type="search" class="uix-ajax" data-load-element="_parent" data-delay="250" data-method="POST" data-uix-id="' . esc_attr( $this->id() ) . '" data-event="input paginate" data-before="uix_related_post_before" data-callback="uix_related_post_handler" data-target="#' . esc_attr( $this->id() ) . '-search-results">';

        $input .= '<div class="uix-post-relation-results" id="' . esc_attr( $this->id() ) . '-search-results">';



        $input .= '</div>';

        $input .= '</div>';

        return $input;
    }

    public function render_item( $item ){
        $input = null;

        if( get_post( $item ) ){

            $input .= '<div class="uix-post-relation-item">';
            $input .= '<span class="uix-post-relation-remover dashicons dashicons-no-alt"></span>';
            $input .= '<span class="uix-relation-name">' . get_the_title($item) . '</span>';
            $input .= '<input class="uix-post-relation-id" type="hidden" name="' . esc_html($this->name()) . '[]" value="' . esc_attr($item) . '">';
            $input .= '</div>';
        }

        return $input;
    }

    /**
     * register scritps and styles
     *
     * @since 1.0.0
     * @access public
     */
    public function set_assets() {

        // Initilize core styles
        $this->assets['style']['post-relation']        = $this->url . 'assets/controls/post-relation/css/post-relation' . UIX_ASSET_DEBUG . '.css';

        $this->assets['script']['post-relation']  = array(
            "src"       => $this->url . 'assets/controls/post-relation/js/post-relation' . UIX_ASSET_DEBUG . '.js',
            "in_footer" => true
        );

        parent::set_assets();
    }

}