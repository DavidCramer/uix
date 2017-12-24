# UIX

[![Travis](https://api.travis-ci.org/DavidCramer/uix.svg?branch=develop)](https://travis-ci.org/DavidCramer/uix/)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/DavidCramer/uix.svg)](https://scrutinizer-ci.com/g/DavidCramer/uix/?branch=master)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/DavidCramer/uix.svg)](https://scrutinizer-ci.com/g/DavidCramer/uix/?branch=master)


UIX is a small framework for creating user interfaces ( Post Types, Settings Pages, and Metaboxes ) and config structures with the least code possible. It only handles the UI. The program logic is up to you.


## Documentation

Important note is that UIX used namespacing so it is PHP 5.3+. It's also heavy in development, so treat this as an `ALPHA`.

### Installation

Currently using it as a WordPress plugin is the only reliable way to use it.
- A Composer method will be included soon. 
- Grunt process will be updated and be available again.


## Registration

UIX has a `uix()` helper function you can use to add UI objects as needed, or it can auto load UI structures from a defined UI folder.

### Helper Function

The helper function makes it easy to add UI structures quickly.
```php
$employees = uix()->add( 'post_type', 'employees', array(
    'settings' => array(
        'label'                 => __( 'Employee', 'text-domain' ),
        'description'           => __( 'Employees Post Type', 'text-domain' ),
        'labels'                => array(
            'name'                  => _x( 'Employees', 'Post Type General Name', 'text-domain' ),
            'singular_name'         => _x( 'Employee', 'Post Type Singular Name', 'text-domain' ),
            'menu_name'             => __( 'Employees', 'text-domain' ),
            'name_admin_bar'        => __( 'Employee', 'text-domain' ),
        ),
        'supports'              => array( 'title' ),
        'public'                => true,
        'menu_name'             => 'Employees',
        'menu_icon'             => 'dashicons-menu',
    ),
));
```
Now `$employees` is the UI object created. From here you just leave it and your post type is registered. However, you can also add metaboxes to the object like this:
```php
$metabox = $employees->metabox( 'meta_fields', array(
    'name'              =>  esc_html__( 'Metabox Fields', 'text-domain' ),
    'context'           =>  'normal',
    'priority'          =>  'high',
));
```
This adds a `Metabox Fields` meta box to the post type. You'll need to have some sections and controls for the metabox to be useful, so you can add them to the metabox object:
```php
$metabox->section( 'employee_details', array(
    'label' => esc_html__( 'Employee Details', 'text-domain' ),
))->control( 'employee_name', array(
    'label' => esc_html__( 'Name', 'text-domain' ),    
))->parent->control( 'employee_bio', array(
    'label' => esc_html__( 'Bio', 'text-domain' ),
    'type' => 'textarea'
));
```

### Autoloading

You can register a folder for UIX to scan and auto load any structures it finds. This means that you don't need ever write registraion code to make stuff happen.

There is a `uix_register` hook that will allow you to register the folder location where the definition files are kept.
You can use it like this:

```php
function register_ui_folders( $uix ){
    $uix->register( plugin_dir_path( __FILE__ ) . 'includes/ui' );
}
add_action( 'uix_register', 'register_ui_folders' );
```

The path registered should have folders of each type of UI object and contain fields defining the UI structure:

```
ui/
├── metabox/
│   ├── user_fields.php
│   └── post_meta.php
├── post_type/
│   ├── portfolio.php
│   └── employees.php
└── page/
    └── my_settings.php
```

The file needs to return an array structure of the objects to auto load. 
The `ui/employees.php` mentioned above, could look like this:
```php
$post_type = array(
    'post_type_slug' => array(
        'settings' => array(
            'label'                 => __( 'Employee', 'text-domain' ),
            'description'           => __( 'Employees Post Type', 'text-domain' ),
            'labels'                => array(
                'name'                  => _x( 'Employees', 'Post Type General Name', 'text-domain' ),
                'singular_name'         => _x( 'Employee', 'Post Type Singular Name', 'text-domain' ),
                'menu_name'             => __( 'Employees', 'text-domain' ),
                'name_admin_bar'        => __( 'Employee', 'text-domain' ),
            ),
            'supports'              => array( 'title' ),
            'public'                => true,
            'menu_name'             => 'Employees',
            'menu_icon'             => 'dashicons-menu',
        ),
        'metabox'                   => array(
            'meta_fields'           =>  array(
                'name'              =>  esc_html__( 'Metabox Fields', 'text-domain' ),
                'context'           =>  'normal',
                'priority'          =>  'high',
                'section'               =>  array(
                    'employee_details'  =>  array(
                        'label'         =>  esc_html__( 'Employee Details', 'text-domain' ),
                        'control'       =>  array(
                            'employee_name' =>  array(
                                'label'     => esc_html__( 'Name', 'text-domain' ),
                            ),
                            'employee_bio'    =>  array(
                                'label' => esc_html__( 'Bio', 'text-domain' ),
                                'type' => 'textarea'
                            ),
                        ),
                    ),
                ),
            ),  
        ),
    ),
);
return $post_type;
```
This will create and register the post type automatically on load, including the metabox and controls attached.
