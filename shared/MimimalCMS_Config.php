<?php

const PUBLIC_DIR = __DIR__ . '/../public';
const VIEWS_DIR = __DIR__ . '/../app/Views';

// Default options for cookies.
const COOKIE_DEFAULT_SECURE = false;
const COOKIE_DEFAULT_HTTPONLY = true;
const COOKIE_DEFAULT_SAMESITE = 'lax';

// Options for session.
const FLASH_SESSION_KEY_NAME = 'mimimalFlashSession';
const SESSION_KEY_NAME = 'mimimalSession';
const SESSION_COOKIE_PARAMS = [
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax',
];

// Start session.
session_set_cookie_params(SESSION_COOKIE_PARAMS);
session_start();

// File validator.
const DEFAULT_MAX_FILE_SIZE = 20480;
const IMAGE_MIME_TYPE = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

const URL_STRING_PATTERN = '/^[a-zA-Z0-9-._~!$&\'()*+,;=:@\/?%]+$/';
const RELATIVE_PATH_PATTERN = '/^(?!(?:f|ht)tps?:\/\/)/i';