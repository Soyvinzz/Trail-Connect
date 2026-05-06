<?php
declare(strict_types=1);

final class SettingsController
{
    public function handle(string $action): bool
    {
        return match ($action) {
            'start_2fa_setup' => $this->startTwoFactorSetup(),
            'enable_2fa' => $this->enableTwoFactor(),
            'disable_2fa' => $this->disableTwoFactor(),
            default => false,
        };
    }

    private function startTwoFactorSetup(): bool
    {
        if (!tc_two_factor_library_ready()) {
            header('Location: index.php?page=settings&error=two_factor_lib_missing');
            exit;
        }
        $secret = tc_two_factor_begin_setup(tc_role());
        if ($secret === '') {
            header('Location: index.php?page=settings&error=two_factor_setup');
            exit;
        }
        header('Location: index.php?page=settings&msg=two_factor_scan');
        exit;
    }

    private function enableTwoFactor(): bool
    {
        $code = (string) ($_POST['otp_code'] ?? '');
        if (tc_two_factor_enable(tc_role(), $code)) {
            header('Location: index.php?page=settings&msg=two_factor_enabled');
            exit;
        }
        header('Location: index.php?page=settings&error=two_factor_invalid');
        exit;
    }

    private function disableTwoFactor(): bool
    {
        tc_two_factor_disable(tc_role());
        header('Location: index.php?page=settings&msg=two_factor_disabled');
        exit;
    }
}
