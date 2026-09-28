<?php
/**
 * Cora Growth Workspace - Content Validation Engine
 *
 * Enforces strict pre-publishing validation rules across all content models,
 * including structured JSON blocks, canonical taxonomy, source attribution for stats,
 * relationship integrity, and asset compliance.
 *
 * @package CoraWorkspace
 * @subpackage Growth
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_Content_Validator {

    /**
     * Allowed source types
     */
    const ALLOWED_SOURCE_TYPES = array(
        'external',
        'first_party',
        'first_party_dataset',
        'internal_methodology',
    );

    /**
     * Allowed asset MIME types
     */
    const ALLOWED_ASSET_MIMES = array(
        'image/png',
        'image/jpeg',
        'image/webp',
        'image/svg+xml',
        'application/pdf',
    );

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

        // 2. Canonical Category / Taxonomy Validation
        if ( ! empty( $entry['category'] ) || ! empty( $entry['category_slug'] ) ) {
            $cat = ! empty( $entry['category'] ) ? $entry['category'] : $entry['category_slug'];
            $canonical_cats = Cora_Content_Type_Registry::get_canonical_categories();
            $cat_slug = sanitize_title( $cat );
            if ( ! array_key_exists( $cat_slug, $canonical_cats ) && ! in_array( $cat, $canonical_cats, true ) ) {
                $errors[] = "Invalid category '{$cat}'. Standard canonical categories: " . implode( ', ', array_keys( $canonical_cats ) ) . '.';
            }
        }

        // 3. Title validation
        if ( empty( $entry['title'] ) || trim( $entry['title'] ) === '' ) {
            $errors[] = 'Content title is required.';
        } elseif ( mb_strlen( $entry['title'] ) > 255 ) {
            $warnings[] = 'Title is longer than 255 characters, which may get truncated in SERPs.';
        }

        // 4. Slug validation & uniqueness
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

        // 5. Primary Keyword & Search Intent
        if ( empty( $entry['primary_keyword'] ) ) {
            if ( $action === 'publish' ) {
                $warnings[] = 'Primary keyword is recommended for optimal search indexing.';
            }
        }

        // 6. Excerpt validation
        if ( empty( $entry['excerpt'] ) && $action === 'publish' ) {
            $warnings[] = 'Excerpt is empty. Cora will generate one automatically from the introductory block.';
        }

        // 7. Structured Sources Validation & Source ID Registry
        $valid_source_ids = array();
        $sources = ! empty( $entry['sources'] ) ? $entry['sources'] : array();
        if ( is_string( $sources ) ) {
            $sources = json_decode( $sources, true );
        }

        if ( ! empty( $sources ) && is_array( $sources ) ) {
            foreach ( $sources as $s_idx => $source ) {
                if ( ! is_array( $source ) ) {
                    $errors[] = "Source at index {$s_idx} is not a valid object.";
                    continue;
                }

                $s_id = ! empty( $source['id'] ) ? sanitize_text_field( $source['id'] ) : ( ! empty( $source['title'] ) ? 'src_' . substr( md5( $source['title'] ), 0, 8 ) : "src_{$s_idx}" );
                $valid_source_ids[] = $s_id;

                if ( empty( $source['title'] ) ) {
                    $warnings[] = "Source item {$s_idx} ('{$s_id}') is missing a title.";
                }

                if ( ! empty( $source['source_type'] ) && ! in_array( $source['source_type'], self::ALLOWED_SOURCE_TYPES, true ) ) {
                    $errors[] = "Source '{$s_id}' has invalid source_type '{$source['source_type']}'. Allowed: " . implode( ', ', self::ALLOWED_SOURCE_TYPES ) . '.';
                }
            }
        }

        // 8. Content Blocks or Chapters validation + Stat Block Source Verification
        $all_blocks = array();

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

                    // Chapter blocks & featured asset check
                    $ch_blocks = ! empty( $chapter['blocks'] ) ? $chapter['blocks'] : array();
                    if ( empty( $ch_blocks ) && empty( $chapter['featured_asset'] ) && empty( $chapter['asset_id'] ) ) {
                        if ( $action === 'publish' ) {
                            $errors[] = "Chapter {$c_num} must have either content blocks or a featured asset attached.";
                        }
                    }

                    if ( ! empty( $ch_blocks ) ) {
                        $block_errors = Cora_Block_Registry::validate_blocks( $ch_blocks );
                        foreach ( $block_errors as $b_err ) {
                            $errors[] = "Chapter {$c_num}: " . $b_err;
                        }
                        foreach ( $ch_blocks as $blk ) {
                            $all_blocks[] = $blk;
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
                $all_blocks = $blocks;
            }
        }

        // Validate Stat Blocks Source Attributions
        foreach ( $all_blocks as $b_idx => $block ) {
            if ( ! is_array( $block ) ) continue;

            $b_type = ! empty( $block['type'] ) ? $block['type'] : '';
            $b_id = ! empty( $block['id'] ) ? $block['id'] : "block_{$b_idx}";

            if ( $b_type === 'stat' ) {
                $is_internal = false;
                if ( ! empty( $block['source_type'] ) && $block['source_type'] === 'internal_methodology' ) {
                    $is_internal = true;
                } elseif ( ! empty( $block['data']['source_type'] ) && $block['data']['source_type'] === 'internal_methodology' ) {
                    $is_internal = true;
                } elseif ( ! empty( $block['is_internal_methodology'] ) || ! empty( $block['data']['is_internal_methodology'] ) ) {
                    $is_internal = true;
                }

                if ( ! $is_internal ) {
                    $stat_source_ids = array();
                    if ( ! empty( $block['source_ids'] ) && is_array( $block['source_ids'] ) ) {
                        $stat_source_ids = array_merge( $stat_source_ids, $block['source_ids'] );
                    } elseif ( ! empty( $block['source_id'] ) && is_string( $block['source_id'] ) ) {
                        $stat_source_ids[] = $block['source_id'];
                    }

                    if ( ! empty( $block['data']['source_ids'] ) && is_array( $block['data']['source_ids'] ) ) {
                        $stat_source_ids = array_merge( $stat_source_ids, $block['data']['source_ids'] );
                    } elseif ( ! empty( $block['data']['source_id'] ) && is_string( $block['data']['source_id'] ) ) {
                        $stat_source_ids[] = $block['data']['source_id'];
                    }

                    $matched_sources = array_intersect( $stat_source_ids, $valid_source_ids );

                    if ( empty( $matched_sources ) ) {
                        $errors[] = "Stat block '{$b_id}' must reference at least one valid source ID from the sources list, or be flagged with source_type: 'internal_methodology' / is_internal_methodology: true.";
                    }
                }
            }
        }

        // 9. Relationship Validation
        $relationships = ! empty( $entry['relationships'] ) ? $entry['relationships'] : ( ! empty( $entry['relationships_json'] ) ? $entry['relationships_json'] : array() );
        if ( is_string( $relationships ) ) {
            $relationships = json_decode( $relationships, true );
        }

        if ( ! empty( $relationships ) && is_array( $relationships ) ) {
            $allowed_rels = Cora_Content_Type_Registry::get_canonical_relationships();
            $table_content = $wpdb->prefix . 'cora_content_entries';

            foreach ( $relationships as $r_idx => $rel ) {
                if ( ! is_array( $rel ) ) continue;

                $r_type = ! empty( $rel['type'] ) ? $rel['type'] : '';
                $target = ! empty( $rel['target_id'] ) ? $rel['target_id'] : ( ! empty( $rel['id'] ) ? $rel['id'] : ( ! empty( $rel['slug'] ) ? $rel['slug'] : ( ! empty( $rel['target_slug'] ) ? $rel['target_slug'] : '' ) ) );
                $is_optional = ! empty( $rel['optional'] );

                if ( ! in_array( $r_type, $allowed_rels, true ) ) {
                    $warnings[] = "Relationship type '{$r_type}' is non-standard. Recommended canonical types: " . implode( ', ', $allowed_rels ) . '.';
                }

                if ( empty( $target ) ) {
                    $errors[] = "Relationship at index {$r_idx} ({$r_type}) is missing target ID or slug.";
                    continue;
                }

                // Check resolution for tools or known static slugs
                if ( $r_type === 'related_tool' || $r_type === 'tool' ) {
                    continue; // Free tools are built-in platform routes under /tools/*
                }

                $known_static_slugs = array(
                    'agency-client-onboarding-playbook',
                    'agency-scope-creep-defence-system',
                    'high-ticket-retainer-proposal-blueprint',
                    'agency-profitability-margin-guide',
                    'retainer-calculator',
                    'gst-calculator'
                );
                if ( in_array( $target, $known_static_slugs, true ) ) {
                    continue;
                }

                $exists = $wpdb->get_var( $wpdb->prepare(
                    "SELECT id FROM {$table_content} WHERE workspace_id = %s AND (id = %s OR slug = %s) LIMIT 1",
                    $workspace_id,
                    $target,
                    $target
                ) );

                if ( ! $exists ) {
                    if ( $is_optional || $action === 'save_draft' ) {
                        $warnings[] = "Relationship target '{$target}' ({$r_type}) does not resolve to an existing content entry.";
                    } else {
                        $errors[] = "Relationship target '{$target}' ({$r_type}) does not resolve to an existing content entry in this workspace.";
                    }
                }
            }
        }

        // 10. Asset Validation
        $assets = ! empty( $entry['assets'] ) ? $entry['assets'] : ( ! empty( $entry['assets_json'] ) ? $entry['assets_json'] : array() );
        if ( is_string( $assets ) ) {
            $assets = json_decode( $assets, true );
        }

        $table_assets = $wpdb->prefix . 'cora_content_assets';
        $has_cover = false;
        $has_lead_magnet_asset = false;

        if ( ! empty( $assets ) && is_array( $assets ) ) {
            foreach ( $assets as $a_idx => $asset ) {
                if ( ! is_array( $asset ) ) continue;

                $a_id = ! empty( $asset['asset_id'] ) ? $asset['asset_id'] : ( ! empty( $asset['id'] ) ? $asset['id'] : '' );
                $a_role = ! empty( $asset['role'] ) ? $asset['role'] : '';
                $a_mime = ! empty( $asset['mime_type'] ) ? $asset['mime_type'] : '';
                $a_url = ! empty( $asset['file_url'] ) ? $asset['file_url'] : ( ! empty( $asset['url'] ) ? $asset['url'] : '' );
                $a_type = ! empty( $asset['type'] ) ? $asset['type'] : '';
                if ( $a_role === 'cover' || $a_role === 'og' || $a_role === 'cover_image' || $a_type === 'cover_image' || $a_type === 'cover' ) {
                    $has_cover = true;
                }
                if ( $a_role === 'pdf_lead_magnet' || $a_role === 'download' || $a_role === 'lead_magnet' || $a_type === 'pdf_lead_magnet' || $a_mime === 'application/pdf' ) {
                    $has_lead_magnet_asset = true;
                }

                // Verify existence in DB if asset_id is provided
                if ( ! empty( $a_id ) ) {
                    $db_asset = $wpdb->get_row( $wpdb->prepare(
                        "SELECT * FROM {$table_assets} WHERE workspace_id = %s AND asset_id = %s LIMIT 1",
                        $workspace_id,
                        $a_id
                    ), ARRAY_A );

                    if ( $db_asset ) {
                        $a_mime = $db_asset['mime_type'];
                        if ( empty( $alt_text ) && ! empty( $db_asset['alt_text'] ) ) {
                            $alt_text = trim( $db_asset['alt_text'] );
                        }
                    } elseif ( empty( $a_url ) ) {
                        $errors[] = "Asset ID '{$a_id}' was not found in registered workspace assets.";
                    }
                }

                // Check MIME type
                if ( ! empty( $a_mime ) && ! in_array( $a_mime, self::ALLOWED_ASSET_MIMES, true ) ) {
                    $errors[] = "Asset " . ( $a_id ? "'{$a_id}'" : "at index {$a_idx}" ) . " has unsupported MIME type '{$a_mime}'. Allowed: PNG, JPEG, WebP, SVG, PDF.";
                }

                // Alt text verification for editorial images
                $is_image = strpos( $a_mime, 'image/' ) === 0 || in_array( $a_role, array( 'cover', 'chapter_featured', 'infographic', 'image' ), true );
                if ( $is_image && empty( $alt_text ) && $action === 'publish' ) {
                    $errors[] = "Editorial image asset " . ( $a_id ? "'{$a_id}'" : "role '{$a_role}'" ) . " is missing required alt_text.";
                }
            }
        }

        // Check top-level cover or OG image fallback
        if ( ! empty( $entry['cover_image'] ) || ! empty( $entry['og_image'] ) || ! empty( $entry['seo']['og_image'] ) ) {
            $has_cover = true;
        }

        // For Articles: Cover image is required on publish
        if ( $type === 'article' && ! $has_cover && $action === 'publish' ) {
            $errors[] = 'Articles require a cover image before publishing.';
        }

        // For Guides: Cover image required + Lead magnet validation if enabled
        if ( $type === 'guide' ) {
            if ( ! $has_cover && $action === 'publish' ) {
                $errors[] = 'Guides require a cover image before publishing.';
            }

            $lead_magnet_enabled = ! empty( $entry['lead_magnet'] ) || ! empty( $entry['cta']['lead_magnet'] ) || ! empty( $entry['is_lead_magnet'] );
            if ( $lead_magnet_enabled && ! $has_lead_magnet_asset && $action === 'publish' ) {
                $errors[] = 'Lead magnet is enabled for this guide, but no downloadable PDF asset was registered.';
            }
        }

        // 11. SEO Metadata validation for publish action
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

        $is_valid = empty( $errors );

        return array(
            'valid'    => $is_valid,
            'errors'   => $errors,
            'warnings' => $warnings,
        );
    }
}
