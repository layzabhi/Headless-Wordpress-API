<?php
/**
 * JWT Authentication Handler
 * 
 * Implements token-based authentication for REST API
 * Provides stateless auth without server-side sessions
 */

class Headless_JWT_Auth {
    
    public static function init() {
        add_action('admin_notices', array(__CLASS__, 'check_jwt_config'));
        add_action('rest_api_init', array(__CLASS__, 'register_routes'));
        add_filter('determine_current_user', array(__CLASS__, 'validate_token_in_request'), 20);
    }
    
    public static function check_jwt_config() {
        if (!defined('JWT_AUTH_SECRET_KEY')) {
            echo '<div class="notice notice-warning"><p>';
            echo '<strong>JWT Auth:</strong> Add JWT_AUTH_SECRET_KEY to wp-config.php';
            echo '</p></div>';
        }
    }
    
    public static function register_routes() {
        $namespace = 'headless/v1/auth';
        
        register_rest_route($namespace, '/login', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'login'),
            'permission_callback' => '__return_true',
            'args' => array(
                'username' => array('required' => true),
                'password' => array('required' => true),
            ),
        ));
        
        register_rest_route($namespace, '/validate', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'validate'),
            'permission_callback' => '__return_true',
        ));
        
        register_rest_route($namespace, '/me', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_user'),
            'permission_callback' => 'is_user_logged_in',
        ));
    }
    
    public static function login($request) {
        $user = wp_authenticate(
            $request->get_param('username'),
            $request->get_param('password')
        );
        
        if (is_wp_error($user)) {
            return new WP_Error('auth_failed', 'Invalid credentials', array('status' => 401));
        }
        
        $token = self::generate_token($user);
        
        return rest_ensure_response(array(
            'token' => $token,
            'user' => array(
                'id' => $user->ID,
                'email' => $user->user_email,
                'name' => $user->display_name,
            ),
        ));
    }
    
    private static function generate_token($user) {
        if (!defined('JWT_AUTH_SECRET_KEY')) {
            return new WP_Error('jwt_missing', 'JWT not configured');
        }
        
        $payload = array(
            'iss' => get_bloginfo('url'),
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24 * 7), // 7 days
            'data' => array(
                'user' => array(
                    'id' => $user->ID,
                ),
            ),
        );
        
        return self::encode_jwt($payload);
    }
    
    private static function encode_jwt($payload) {
        $header = base64_encode(json_encode(array('typ' => 'JWT', 'alg' => 'HS256')));
        $payload = base64_encode(json_encode($payload));
        $signature = base64_encode(hash_hmac('sha256', "$header.$payload", JWT_AUTH_SECRET_KEY, true));
        
        return "$header.$payload.$signature";
    }
    
    private static function decode_jwt($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;
        
        list($header, $payload, $signature) = $parts;
        
        $expected_sig = base64_encode(
            hash_hmac('sha256', "$header.$payload", JWT_AUTH_SECRET_KEY, true)
        );
        
        if ($signature !== $expected_sig) return false;
        
        $payload = json_decode(base64_decode($payload), true);
        
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }
        
        return $payload;
    }
    
    public static function validate($request) {
        $auth_header = $request->get_header('Authorization');
        if (!$auth_header) {
            return new WP_Error('no_token', 'No token provided', array('status' => 401));
        }
        
        $token = str_replace('Bearer ', '', $auth_header);
        $payload = self::decode_jwt($token);
        
        if (!$payload) {
            return new WP_Error('invalid_token', 'Invalid token', array('status' => 401));
        }
        
        return rest_ensure_response(array('valid' => true));
    }
    
    public static function get_user($request) {
        $user = wp_get_current_user();
        
        return rest_ensure_response(array(
            'id' => $user->ID,
            'email' => $user->user_email,
            'name' => $user->display_name,
        ));
    }
    
    public static function validate_token_in_request($user_id) {
        if ($user_id) return $user_id;
        
        $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (!$auth_header) return $user_id;
        
        $token = str_replace('Bearer ', '', $auth_header);
        $payload = self::decode_jwt($token);
        
        return $payload['data']['user']['id'] ?? $user_id;
    }
}