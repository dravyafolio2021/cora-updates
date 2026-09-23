<?php
/**
 * Cora Platform Central Authorization & Security Context Engine (SEC-002, SEC-018)
 *
 * Implements strict, centralized tenant and object-level authorization,
 * server-side request context resolution, fail-closed policy evaluation,
 * and immutable security audit event logging.
 *
 * @package Cora_Workspace
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Immutable Server-Side Request Context Value Object
 */
class Cora_Request_Context {

    /**
     * Authenticated WordPress User ID
     * @var int
     */
    public $user_id = 0;

    /**
     * User Email Address
     * @var string
     */
    public $user_email = '';

    /**
     * Primary WordPress User Role
     * @var string
     */
    public $wp_role = 'anonymous';

    /**
     * Server-Derived Active Tenant / Workspace ID
     * @var string|int
     */
    public $tenant_id = '';

    /**
     * User's Role within the Active Tenant ('super_admin', 'owner', 'manager', 'member', 'guest', 'anonymous')
     * @var string
     */
    public $tenant_role = 'anonymous';

    /**
     * Whether user is a Platform Super Owner (Global Support / Super Admin)
     * @var bool
     */
    public $is_super_owner = false;

    /**
     * Request Correlation ID for tracing
     * @var string
     */
    public $correlation_id = '';

    /**
     * Request Source IP (Anonymized / Prefix)
     * @var string
     */
    public $ip_prefix = '';

    /**
     * Constructor
     */
    public function __construct( array $data = array() ) {
        foreach ( $data as $key => $value ) {
            if ( property_exists( $this, $key ) ) {
                $this->$key = $value;
            }
        }
        if ( empty( $this->correlation_id ) ) {
            $this->correlation_id = 'req_' . bin2hex( random_bytes( 8 ) );
        }
    }

    /**
     * Check if context represents an authenticated user
     * @return bool
     */
    public function is_authenticated() {
        return $this->user_id > 0;
    }

    /**
     * Check if user is an owner or super owner of the tenant
     * @return bool
     */
    public function is_owner_or_super() {
        return $this->is_super_owner || in_array( $this->tenant_role, array( 'owner', 'super_admin' ), true );
    }

    /**
     * Check if user is a manager or above
     * @return bool
     */
    public function is_manager_or_above() {
        return $this->is_super_owner || in_array( $this->tenant_role, array( 'owner', 'manager', 'super_admin' ), true );
    }
}

/**
 * Resolve Request Context strictly from server-side state
 *
 * @param int|null $user_id
 * @param string|null $requested_tenant_id
 * @return Cora_Request_Context
 */
function cora_get_request_context( $user_id = null, $requested_tenant_id = null ) {
    static $cached_contexts = array();

    if ( null === $user_id ) {
        $user_id = get_current_user_id();
    }
    $user_id = intval( $user_id );

    $cache_key = $user_id . ':' . (string) $requested_tenant_id;
    if ( isset( $cached_contexts[ $cache_key ] ) ) {
        return $cached_contexts[ $cache_key ];
    }

    $ip = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1' );
    $ip_prefix = preg_replace( '/(\d+)\.(\d+)\.(\d+)\.(\d+)/', '$1.$2.$3.0/24', $ip );

    if ( $user_id <= 0 ) {
        $context = new Cora_Request_Context( array(
            'user_id'        => 0,
            'user_email'     => '',
            'wp_role'        => 'anonymous',
            'tenant_id'      => '',
            'tenant_role'    => 'anonymous',
            'is_super_owner' => false,
            'ip_prefix'      => $ip_prefix,
        ) );
        $cached_contexts[ $cache_key ] = $context;
        return $context;
    }

    $user = get_userdata( $user_id );
    if ( ! $user ) {
        $context = new Cora_Request_Context( array( 'user_id' => 0, 'wp_role' => 'anonymous', 'tenant_role' => 'anonymous' ) );
        $cached_contexts[ $cache_key ] = $context;
        return $context;
    }

    $is_super = function_exists( 'cora_is_super_owner' ) ? cora_is_super_owner( $user ) : false;
    $wp_role  = ! empty( $user->roles ) ? reset( $user->roles ) : 'subscriber';

    // Server-side tenant derivation
    $derived_tenant = get_user_meta( $user_id, 'cora_agency_id', true );
    if ( empty( $derived_tenant ) ) {
        $derived_tenant = get_user_meta( $user_id, 'cora_workspace_id', true );
    }
    if ( empty( $derived_tenant ) && $is_super ) {
        $derived_tenant = 'default';
    }

    // Active tenant resolution
    $active_tenant = $derived_tenant;

    // Super owners may explicitly target a specific tenant for maintenance
    if ( $is_super && ! empty( $requested_tenant_id ) ) {
        $active_tenant = sanitize_text_field( $requested_tenant_id );
    }

    // Determine tenant role
    $tenant_role = 'member';
    if ( $is_super ) {
        $tenant_role = 'super_admin';
    } elseif ( in_array( $wp_role, array( 'administrator', 'cora_super_admin', 'cora_agency_owner' ), true ) ) {
        $tenant_role = 'owner';
    } elseif ( in_array( $wp_role, array( 'editor', 'cora_manager', 'cora_photographer' ), true ) ) {
        $tenant_role = 'manager';
    } elseif ( in_array( $wp_role, array( 'cora_client', 'subscriber' ), true ) ) {
        $tenant_role = 'client';
    }

    $context = new Cora_Request_Context( array(
        'user_id'        => $user_id,
        'user_email'     => strtolower( $user->user_email ),
        'wp_role'        => $wp_role,
        'tenant_id'      => $active_tenant,
        'tenant_role'    => $tenant_role,
        'is_super_owner' => $is_super,
        'ip_prefix'      => $ip_prefix,
    ) );

    $cached_contexts[ $cache_key ] = $context;
    return $context;
}

