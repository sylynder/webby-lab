<?php

use Base\Route\Route;

// Auto Login Route
Route::get('auth/auto-login', 'Auth/WebLoginController::autoLogin');

// Auth Roles Routes
Route::get('admin/roles/list', 'Auth/RoleController::index');
Route::get('admin/roles/create', 'Auth/RoleController::create');
Route::post('admin/roles/store', 'Auth/RoleController::store');
Route::get('admin/roles/edit/(:num)', 'Auth/RoleController/edit/$1');
Route::get('admin/roles/view/(:num)', 'Auth/RoleController/show/$1');
Route::post('admin/roles/update/(:num)', 'Auth/RoleController/update/$1');
Route::get('admin/roles/assign-permission/(:num)', 'Auth/RoleController/assign/$1');
Route::post('admin/roles/add-permission/(:num)', 'Auth/RoleController/assign_role/$1');
Route::post('admin/roles/edit-permission/(:num)', 'Auth/RoleController/edit_assign/$1');
Route::post('admin/roles/revoke-permission/(:num)/(:num)', 'Auth/RoleController/revoke/$1/$2');
Route::delete('admin/roles/delete/(:num)', 'Auth/RoleController/delete/$1');

// Auth Permissions Routes
Route::get('admin/permissions/list', 'Auth/PermissionController::index');
Route::get('admin/permissions/create', 'Auth/PermissionController::create');
Route::post('admin/permissions/store', 'Auth/PermissionController::store');
Route::get('admin/permissions/view/(:num)', 'Auth/PermissionController/show/$1');
Route::get('admin/permissions/edit/(:num)', 'Auth/PermissionController/edit/$1');
Route::post('admin/permissions/update/(:num)', 'Auth/PermissionController/update/$1');
Route::post('admin/permissions/get-actions', 'Auth/PermissionController/getActions');
Route::post('admin/permissions/get-actions/(:num)', 'Auth/PermissionController/getActions/$1');
Route::delete('admin/permissions/delete/(:num)', 'Auth/PermissionController/delete/$1');

// Auth User/Staff Permission Routes
// Route::get('admin/user/user-permissions/(:any)', 'Auth/PermissionController/user_permissions/$1');
// Route::get('admin/user/staff-permissions/(:any)', 'Auth/PermissionController/staff_permissions/$1');
// Route::get('admin/user/revoke-permission/(:num).(:any)', 'Auth/PermissionController/revoke_permission/$1/$2');
// Route::post('admin/user/add-permission/(:any)', 'Auth/PermissionController/add_permission/$1');

// Auth User/Staff Roles Routes
Route::get('admin/users/member-roles/(:any)', 'Auth/RoleController/memberRoles/$1');
Route::get('admin/users/user-roles/(:any)', 'Auth/RoleController/userRoles/$1');
Route::get('admin/users/staff-roles/(:any)', 'Auth/RoleController/staffRoles/$1');
Route::get('admin/users/revoke-role/{id}/{user-id}', 'Auth/RoleController/revokeRole/$1/$2');
Route::post('admin/users/add-role/{user-id}', 'Auth/RoleController/addRole/$1');


$route = Route::include();
