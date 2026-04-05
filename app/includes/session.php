<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function tc_role(): string
{
    $r = $_SESSION['tc_role'] ?? 'hiker';

    return $r === 'organizer' ? 'organizer' : 'hiker';
}

function tc_set_role(string $r): void
{
    $_SESSION['tc_role'] = $r === 'organizer' ? 'organizer' : 'hiker';
}

function tc_logged_in(): bool
{
    return !empty($_SESSION['tc_logged_in']);
}

function tc_set_logged_in(bool $v): void
{
    $_SESSION['tc_logged_in'] = $v;
}

function tc_display_name(): string
{
    $n = $_SESSION['tc_name'] ?? '';

    return $n !== '' ? $n : (tc_role() === 'organizer' ? 'Alex Rivers' : 'Jordan Peak');
}
