<?php
/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     ReduxFramework
 * @author      Dovy Paukstys
 * @version     3.1.5
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if( !class_exists( 'ReduxFramework_grouped_adifier' ) ) {

    /**
     * Main ReduxFramework_grouped_adifier class
     *
     * @since       1.0.0
     */
    class ReduxFramework_grouped_adifier extends ReduxFramework {
    
        /**
         * Field Constructor.
         *
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        function __construct( $field = array(), $value ='', $parent ) {
        
            
            $this->parent = $parent;
            $this->field = $field;
            $this->value = $value;

            if ( empty( $this->extension_dir ) ) {
                $this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
                //$this->extension_url = site_url( str_replace( trailingslashit( str_replace( '\\', '/', ABSPATH ) ), '', $this->extension_dir ) );
                if ( preg_match("/wp-content\/(.*)/", $this->extension_dir, $match) ) {
                    $this->extension_url = site_url('/wp-content/'.$match[1]);
                }
            }  

            // Set default args for this field to avoid bad indexes. Change this to anything you use.
            $defaults = array(
                'repeatable'          => false,
            );
            $this->field = wp_parse_args( $this->field, $defaults );
        
        }

        /**
         * Field Render Function.
         *
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        private function generate_groups( $group = '' ){
            $values = !empty( $group ) ? explode( '|', $group ) : array();
            ?>
            <div class="grouped-adifier-group">
                <?php
                for( $i=0; $i<sizeof($this->field['subfields']); $i++ ){
                    ?>
                        <label><?php echo esc_html( $this->field['subfields'][$i] ); ?></label>
                        <input type="text" value="<?php echo !empty( $values[$i] ) ? esc_attr( $values[$i] ) : '' ?>" class="regular-text af-gap">
                    <?php
                }
                ?>
                <a href="javascript:void(0);" class="grouped-adifier-remove button"><?php esc_html_e( 'Remove', 'adifier' ) ?></a>
            </div>            
            <?php
        }

        public function render() {
            $groups = !empty( $this->value ) ? explode( '||', $this->value ) : array();
            if( !empty( $groups ) ){
                foreach( $groups as $group ){
                    $this->generate_groups( $group );
                }
            }
            else{
                $this->generate_groups();
            }
            if( $this->field['repeatable'] ):
                ?>
                <a href="javascript:void(0);" class="grouped-adifier-add button button-primary"><?php esc_html_e( 'Add New', 'adifier' ) ?></a>
                <?php
            endif;
            ?>
            <input type="text" value="<?php echo esc_attr( $this->value ); ?>" name="<?php echo esc_attr( $this->field['name'].$this->field['name_suffix'] ); ?>" class="hidden grouped-adifier-value">
            <?php
        }      
    
        /**
         * Enqueue Function.
         *
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function enqueue() {

            wp_enqueue_script(
                'redux-field-grouped-ajax', 
                $this->extension_url . 'field_grouped_adifier.js', 
                array( 'jquery' ),
                time(),
                true
            );

            wp_enqueue_style( 'redux-field-grouped-ajax', $this->extension_url . 'field_grouped_adifier.css' );
        
        }      
        
    }
}
