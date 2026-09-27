<?php
/**
 * Cora Growth Workspace - Content Validation Engine
 *
 * Enforces strict pre-publishing validation rules across all content models.
 *
 * @package CoraWorkspace
 * @subpackage Growth
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_Content_Validator {

    /**
     * Validate a content entry payload
     *
     * @param array  $entry        The content entry data
     * @param string $action       'save_draft', 'publish', 'preview'
     * @param string $workspace_id Tenancy workspace ID
     * @return array Array with 'valid' (bool), 'errors' (array), 'warnings' (array)
     */
    public static function validate( $entry, $action = 'publish', $workspace_id = 'growth_cora_main_01' ) {
        global $wpdb;

        $errors = array();
        $warnings = array();

        // 1. Check content type
        $type = ! empty( $entry['type'] ) ? $entry['type'] : 'article';
        $type_def = Cora_Content_Type_Registry::get_type( $type );
        if ( ! $type_def ) {
            $errors[] = "Invalid content type: '{$type}'.";
            return array( 'valid' => false, 'errors' => $errors, 'warnings' => $warnings );
        }

        // 2. Title validation
        if ( empty( $entry['title'] ) || trim( $entry['title'] ) === '' ) {
            $errors[] = 'Content title is required.';
        } elseif ( mb_strlen( $entry['title'] ) > 255 ) {
            $warnings[] = 'Title is longer than 255 characters, which may get truncated in SERPs.';
        }

        // 3. Slug validation & uniqueness
        if ( empty( $entry['slug'] ) || trim( $entry['slug'] ) === '' ) {
            $errors[] = 'Content slug is required.';
        } else {
            $slug = sanitize_title( $entry['slug'] );
            if ( $slug !== $entry['slug'] ) {
                $warnings[] = "Slug contains non-standard characters and will be formatted as '{$slug}'.";
            }

            // Check slug uniqueness in this workspace
            $table_content = $wpdb->prefix . 'cora_content_entries';
            $current_id = ! empty( $entry['id'] ) ? $entry['id'] : '';
            
            $existing_slug = $wpdb->get_var( $wpdb->prepare(
                "SELECT id FROM {$table_content} WHERE workspace_id = %s AND slug = %s AND id != %s LIMIT 1",
                $workspace_id,
                $slug,
                $current_id
            ) );

            if ( $existing_slug ) {
                $errors[] = "The slug '{$slug}' is already in use by content ID '{$existing_slug}'.";
            }
        }

        // 4. Primary Keyword & Search Intent
        if ( empty( $entry['primary_keyword'] ) ) {
            if ( $action === 'publish' ) {
                $warnings[] = 'Primary keyword is recommended for optimal search indexing.';
            }
        }

        // 5. Excerpt validation
        if ( empty( $entry['excerpt'] ) && $action === 'publish' ) {
            $warnings[] = 'Excerpt is empty. Cora will generate one automatically from the introductory block.';
        }

        // 6. Content Blocks or Chapters validation
        if ( $type_def['supports_chapters'] ) {
            // Validate chapter structure for guides
            $chapters = ! empty( $entry['chapters'] ) ? $entry['chapters'] : array();
            if ( is_string( $chapters ) ) {
                $chapters = json_decode( $chapters, true );
            }

            if ( empty( $chapters ) || ! is_array( $chapters ) ) {
                if ( $action === 'publish' ) {
                    $errors[] = 'Guides must have at least one chapter configured.';
                }
            } else {
                $chapter_slugs = array();
                foreach ( $chapters as $c_idx => $chapter ) {
                    $c_num = isset( $chapter['number'] ) ? $chapter['number'] : ( $c_idx + 1 );
                    if ( empty( $chapter['title'] ) ) {
                        $errors[] = "Chapter {$c_num} is missing a title.";
                    }
                    if ( empty( $chapter['slug'] ) ) {
                        $errors[] = "Chapter {$c_num} is missing a slug.";
                    } else {
                        if ( in_array( $chapter['slug'], $chapter_slugs, true ) ) {
                            $errors[] = "Duplicate chapter slug '{$chapter['slug']}' in Chapter {$c_num}.";
                        }
                        $chapter_slugs[] = $chapter['slug'];
                    }

                    // Validate chapter blocks
                    if ( ! empty( $chapter['blocks'] ) ) {
                        $block_errors = Cora_Block_Registry::validate_blocks( $chapter['blocks'] );
                        foreach ( $block_errors as $b_err ) {
                            $errors[] = "Chapter {$c_num}: " . $b_err;
                        }
                    }
                }
            }
        } else {
            // Standard block content validation
            $blocks = ! empty( $entry['content'] ) ? $entry['content'] : ( ! empty( $entry['content_blocks'] ) ? $entry['content_blocks'] : array() );
            if ( is_string( $blocks ) ) {
                $blocks = json_decode( $blocks, true );
            }

            if ( empty( $blocks ) || ! is_array( $blocks ) ) {
                if ( $action === 'publish' ) {
                    $errors[] = 'Content must contain at least one content block before publishing.';
                }
            } else {
                $block_errors = Cora_Block_Registry::validate_blocks( $blocks );
                $errors = array_merge( $errors, $block_errors );
            }
        }

        // 7. SEO Metadata validation for publish action
        if ( $action === 'publish' ) {
            $seo = ! empty( $entry['seo'] ) ? $entry['seo'] : array();
            if ( is_string( $seo ) ) {
                $seo = json_decode( $seo, true );
            }

            if ( empty( $seo['title'] ) && empty( $entry['title'] ) ) {
                $errors[] = 'SEO meta title is required.';
            }

            if ( empty( $seo['meta_description'] ) && empty( $entry['excerpt'] ) ) {
                $warnings[] = 'SEO meta description is empty; excerpt will be used as default.';
            }
        }

        // 8. Sources structure validation if provided
        if ( ! empty( $entry['sources'] ) ) {
            $sources = is_string( $entry['sources'] ) ? json_decode( $entry['sources'], true ) : $entry['sources'];
            if ( is_array( $sources ) ) {
                foreach ( $sources as $s_idx => $source ) {
                    if ( ! is_array( $source ) || empty( $source['title'] ) ) {
                        $warnings[] = "Source item {$s_idx} is missing a title.";
                    }
                }
            }
        }

        $is_valid = empty( $errors );

        return array(
            'valid'    => $is_valid,
            'errors'   => $errors,
            'warnings' => $warnings,
        );
    }
}
