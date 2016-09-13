<?php
/**
 * Plugin metabox Structures
 *
 * @package   uixdemo
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
$posttypes = array(
    'employee' => array(
        'base_color' => '#ff00aa',
        'settings' => array(
            'label'                 => __( 'Employee', 'uix-demo' ),
            'description'           => __( 'Employees Post Type', 'uix-demo' ),
            'labels'                => array(
                'name'                  => _x( 'Employees', 'Post Type General Name', 'uix-demo' ),
                'singular_name'         => _x( 'Employee', 'Post Type Singular Name', 'uix-demo' ),
                'menu_name'             => __( 'Employees', 'uix-demo' ),
                'name_admin_bar'        => __( 'Employee', 'uix-demo' ),
                'archives'              => __( 'Employee Archives', 'uix-demo' ),
                'parent_item_colon'     => __( 'Parent Employee:', 'uix-demo' ),
                'all_items'             => __( 'All Employees', 'uix-demo' ),
                'add_new_item'          => __( 'Add New Employee', 'uix-demo' ),
                'add_new'               => __( 'Add New', 'uix-demo' ),
                'new_item'              => __( 'New Employee', 'uix-demo' ),
                'edit_item'             => __( 'Edit Employee', 'uix-demo' ),
                'update_item'           => __( 'Update Employee', 'uix-demo' ),
                'view_item'             => __( 'View Employee', 'uix-demo' ),
                'search_items'          => __( 'Search Employee', 'uix-demo' ),
                'not_found'             => __( 'Not found', 'uix-demo' ),
                'not_found_in_trash'    => __( 'Not found in Trash', 'uix-demo' ),
                'featured_image'        => __( 'Employee Picture', 'uix-demo' ),
                'set_featured_image'    => __( 'Set employee picture', 'uix-demo' ),
                'remove_featured_image' => __( 'Remove employee picture', 'uix-demo' ),
                'use_featured_image'    => __( 'Use as employee picture', 'uix-demo' ),
                'insert_into_item'      => __( 'Insert into employee', 'uix-demo' ),
                'uploaded_to_this_item' => __( 'Uploaded to this employee', 'uix-demo' ),
                'items_list'            => __( 'Employees list', 'uix-demo' ),
                'items_list_navigation' => __( 'Employees list navigation', 'uix-demo' ),
                'filter_items_list'     => __( 'Filter employees list', 'uix-demo' ),
            ),
            'supports'              => array( 'title', 'thumbnail', ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-businessman',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,        
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
        ),   
    ),
);



return $posttypes;