/**
 * Central Policy Authorization Gatekeeper (SEC-002)
 *
 * @param Cora_Request_Context $context
 * @param string $action (e.g. 'form.read', 'form.update', 'form.delete', 'task.read', 'task.update', 'task.delete', 'submission.read')
 * @param array $resource Optional resource details (e.g. ['tenant_id' => '...', 'resource_id' => 123, 'resource_type' => 'form', 'owner_id' => 45])
 * @return bool
 */
function cora_authorize( $context, $action, array $resource = array() ) {
    if ( ! ( $context instanceof Cora_Request_Context ) ) {
        $context = cora_get_request_context();
    }

    // Anonymous requests: deny everything except explicitly declared public actions
    if ( ! $context->is_authenticated() ) {
        $public_actions = array( 'public.form.submit', 'public.view', 'public.esign.verify' );
        if ( in_array( $action, $public_actions, true ) ) {
            return true;
        }
        cora_log_security_event( 'AUTHORIZATION_DENIED_ANONYMOUS', array(
            'action'        => $action,
            'resource_type' => $resource['resource_type'] ?? 'unknown',
            'resource_id'   => $resource['resource_id'] ?? 0,
        ), $context );
        return false;
    }

    // Platform Super Owner bypass (with security audit tracking)
    if ( $context->is_super_owner ) {
        return true;
    }

    // Cross-tenant validation: If resource declares a tenant_id, it MUST match context tenant_id
    $resource_tenant = $resource['tenant_id'] ?? ( $resource['agency_id'] ?? ( $resource['workspace_id'] ?? '' ) );
    if ( ! empty( $resource_tenant ) && ! empty( $context->tenant_id ) ) {
        if ( (string) $resource_tenant !== (string) $context->tenant_id ) {
            cora_log_security_event( 'CROSS_TENANT_ACCESS_DENIED', array(
                'action'          => $action,
                'user_tenant'     => $context->tenant_id,
                'target_tenant'   => $resource_tenant,
                'resource_type'   => $resource['resource_type'] ?? 'unknown',
                'resource_id'     => $resource['resource_id'] ?? 0,
            ), $context );
            return false;
        }
    }

    // Role-specific action policies
    $allowed = false;
    switch ( $action ) {
        // Forms
        case 'form.read':
        case 'form.list':
        case 'submission.read':
        case 'submission.export':
        case 'form.logs':
            $allowed = in_array( $context->tenant_role, array( 'owner', 'manager', 'member' ), true );
            break;

        case 'form.create':
        case 'form.update':
        case 'form.duplicate':
            $allowed = in_array( $context->tenant_role, array( 'owner', 'manager' ), true );
            break;

        case 'form.delete':
        case 'submission.delete':
        case 'form.settings':
            $allowed = in_array( $context->tenant_role, array( 'owner' ), true );
            break;

        // Tasks
        case 'task.read':
        case 'task.list':
        case 'task.create':
            $allowed = in_array( $context->tenant_role, array( 'owner', 'manager', 'member' ), true );
            break;

        case 'task.update':
        case 'task.status_change':
            $allowed = in_array( $context->tenant_role, array( 'owner', 'manager', 'member' ), true );
            break;

        case 'task.delete':
        case 'task.assign':
            $allowed = in_array( $context->tenant_role, array( 'owner', 'manager' ), true );
            break;

        // Canvas & Themes
        case 'canvas.read':
        case 'canvas.list':
            $allowed = in_array( $context->tenant_role, array( 'owner', 'manager', 'member' ), true );
            break;

        case 'canvas.publish':
        case 'canvas.create':
        case 'canvas.update':
            $allowed = in_array( $context->tenant_role, array( 'owner', 'manager' ), true );
            break;

        case 'canvas.delete':
            $allowed = in_array( $context->tenant_role, array( 'owner' ), true );
            break;

        // Vault & E-Sign
        case 'vault.read':
        case 'vault.list':
        case 'vault.download':
            $allowed = in_array( $context->tenant_role, array( 'owner', 'manager', 'member' ), true );
            break;

        case 'vault.upload':
        case 'vault.share':
        case 'vault.sign':
            $allowed = in_array( $context->tenant_role, array( 'owner', 'manager' ), true );
            break;

        case 'vault.delete':
            $allowed = in_array( $context->tenant_role, array( 'owner' ), true );
            break;

        // Workspace Management & Invites
        case 'workspace.invite':
        case 'workspace.settings':
            $allowed = in_array( $context->tenant_role, array( 'owner' ), true );
            break;

        // AI Actions
        case 'ai.chat':
        case 'ai.suggest':
            $allowed = in_array( $context->tenant_role, array( 'owner', 'manager', 'member' ), true );
            break;

        case 'ai.mutate':
            $allowed = in_array( $context->tenant_role, array( 'owner', 'manager' ), true );
            break;

        case 'ai.destructive':
            $allowed = in_array( $context->tenant_role, array( 'owner' ), true );
            break;

        default:
            $allowed = $context->is_owner_or_super();
            break;
    }

    if ( ! $allowed ) {
        cora_log_security_event( 'AUTHORIZATION_DENIED_ROLE', array(
            'action'        => $action,
            'tenant_role'   => $context->tenant_role,
            'resource_type' => $resource['resource_type'] ?? 'unknown',
            'resource_id'   => $resource['resource_id'] ?? 0,
        ), $context );
    }

    return $allowed;
}

