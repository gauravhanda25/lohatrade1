<?php
// Replace {$redux_opt_name} with your opt_name.
// Also be sure to change this function name!
$adifier_extension_path = '';
if(!function_exists('adifier_redux_register_custom_extension_loader')) :
    function adifier_redux_register_custom_extension_loader($ReduxFramework) {
            global $adifier_extension_path;
            $adifier_extension_path = plugin_dir_path( __FILE__ ) . 'extensions/';
            $folders = scandir( $adifier_extension_path, 1 );
            foreach ( $folders as $folder ) {
                if ( $folder === '.' or $folder === '..' or ! is_dir( $adifier_extension_path . $folder ) ) {
                    continue;
                }
                $extension_class = 'ReduxFramework_Extension_' . $folder;
                if ( ! class_exists( $extension_class ) ) {
                    // In case you wanted override your override, hah.
                    $class_file = $adifier_extension_path . $folder . '/extension_' . $folder . '.php';
                    $class_file = apply_filters( 'redux/extension/' . $ReduxFramework->args['opt_name'] . '/' . $folder, $class_file );
                    if ( $class_file ) {
                        require_once( $class_file );
                    }
                }
                if ( ! isset( $ReduxFramework->extensions[ $folder ] ) ) {
                    $ReduxFramework->extensions[ $folder ] = new $extension_class( $ReduxFramework );
                }
            }
    }

    // Modify {$redux_opt_name} to match your opt_name
    add_action("redux/extensions/adifier_options/before", 'adifier_redux_register_custom_extension_loader', 0);
endif;
?>