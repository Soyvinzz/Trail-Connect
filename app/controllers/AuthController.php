<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/UserModel.php';

final class AuthController
{
    private UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function handle(string $action): bool
    {
        return match ($action) {
            'register' => $this->register(),
            'login' => $this->login(),
            'verify_2fa' => $this->verifyTwoFactor(),
            'logout' => $this->logout(),
            'request_password_reset' => $this->requestPasswordReset(),
            'reset_password' => $this->resetPassword(),
            'admin_toggle_user' => $this->adminToggleUser(),
            'resend_verification' => $this->resendVerification(),
            'verify_email' => $this->verifyEmail(),
            default => false,
        };
    }

    private function register(): bool
    {
        $role = (string) ($_POST['role'] ?? 'hiker');
        tc_set_role($role);
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $password2 = (string) ($_POST['password2'] ?? '');
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $currentAddress = trim((string) ($_POST['current_address'] ?? ''));
        $hikingLevel = trim((string) ($_POST['hiking_level'] ?? ''));
        $minorHikesCompleted = (string) ($_POST['minor_hikes_completed'] ?? '');
        $majorHikesCompleted = (string) ($_POST['major_hikes_completed'] ?? '');
        $bioNote = trim((string) ($_POST['bio_note'] ?? ''));
        if ($name === '') {
            header('Location: index.php?page=register&error=name');
            exit;
        }
        if ($email === '') {
            header('Location: index.php?page=register&error=email');
            exit;
        }
        if (strlen($password) < 8) {
            header('Location: index.php?page=register&error=password_short');
            exit;
        }
        if ($password !== $password2) {
            header('Location: index.php?page=register&error=password_mismatch');
            exit;
        }
        $isHiker = $role !== 'organizer';
        $allowedLevels = ['beginner', 'minor', 'intermediate', 'advanced'];
        if ($isHiker && $phone === '') {
            header('Location: index.php?page=register&error=phone');
            exit;
        }
        if ($isHiker && !preg_match('/^[0-9+()\\-\\s]{7,20}$/', $phone)) {
            header('Location: index.php?page=register&error=phone_invalid');
            exit;
        }
        if ($isHiker && $currentAddress === '') {
            header('Location: index.php?page=register&error=current_address');
            exit;
        }
        if ($isHiker && !in_array($hikingLevel, $allowedLevels, true)) {
            header('Location: index.php?page=register&error=hiking_level');
            exit;
        }
        if ($isHiker && ($minorHikesCompleted === '' || !ctype_digit($minorHikesCompleted))) {
            header('Location: index.php?page=register&error=minor_hikes_completed');
            exit;
        }
        if ($isHiker && ($majorHikesCompleted === '' || !ctype_digit($majorHikesCompleted))) {
            header('Location: index.php?page=register&error=major_hikes_completed');
            exit;
        }
        if ($isHiker && strlen($bioNote) < 10) {
            header('Location: index.php?page=register&error=bio_note');
            exit;
        }
        $minorHikeCount = max(0, (int) $minorHikesCompleted);
        $majorHikeCount = max(0, (int) $majorHikesCompleted);
        try {
            tc_db_migrate();
            if ($this->users->findByEmail($email) !== null) {
                header('Location: index.php?page=register&error=email_exists');
                exit;
            }
            $user = $this->users->create($name, $email, $password, $role, [
                'phone_number' => $isHiker ? $phone : '',
                'current_address' => $isHiker ? $currentAddress : '',
                'hiking_level' => $isHiker ? $hikingLevel : '',
                'minor_hikes_completed' => $isHiker ? $minorHikeCount : null,
                'major_hikes_completed' => $isHiker ? $majorHikeCount : null,
                'bio_note' => $isHiker ? $bioNote : '',
            ]);
            $verifyToken = bin2hex(random_bytes(24));
            tc_db_email_verification_create((int) ($user['id'] ?? 0), $verifyToken, 86400);
            $_SESSION['tc_verify_demo_link'] = 'index.php?page=verify_email&token=' . urlencode($verifyToken);
            header('Location: index.php?page=verify_email&sent=1&email=' . urlencode($email));
            exit;
        } catch (\Throwable $e) {
            header('Location: index.php?page=register&error=db');
            exit;
        }
    }

    private function login(): bool
    {
        tc_set_role((string) ($_POST['role'] ?? 'hiker'));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if ($email === '') {
            header('Location: index.php?page=login&error=email');
            exit;
        }
        if ($password === '') {
            header('Location: index.php?page=login&error=password');
            exit;
        }
        $rate = tc_login_rate_limit_status($email);
        if (!empty($rate['blocked'])) {
            header('Location: index.php?page=login&error=rate_limited');
            exit;
        }
        try {
            tc_db_migrate();
            $user = $this->users->findByEmail($email);
        } catch (\Throwable $e) {
            header('Location: index.php?page=login&error=db');
            exit;
        }
        if (!is_array($user)) {
            tc_login_rate_limit_register_failure($email);
            header('Location: index.php?page=login&error=badpass');
            exit;
        }
        if (empty($user['is_verified'])) {
            header('Location: index.php?page=login&error=email_unverified&email=' . urlencode($email));
            exit;
        }
        if (!empty($user['is_disabled'])) {
            header('Location: index.php?page=login&error=account_disabled');
            exit;
        }
        if (!password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            tc_login_rate_limit_register_failure($email);
            header('Location: index.php?page=login&error=badpass');
            exit;
        }
        tc_login_rate_limit_reset($email);
        tc_set_auth_user($user);
        $role = tc_role();
        $twoFactor = tc_two_factor_status($role);
        if (!empty($twoFactor['enabled']) && (string) $twoFactor['secret'] !== '') {
            tc_set_logged_in(false);
            tc_two_factor_start_login($role, $email);
            header('Location: index.php?page=login&two_factor=1&role=' . $role);
            exit;
        }
        tc_two_factor_clear_login();
        header('Location: index.php?page=dashboard');
        exit;
    }

