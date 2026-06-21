<?php
// Auth
$router->get('auth/login',           'AuthController@showLogin');
$router->post('auth/login',          'AuthController@login');
$router->get('auth/logout',          'AuthController@logout');
$router->get('auth/forgot',          'AuthController@showForgot');
$router->post('auth/forgot',         'AuthController@forgot');
$router->get('auth/reset/{token}',   'AuthController@showReset');
$router->post('auth/reset/{token}',  'AuthController@reset');

// Dashboard
$router->get('',                     'DashboardController@index');
$router->get('dashboard',            'DashboardController@index');

// Tickets
$router->get('tickets',              'TicketController@index');
$router->get('tickets/create',       'TicketController@create');
$router->post('tickets/create',      'TicketController@store');
$router->get('tickets/{id}',         'TicketController@show');
$router->post('tickets/{id}/reply',  'TicketController@reply');
$router->post('tickets/{id}/status', 'TicketController@updateStatus');
$router->post('tickets/{id}/assign', 'TicketController@assign');
$router->post('tickets/{id}/tag',    'TicketController@tagUpdate');
$router->post('tickets/{id}/watch',  'TicketController@watch');
$router->post('tickets/{id}/timer',  'TicketController@timer');
$router->post('tickets/{id}/log-time','TicketController@logTime');
$router->post('tickets/{id}/clone',  'TicketController@cloneTicket');
$router->post('tickets/{id}/merge',  'TicketController@merge');
$router->post('tickets/{id}/alias',  'TicketController@alias');
$router->post('tickets/{id}/delete', 'TicketController@delete');
$router->post('tickets/{id}/archive','TicketController@archive');
$router->get('tickets/{id}/history', 'TicketController@history');

// Users
$router->get('users',                'UserController@index');
$router->get('users/create',         'UserController@create');
$router->post('users/create',        'UserController@store');
$router->get('users/{id}/edit',      'UserController@edit');
$router->post('users/{id}/edit',     'UserController@update');
$router->post('users/{id}/delete',   'UserController@delete');

// Profile
$router->get('profile',              'ProfileController@index');
$router->post('profile',             'ProfileController@update');
$router->post('profile/password',    'ProfileController@password');
$router->post('profile/avatar',      'ProfileController@avatar');

// Knowledge Base
$router->get('kb',                   'KbController@index');
$router->get('kb/create',            'KbController@create');
$router->post('kb/create',           'KbController@store');
$router->get('kb/{id}',              'KbController@show');
$router->get('kb/{id}/edit',         'KbController@edit');
$router->post('kb/{id}/edit',        'KbController@update');
$router->post('kb/{id}/delete',      'KbController@delete');
$router->get('kb/categories',        'KbController@categories');
$router->post('kb/categories',       'KbController@storeCategory');

// Settings
$router->get('settings',             'SettingsController@index');
$router->post('settings',            'SettingsController@update');
$router->get('settings/sla',         'SettingsController@sla');
$router->post('settings/sla',        'SettingsController@storeSla');
$router->post('settings/sla/{id}/delete', 'SettingsController@deleteSla');
$router->get('settings/custom-fields',   'SettingsController@customFields');
$router->post('settings/custom-fields',  'SettingsController@storeField');
$router->post('settings/custom-fields/{id}/delete', 'SettingsController@deleteField');
$router->get('settings/templates',       'SettingsController@templates');
$router->post('settings/templates',      'SettingsController@storeTemplate');
$router->post('settings/templates/{id}/delete', 'SettingsController@deleteTemplate');
$router->get('settings/tags',            'SettingsController@tags');
$router->post('settings/tags',           'SettingsController@storeTag');
$router->post('settings/tags/{id}/delete','SettingsController@deleteTag');
$router->get('settings/saved-replies',   'SettingsController@savedReplies');
$router->post('settings/saved-replies',  'SettingsController@storeSavedReply');
$router->post('settings/saved-replies/{id}/delete','SettingsController@deleteSavedReply');
$router->get('settings/categories',      'SettingsController@categories');
$router->post('settings/categories',     'SettingsController@storeCategory');
$router->post('settings/categories/{id}/delete','SettingsController@deleteCategory');
$router->get('settings/smtp',            'SettingsController@smtp');
$router->post('settings/smtp',           'SettingsController@updateSmtp');
$router->get('settings/discord',         'SettingsController@discord');
$router->post('settings/discord',        'SettingsController@updateDiscord');
$router->get('settings/branding',        'SettingsController@branding');
$router->post('settings/branding',       'SettingsController@updateBranding');

// Analytics
$router->get('analytics',            'AnalyticsController@index');
$router->get('analytics/export',     'AnalyticsController@export');

// Install
$router->get('install',              'InstallController@index');
$router->post('install',             'InstallController@run');

// API (AJAX)
$router->get('api/saved-replies',    'ApiController@savedReplies');
$router->get('api/timer-status',     'ApiController@timerStatus');
$router->get('api/users-search',     'ApiController@usersSearch');