/**
 * Structured Security Audit Event Logger (SEC-018)
 *
 * @param string $event_type
 * @param array $details
 * @param Cora_Request_Context|null $context
 */
function cora_log_security_event( $event_type, array $details = array(), $context = null ) {
    if ( ! ( $context instanceof Cora_Request_Context ) ) {
        $context = cora_get_request_context();
    }

    // Redact sensitive patterns from details
    $redacted_details = array();
    $sensitive_keys = array( 'password', 'token', 'secret', 'key', 'auth', 'cookie', 'signature_image', 'cora_password' );
    foreach ( $details as $k => $v ) {
        $lower_k = strtolower( (string) $k );
        $is_sensitive = false;
        foreach ( $sensitive_keys as $s ) {
            if ( strpos( $lower_k, $s ) !== false ) {
                $is_sensitive = true;
                break;
            }
        }
        if ( $is_sensitive ) {
            $redacted_details[ $k ] = '[REDACTED]';
        } elseif ( is_array( $v ) ) {
            $redacted_details[ $k ] = json_encode( $v );
        } else {
            $redacted_details[ $k ] = (string) $v;
        }
    }

    $event = array(
        'id'             => 'sec_' . bin2hex( random_bytes( 8 ) ),
        'timestamp'      => time(),
        'date_iso'       => gmdate( 'c' ),
        'event_type'     => sanitize_text_field( $event_type ),
        'user_id'        => $context->user_id,
        'user_email'     => $context->user_email,
        'tenant_id'      => $context->tenant_id,
        'tenant_role'    => $context->tenant_role,
        'correlation_id' => $context->correlation_id,
        'ip_prefix'      => $context->ip_prefix,
        'details'        => $redacted_details,
    );

    // Keep ring buffer of last 500 security events in options
    $log = get_option( 'cora_security_audit_events', array() );
    if ( ! is_array( $log ) ) {
        $log = array();
    }
    array_unshift( $log, $event );
    if ( count( $log ) > 500 ) {
        array_splice( $log, 500 );
    }
    update_option( 'cora_security_audit_events', $log, false );

    // Trigger action hook for external collectors or alerts
    do_action( 'cora_security_event_recorded', $event );

    return $event;
}