    private function logout(): bool
    {
        tc_set_logged_in(false);
        unset($_SESSION['tc_avatar_path']);
        tc_two_factor_clear_login();
        header('Location: index.php?page=landing');
        exit;
    }

    private function verifyTwoFactor(): bool
    {
        $pending = tc_two_factor_pending_context();
        if (!is_array($pending)) {
            header('Location: index.php?page=login&error=two_factor_expired');
            exit;
        }
        $role = (string) ($pending['role'] ?? 'hiker');
        $pendingEmail = (string) ($pending['email'] ?? '');
        tc_set_role($role);
        $status = tc_two_factor_status($role);
        $code = (string) ($_POST['otp_code'] ?? '');
        if (tc_two_factor_verify_code((string) ($status['secret'] ?? ''), $code)) {
            $user = $this->users->findByEmail($pendingEmail);
            if (is_array($user)) {
                tc_set_auth_user($user);
            } else {
                tc_set_logged_in(true);
            }
            tc_two_factor_clear_login();
            header('Location: index.php?page=dashboard');
            exit;
        }
        header('Location: index.php?page=login&two_factor=1&error=two_factor_invalid&role=' . $role);
        exit;
    }

    private function requestPasswordReset(): bool
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        if ($email === '') {
            header('Location: index.php?page=forgot_password&error=email');
            exit;
        }
        try {
            tc_db_migrate();
            $user = $this->users->findByEmail($email);
            if (is_array($user) && (int) ($user['id'] ?? 0) > 0) {
                $token = bin2hex(random_bytes(24));
                tc_db_password_reset_create((int) $user['id'], $token, 3600);
                $_SESSION['tc_reset_demo_link'] = 'index.php?page=reset_password&token=' . urlencode($token);
            }
            header('Location: index.php?page=forgot_password&sent=1');
            exit;
        } catch (\Throwable $e) {
            header('Location: index.php?page=forgot_password&error=db');
            exit;
        }
    }

    private function resetPassword(): bool
    {
        $token = trim((string) ($_POST['token'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $password2 = (string) ($_POST['password2'] ?? '');
        if ($token === '') {
            header('Location: index.php?page=reset_password&error=token');
            exit;
        }
        if (strlen($password) < 8) {
            header('Location: index.php?page=reset_password&token=' . urlencode($token) . '&error=password_short');
            exit;
        }
        if ($password !== $password2) {
            header('Location: index.php?page=reset_password&token=' . urlencode($token) . '&error=password_mismatch');
            exit;
        }
        try {
            tc_db_migrate();
            $ok = tc_db_password_reset_consume($token, $password);
            if (!$ok) {
                header('Location: index.php?page=reset_password&error=token_expired');
                exit;
            }
            unset($_SESSION['tc_reset_demo_link']);
            header('Location: index.php?page=login&reset=1');
            exit;
        } catch (\Throwable $e) {
            header('Location: index.php?page=reset_password&token=' . urlencode($token) . '&error=db');
            exit;
        }
    }

    private function adminToggleUser(): bool
    {
        if (!tc_is_admin_user()) {
            header('Location: index.php?page=dashboard');
            exit;
        }
        $userId = (int) ($_POST['user_id'] ?? 0);
        $disable = (string) ($_POST['disable'] ?? '0') === '1';
        if ($userId > 0) {
            try {
                $this->users->setDisabled($userId, $disable);
            } catch (\Throwable $e) {
                header('Location: index.php?page=admin&msg=update_failed');
                exit;
            }
        }
        header('Location: index.php?page=admin&msg=user_updated');
        exit;
    }

    private function resendVerification(): bool
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        if ($email === '') {
            header('Location: index.php?page=login&error=email');
            exit;
        }
        try {
            tc_db_migrate();
            $user = $this->users->findByEmail($email);
            if (is_array($user) && empty($user['is_verified']) && (int) ($user['id'] ?? 0) > 0) {
                $verifyToken = bin2hex(random_bytes(24));
                tc_db_email_verification_create((int) $user['id'], $verifyToken, 86400);
                $_SESSION['tc_verify_demo_link'] = 'index.php?page=verify_email&token=' . urlencode($verifyToken);
            }
            header('Location: index.php?page=verify_email&sent=1&email=' . urlencode($email));
            exit;
        } catch (\Throwable $e) {
            header('Location: index.php?page=login&error=db');
            exit;
        }
    }

    private function verifyEmail(): bool
    {
        $token = trim((string) ($_POST['token'] ?? ''));
        if ($token === '') {
            header('Location: index.php?page=verify_email&error=token');
            exit;
        }
        try {
            tc_db_migrate();
            $ok = tc_db_email_verification_consume($token);
            if (!$ok) {
                header('Location: index.php?page=verify_email&error=token_expired');
                exit;
            }
            unset($_SESSION['tc_verify_demo_link']);
            header('Location: index.php?page=login&verified=1');
            exit;
        } catch (\Throwable $e) {
            header('Location: index.php?page=verify_email&error=db');
            exit;
        }
    }
}
