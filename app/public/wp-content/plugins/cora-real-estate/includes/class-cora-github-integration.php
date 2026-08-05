<?php
if ( ! defined( "ABSPATH" ) ) exit;

if ( ! class_exists( 'Cora_GitHub_Integration' ) ) {
class Cora_GitHub_Integration {
	const GITHUB_API        = "https://api.github.com";
	const GITHUB_DEVICE_URL = "https://github.com/login/device/code";
	const GITHUB_TOKEN_URL  = "https://github.com/login/oauth/access_token";
	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) self::$instance = new self();
		return self::$instance;
	}

	private function __construct() {
		$this->register_ajax_actions();
		add_action( "admin_init", array( $this, "register_settings" ) );
	}

	public function register_settings() {
		register_setting( "cora_options", "cora_github_client_id", array( "type" => "string", "sanitize_callback" => "sanitize_text_field" ) );
	}

	private function register_ajax_actions() {
		foreach ( array( "cora_github_initiate_device_flow", "cora_github_poll_device_token", "cora_github_create_repo", "cora_github_commit_page", "cora_github_get_status", "cora_github_create_branch", "cora_github_get_branches", "cora_github_disconnect", "cora_github_save_pat", "cora_github_link_repo" ) as $action ) {
			add_action( "wp_ajax_" . $action, array( $this, $action ) );
		}
	}

	private function get_client_id() { return get_option( "cora_github_client_id", "" ); }
	private function get_user_token( $uid = null ) {
		$token = get_user_meta( $uid ?: get_current_user_id(), "cora_github_access_token", true );
		if ( empty( $token ) ) {
			$token = get_option( "cora_git_sync_token", "" );
		}
		return $token;
	}
	private function set_user_token( $token, $uid = null ) {
		update_user_meta( $uid ?: get_current_user_id(), "cora_github_access_token", sanitize_text_field( $token ) );
		update_option( "cora_git_sync_token", sanitize_text_field( $token ) );
	}
	private function get_repo( $uid = null ) {
		$repo = get_user_meta( $uid ?: get_current_user_id(), "cora_github_repo", true );
		if ( empty( $repo ) ) {
			$repo = get_option( "cora_git_sync_repo", "" );
		}
		return $repo;
	}
	private function set_repo( $repo, $uid = null ) {
		update_user_meta( $uid ?: get_current_user_id(), "cora_github_repo", sanitize_text_field( $repo ) );
		update_option( "cora_git_sync_repo", sanitize_text_field( $repo ) );
	}


	private function github_request( $method, $endpoint, $body = array(), $token = null ) {
		if ( ! $token ) $token = $this->get_user_token();
		$args = array( "method" => strtoupper( $method ), "headers" => array( "Authorization" => "Bearer " . $token, "Accept" => "application/vnd.github+json", "Content-Type" => "application/json", "X-GitHub-Api-Version" => "2022-11-28", "User-Agent" => "Cora-Platform/1.0" ), "timeout" => 20 );
		if ( ! empty( $body ) ) $args["body"] = wp_json_encode( $body );
		$response = wp_remote_request( self::GITHUB_API . $endpoint, $args );
		if ( is_wp_error( $response ) ) return array( "error" => $response->get_error_message(), "code" => 0 );
		return array( "code" => (int) wp_remote_retrieve_response_code( $response ), "data" => json_decode( wp_remote_retrieve_body( $response ), true ) );
	}

	public function cora_github_initiate_device_flow() {
		check_ajax_referer( "cora_ajax_nonce", "nonce" );
		$client_id = $this->get_client_id();
		if ( empty( $client_id ) ) wp_send_json_error( array( "message" => "GitHub OAuth App Client ID not configured. Add it in WordPress Admin > Settings.", "no_client_id" => true ) );
		$response = wp_remote_post( self::GITHUB_DEVICE_URL, array( "headers" => array( "Accept" => "application/json", "Content-Type" => "application/x-www-form-urlencoded", "User-Agent" => "Cora-Platform/1.0" ), "body" => http_build_query( array( "client_id" => $client_id, "scope" => "repo user:email" ) ), "timeout" => 20 ) );
		if ( is_wp_error( $response ) ) wp_send_json_error( array( "message" => $response->get_error_message() ) );
		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( isset( $data["error"] ) ) wp_send_json_error( array( "message" => $data["error_description"] ?? $data["error"] ) );
		set_transient( "cora_gh_device_" . get_current_user_id(), $data["device_code"], 900 );
		wp_send_json_success( array( "user_code" => $data["user_code"], "verification_uri" => $data["verification_uri"], "interval" => $data["interval"] ?? 5 ) );
	}

	public function cora_github_poll_device_token() {
		check_ajax_referer( "cora_ajax_nonce", "nonce" );
		$device_code = get_transient( "cora_gh_device_" . get_current_user_id() );
		if ( empty( $device_code ) ) wp_send_json_error( array( "message" => "Session expired. Please start again." ) );
		$response = wp_remote_post( self::GITHUB_TOKEN_URL, array( "headers" => array( "Accept" => "application/json", "Content-Type" => "application/x-www-form-urlencoded", "User-Agent" => "Cora-Platform/1.0" ), "body" => http_build_query( array( "client_id" => $this->get_client_id(), "device_code" => $device_code, "grant_type" => "urn:ietf:params:oauth:grant-type:device_code" ) ), "timeout" => 20 ) );
		if ( is_wp_error( $response ) ) wp_send_json_error( array( "message" => $response->get_error_message() ) );
		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( isset( $data["error"] ) ) {
			if ( in_array( $data["error"], array( "authorization_pending", "slow_down" ), true ) ) wp_send_json_success( array( "status" => "pending" ) );
			wp_send_json_error( array( "message" => $data["error_description"] ?? $data["error"] ) );
		}
		if ( ! empty( $data["access_token"] ) ) {
			$this->set_user_token( $data["access_token"] );
			delete_transient( "cora_gh_device_" . get_current_user_id() );
			$user_resp = $this->github_request( "GET", "/user", array(), $data["access_token"] );
			$username  = $user_resp["data"]["login"] ?? "";
			update_user_meta( get_current_user_id(), "cora_github_username", sanitize_text_field( $username ) );
			wp_send_json_success( array( "status" => "connected", "username" => $username ) );
		}
		wp_send_json_error( array( "message" => "Unexpected response." ) );
	}

	public function cora_github_create_repo() {
		check_ajax_referer( "cora_ajax_nonce", "nonce" );
		$repo_name = isset( $_POST["repo_name"] ) ? sanitize_title( wp_unslash( $_POST["repo_name"] ) ) : "cora-" . sanitize_title( get_bloginfo( "name" ) ?: "website" );
		$result    = $this->github_request( "POST", "/user/repos", array( "name" => $repo_name, "description" => "Cora version control", "private" => true, "auto_init" => true ) );
		if ( isset( $result["error"] ) || ! in_array( $result["code"], array( 200, 201 ), true ) ) {
			$msg = $result["data"]["message"] ?? ( $result["error"] ?? "Failed to create repository." );
			if ( strpos( $msg, "already exists" ) !== false ) $msg = "Repository name already exists. Try a different name.";
			wp_send_json_error( array( "message" => $msg ) );
		}
		$this->set_repo( $result["data"]["full_name"] );
		$this->write_initial_manifest( $result["data"]["full_name"] );
		wp_send_json_success( array( "repo" => $result["data"]["full_name"], "html_url" => $result["data"]["html_url"] ) );
	}

	private function write_initial_manifest( $repo ) {
		$m = wp_json_encode( array( "platform" => "Cora", "site" => get_bloginfo( "name" ), "site_url" => get_site_url(), "created_at" => gmdate( "c" ) ), JSON_PRETTY_PRINT );
		$this->github_request( "PUT", "/repos/{$repo}/contents/cora-manifest.json", array( "message" => "chore: initialise Cora manifest", "content" => base64_encode( $m ) ) );
	}

	public function cora_github_commit_page() {
		check_ajax_referer( "cora_ajax_nonce", "nonce" );
		$page_id = isset( $_POST["page_id"] ) ? absint( $_POST["page_id"] ) : 0;
		$message = isset( $_POST["message"] ) ? sanitize_text_field( wp_unslash( $_POST["message"] ) ) : "";
		$branch  = isset( $_POST["branch"] ) ? sanitize_text_field( wp_unslash( $_POST["branch"] ) ) : "main";
		if ( ! $page_id ) wp_send_json_error( array( "message" => "Invalid page ID." ) );
		$repo = $this->get_repo();
		if ( empty( $repo ) ) wp_send_json_error( array( "message" => "No repository connected." ) );
		$post      = get_post( $page_id );
		$slug      = $post->post_name ?: sanitize_title( $post->post_title );
		$snapshot  = array( "page_id" => $page_id, "title" => $post->post_title, "slug" => $slug, "status" => $post->post_status, "modified" => $post->post_modified_gmt, "committed_at" => gmdate( "c" ), "design" => json_decode( get_post_meta( $page_id, "_elementor_data", true ) ) );
		$file_path = "pages/{$slug}/design.json";
		$msg       = $message ?: "publish: " . $post->post_title . " · " . gmdate( "Y-m-d H:i" ) . " UTC";
		$encoded   = base64_encode( wp_json_encode( $snapshot, JSON_PRETTY_PRINT ) );
		$existing  = $this->github_request( "GET", "/repos/{$repo}/contents/{$file_path}?ref={$branch}" );
		$sha       = ( $existing["code"] === 200 && ! empty( $existing["data"]["sha"] ) ) ? $existing["data"]["sha"] : null;
		$body      = array( "message" => $msg, "content" => $encoded, "branch" => $branch );
		if ( $sha ) $body["sha"] = $sha;
		$result = $this->github_request( "PUT", "/repos/{$repo}/contents/{$file_path}", $body );
		if ( isset( $result["error"] ) || ! in_array( $result["code"], array( 200, 201 ), true ) ) wp_send_json_error( array( "message" => $result["data"]["message"] ?? "Commit failed." ) );
		wp_send_json_success( array( "commit_url" => $result["data"]["commit"]["html_url"] ?? "", "message" => $msg ) );
	}

	public function cora_github_get_status() {
		check_ajax_referer( "cora_ajax_nonce", "nonce" );
		$token = $this->get_user_token();
		if ( empty( $token ) ) wp_send_json_success( array( "connected" => false, "has_clientid" => ! empty( $this->get_client_id() ) ) );
		$user_resp = $this->github_request( "GET", "/user", array(), $token );
		if ( $user_resp["code"] !== 200 ) {
			delete_user_meta( get_current_user_id(), "cora_github_access_token" );
			delete_option( "cora_git_sync_token" );
			wp_send_json_success( array( "connected" => false, "has_clientid" => ! empty( $this->get_client_id() ) ) );
		}
		$repo    = $this->get_repo();
		$saved_username = get_user_meta( get_current_user_id(), "cora_github_username", true );
		if ( empty( $saved_username ) ) {
			$saved_username = get_option( "cora_git_sync_username", "" );
		}
		$payload = array(
			"connected" => true,
			"username" => $saved_username ?: ( $user_resp["data"]["login"] ?? "" ),
			"repo" => $repo,
			"has_repo" => ! empty( $repo ),
			"has_clientid" => ! empty( $this->get_client_id() )
		);
		if ( ! empty( $repo ) ) {
			$br = $this->github_request( "GET", "/repos/{$repo}/branches?per_page=50" );
			if ( $br["code"] === 200 ) $payload["branches"] = array_column( $br["data"], "name" );
			$cr = $this->github_request( "GET", "/repos/{$repo}/commits?per_page=5" );
			if ( $cr["code"] === 200 ) $payload["recent_commits"] = array_map( function( $c ) { return array( "sha" => substr( $c["sha"], 0, 7 ), "message" => $c["commit"]["message"], "date" => $c["commit"]["committer"]["date"], "url" => $c["html_url"] ); }, (array) $cr["data"] );

			// Check working directory status for current active page
			$page_id = isset( $_POST["page_id"] ) ? absint( $_POST["page_id"] ) : 0;
			$branch  = isset( $_POST["branch"] ) ? sanitize_text_field( wp_unslash( $_POST["branch"] ) ) : "main";
			if ( $page_id ) {
				$post = get_post( $page_id );
				if ( $post ) {
					$slug = $post->post_name ?: sanitize_title( $post->post_title );
					$file_path = "pages/{$slug}/design.json";
					$existing  = $this->github_request( "GET", "/repos/{$repo}/contents/{$file_path}?ref={$branch}" );
					
					if ( $existing["code"] === 200 && ! empty( $existing["data"]["content"] ) ) {
						$content = json_decode( base64_decode( $existing["data"]["content"] ), true );
						$git_modified = isset( $content["modified"] ) ? $content["modified"] : "";
						$local_modified = $post->post_modified_gmt;
						
						// If local modified time is different, mark it modified
						if ( empty( $git_modified ) || $local_modified !== $git_modified ) {
							$payload["page_status"] = array(
								"modified" => true,
								"file_path" => $file_path,
								"suggested_message" => "update " . strtolower( $post->post_title ) . " page layout"
							);
						} else {
							$payload["page_status"] = array(
								"modified" => false,
								"file_path" => $file_path,
								"suggested_message" => ""
							);
						}
					} else {
						// Not found on git yet
						$payload["page_status"] = array(
							"modified" => true,
							"file_path" => $file_path,
							"suggested_message" => "create " . strtolower( $post->post_title ) . " page layout"
						);
					}
				}
			}
		}
		wp_send_json_success( $payload );
	}

	public function cora_github_create_branch() {
		check_ajax_referer( "cora_ajax_nonce", "nonce" );
		$branch_name = isset( $_POST["branch_name"] ) ? sanitize_title( wp_unslash( $_POST["branch_name"] ) ) : "";
		$from_branch = isset( $_POST["from_branch"] ) ? sanitize_text_field( wp_unslash( $_POST["from_branch"] ) ) : "main";
		$repo        = $this->get_repo();
		if ( empty( $branch_name ) || empty( $repo ) ) wp_send_json_error( array( "message" => "Branch name required." ) );
		$ref_resp = $this->github_request( "GET", "/repos/{$repo}/git/ref/heads/{$from_branch}" );
		if ( $ref_resp["code"] !== 200 ) wp_send_json_error( array( "message" => "Source branch not found." ) );
		$result = $this->github_request( "POST", "/repos/{$repo}/git/refs", array( "ref" => "refs/heads/{$branch_name}", "sha" => $ref_resp["data"]["object"]["sha"] ) );
		if ( isset( $result["error"] ) || ! in_array( $result["code"], array( 200, 201 ), true ) ) wp_send_json_error( array( "message" => $result["data"]["message"] ?? "Failed to create branch." ) );
		wp_send_json_success( array( "branch" => $branch_name ) );
	}

	public function cora_github_get_branches() {
		check_ajax_referer( "cora_ajax_nonce", "nonce" );
		$repo   = $this->get_repo();
		if ( empty( $repo ) ) wp_send_json_error( array( "message" => "No repository." ) );
		$result = $this->github_request( "GET", "/repos/{$repo}/branches?per_page=50" );
		if ( $result["code"] !== 200 ) wp_send_json_error( array( "message" => "Could not fetch branches." ) );
		wp_send_json_success( array( "branches" => array_column( (array) $result["data"], "name" ) ) );
	}

	public function cora_github_disconnect() {
		global $wpdb;
		$uid = get_current_user_id();
		delete_user_meta( $uid, "cora_github_access_token" );
		delete_user_meta( $uid, "cora_github_username" );
		delete_user_meta( $uid, "cora_github_repo" );
		delete_option( "cora_git_sync_token" );
		delete_option( "cora_git_sync_repo" );
		delete_option( "cora_git_sync_branch" );
		delete_option( "cora_git_sync_username" );

		wp_cache_delete( "cora_git_sync_repo", "options" );
		wp_cache_delete( "cora_git_sync_branch", "options" );
		wp_cache_delete( "cora_git_sync_token", "options" );
		wp_cache_delete( "alloptions", "options" );

		// Clear from active canvas themes DB table as well
		$table  = $wpdb->prefix . "cora_canvas_themes";
		$themes = $wpdb->get_results( "SELECT id, settings FROM {$table}" );
		if ( $themes ) {
			foreach ( $themes as $t ) {
				$s = json_decode( $t->settings, true ) ?: array();
				unset( $s["github_repo"], $s["github_branch"], $s["lovable_pat"] );
				$wpdb->update( $table, array( "settings" => json_encode( $s ), "updated_at" => current_time( "mysql" ) ), array( "id" => $t->id ) );
			}
		}

		if ( function_exists( "wp_cache_flush" ) ) {
			wp_cache_flush();
		}

		wp_send_json_success( array( "message" => "Disconnected." ) );
	}

	public function cora_github_save_pat() {
		check_ajax_referer( "cora_ajax_nonce", "nonce" );
		$pat = isset( $_POST["pat"] ) ? sanitize_text_field( wp_unslash( $_POST["pat"] ) ) : "";
		if ( empty( $pat ) ) wp_send_json_error( array( "message" => "Personal Access Token is required." ) );
		$this->set_user_token( $pat );

		// Query GitHub API to get the username and verify the PAT
		$user_resp = $this->github_request( "GET", "/user", array(), $pat );
		if ( $user_resp["code"] !== 200 ) {
			delete_user_meta( get_current_user_id(), "cora_github_access_token" );
			delete_option( "cora_git_sync_token" );
			wp_send_json_error( array( "message" => "Invalid token. GitHub returned code " . $user_resp["code"] ) );
		}
		$username = $user_resp["data"]["login"] ?? "";
		update_user_meta( get_current_user_id(), "cora_github_username", sanitize_text_field( $username ) );
		update_option( "cora_git_sync_username", sanitize_text_field( $username ) );
		wp_send_json_success( array( "status" => "connected", "username" => $username ) );

	}

	public function cora_github_link_repo() {
		check_ajax_referer( "cora_ajax_nonce", "nonce" );
		$repo = isset( $_POST["repo"] ) ? sanitize_text_field( wp_unslash( $_POST["repo"] ) ) : "";
		if ( empty( $repo ) ) wp_send_json_error( array( "message" => "Repository path is required." ) );

		// Verify repository exists
		$result = $this->github_request( "GET", "/repos/{$repo}" );
		if ( $result["code"] !== 200 ) {
			wp_send_json_error( array( "message" => "Repository not found or access denied. Ensure the format is owner/repo." ) );
		}
		$this->set_repo( $repo );
		wp_send_json_success( array( "repo" => $repo, "html_url" => $result["data"]["html_url"] ) );
	}
}

Cora_GitHub_Integration::get_instance();
}
