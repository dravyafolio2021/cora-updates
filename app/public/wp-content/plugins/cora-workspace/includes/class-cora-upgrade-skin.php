<?php
/**
 * Custom WordPress Upgrader Skin to intercept feedback and report progress
 */

if ( ! class_exists( 'Cora_Upgrade_Skin' ) && class_exists( 'Automatic_Upgrader_Skin' ) ) {
    class Cora_Upgrade_Skin extends Automatic_Upgrader_Skin {
        public function feedback( $feedback, ...$args ) {
            parent::feedback( $feedback, ...$args );
            
            $status = '';
            $percent = 0;
            
            // Map common WordPress translation/string feedback keys during upgrade
            if ( 'downloading_package' === $feedback || false !== strpos( $feedback, 'download' ) ) {
                $status = 'Downloading package (cora-workspace.zip)...';
                $percent = 35;
            } elseif ( 'unpack_package' === $feedback || false !== strpos( $feedback, 'unpack' ) ) {
                $status = 'Verifying and extracting package...';
                $percent = 60;
            } elseif ( 'remove_old' === $feedback || false !== strpos( $feedback, 'remove' ) ) {
                $status = 'Backing up current workspace configuration...';
                $percent = 80;
            } elseif ( 'install_package' === $feedback || false !== strpos( $feedback, 'install' ) ) {
                $status = 'Installing latest files...';
                $percent = 90;
            }
            
            if ( $percent > 0 ) {
                update_option( 'cora_workspace_upgrade_progress', array(
                    'step' => 3,
                    'percent' => $percent,
                    'status' => $status
                ) );
            }
        }
    }
}
