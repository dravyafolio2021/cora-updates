<?php
/**
 * Cora Growth Workspace - Structured Content Block Registry
 *
 * Defines and validates structured JSON blocks, preventing uncontrolled raw HTML storage.
 *
 * @package CoraWorkspace
 * @subpackage Growth
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_Block_Registry {

    private static $blocks = array();

    /**
     * Initialize standard blocks
     */
    public static function init() {
        self::register_default_blocks();
    }

    /**
     * Register a block type
     *
     * @param string $block_type Unique block identifier
     * @param array  $config     Block configuration
     */
    public static function register_block( $block_type, $config ) {
        $defaults = array(
            'label'       => ucwords( str_replace( '_', ' ', $block_type ) ),
            'version'     => 1,
            'description' => '',
            'validator'   => null, // callable or null
        );

        self::$blocks[ $block_type ] = wp_parse_args( $config, $defaults );
    }

    /**
     * Get all registered blocks
     *
     * @return array
     */
    public static function get_all_blocks() {
        if ( empty( self::$blocks ) ) {
            self::register_default_blocks();
        }
        return self::$blocks;
    }

    /**
     * Get block definition
     *
     * @param string $block_type
     * @return array|null
     */
    public static function get_block( $block_type ) {
        if ( empty( self::$blocks ) ) {
            self::register_default_blocks();
        }
        return isset( self::$blocks[ $block_type ] ) ? self::$blocks[ $block_type ] : null;
    }

    /**
     * Validate an array of content blocks
     *
     * @param array $blocks Array of block objects: [{ id, type, version, data }]
     * @return array Array of errors (empty if all valid)
     */
    public static function validate_blocks( $blocks ) {
        $errors = array();

        if ( ! is_array( $blocks ) ) {
            $errors[] = 'Content blocks must be an array.';
            return $errors;
        }

        foreach ( $blocks as $index => $block ) {
            if ( ! is_array( $block ) ) {
                $errors[] = "Block at index {$index} is not a valid object.";
                continue;
            }

            if ( empty( $block['id'] ) ) {
                $errors[] = "Block at index {$index} is missing an 'id'.";
            }

            if ( empty( $block['type'] ) ) {
                $errors[] = "Block at index {$index} is missing a 'type'.";
                continue;
            }

            $raw_type = $block['type'];
            if ( $raw_type === 'paragraph' ) {
                $raw_type = 'rich_text';
                $block['type'] = 'rich_text';
            }

            $type_def = self::get_block( $raw_type );
            if ( ! $type_def ) {
                $errors[] = "Block at index {$index} has unknown type '{$block['type']}'.";
                continue;
            }

            // If data object is omitted, allow flat block fields as data
            if ( ! isset( $block['data'] ) || ! is_array( $block['data'] ) ) {
                $block_data = $block;
                unset( $block_data['id'], $block_data['type'], $block_data['version'] );
                $block['data'] = $block_data;
            }
        }

        return $errors;
    }

    /**
     * Sanitize block data to prevent XSS and malformed payloads
     *
     * @param array $block
     * @return array
     */
    public static function sanitize_block( $block ) {
        if ( ! is_array( $block ) ) {
            return array();
        }

        $id = ! empty( $block['id'] ) ? sanitize_text_field( $block['id'] ) : 'blk_' . wp_generate_password( 8, false, false );
        $type = ! empty( $block['type'] ) ? sanitize_key( $block['type'] ) : 'rich_text';
        $version = ! empty( $block['version'] ) ? intval( $block['version'] ) : 1;
        $data = isset( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

        // Extract and sanitize source_ids if provided on the block level
        $source_ids = array();
        if ( ! empty( $block['source_ids'] ) && is_array( $block['source_ids'] ) ) {
            foreach ( $block['source_ids'] as $sid ) {
                if ( is_string( $sid ) && trim( $sid ) !== '' ) {
                    $source_ids[] = sanitize_text_field( $sid );
                }
            }
        } elseif ( ! empty( $block['source_id'] ) && is_string( $block['source_id'] ) ) {
            $source_ids[] = sanitize_text_field( $block['source_id'] );
        }

        $result_block = array(
            'id'      => $id,
            'type'    => $type,
            'version' => $version,
            'data'    => $sanitized_data,
        );

        if ( ! empty( $source_ids ) ) {
            $result_block['source_ids'] = array_values( array_unique( $source_ids ) );
        }

        if ( ! empty( $block['source_type'] ) ) {
            $result_block['source_type'] = sanitize_key( $block['source_type'] );
        }

        if ( isset( $block['is_internal_methodology'] ) ) {
            $result_block['is_internal_methodology'] = (bool) $block['is_internal_methodology'];
        }

        return $result_block;
    }

    private static function sanitize_data_recursive( $data ) {
        $result = array();
        foreach ( $data as $k => $v ) {
            $key = sanitize_text_field( $k );
            if ( is_array( $v ) ) {
                $result[ $key ] = self::sanitize_data_recursive( $v );
            } elseif ( is_string( $v ) ) {
                // Allow safe inline markup for editorial text (strong, em, a, code)
                $result[ $key ] = wp_kses( $v, array(
                    'strong' => array(),
                    'b'      => array(),
                    'em'     => array(),
                    'i'      => array(),
                    'a'      => array( 'href' => array(), 'target' => array(), 'rel' => array() ),
                    'code'   => array( 'class' => array() ),
                    'span'   => array( 'class' => array() ),
                    'p'      => array(),
                    'br'     => array(),
                ) );
            } else {
                $result[ $key ] = $v;
            }
        }
        return $result;
    }

    /**
     * Register standard default block types
     */
    private static function register_default_blocks() {
        $standard_types = array(
            'rich_text'       => 'Standard formatted paragraphs and inline copy.',
            'intro'           => 'Lead editorial introduction paragraph with accent styling.',
            'heading'         => 'Section heading (H2, H3, H4) with optional subline.',
            'statement'       => 'Bold punchy takeaway statement in large display type.',
            'quote'           => 'Blockquote with author, title, and optional citation.',
            'list'            => 'Bulleted or numbered structured feature points.',
            'checklist'       => 'Interactive actionable checklist items with status.',
            'key_takeaway'    => 'Key takeaway callout box with icon and badge.',
            'callout'         => 'Information, warning, tip, or example card.',
            'stat'            => 'Key metric callout (number, label, description, change).',
            'comparison'      => 'Side-by-side Before/After or Old Way/Cora Way block.',
            'steps'           => 'Numbered workflow progression steps.',
            'image'           => 'Single image with caption, alt text, and aspect ratio.',
            'gallery'         => 'Multi-image grid or carousel with lightboxing.',
            'infographic'     => 'Diagram or workflow visual graphic with caption.',
            'chart'           => 'Structured data visualization chart.',
            'table'           => 'Tabular structured data matrix with column headers.',
            'video'           => 'Embedded or hosted explanatory video player.',
            'embed'           => 'External interactive embed or frame.',
            'contextual_cta'  => 'Mid-content contextual call to action card.',
            'product_mention' => 'First-party Cora platform feature teaser card.',
            'newsletter'      => 'Organic email subscription capture card.',
            'lead_magnet_cta' => 'Guide chapter download or PDF asset capture card.',
            'faq'             => 'Frequently asked questions accordion block.',
            'divider'         => 'Visual subtle separator line.',
            'code'            => 'Syntax-highlighted code snippet with language tag.',
            'download'        => 'Direct asset download button with file size & type badge.',
        );

        foreach ( $standard_types as $type => $desc ) {
            self::register_block( $type, array(
                'version'     => 1,
                'description' => $desc,
            ) );
        }
    }
}
