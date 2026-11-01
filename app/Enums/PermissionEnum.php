<?php

namespace App\Enums;

enum PermissionEnum: string
{
    case USER_LIST = 'user.list';
    case USER_VIEW = 'user.view';
    case USER_CREATE = 'user.create';
    case USER_EDIT = 'user.edit';
    case USER_DELETE = 'user.delete';
    case USER_PASSWORD_UPDATE = 'user.password_update';

    case ROLE_LIST = 'role.list';
    case ROLE_VIEW = 'role.view';
    case ROLE_CREATE = 'role.create';
    case ROLE_EDIT = 'role.edit';
    case ROLE_DELETE = 'role.delete';

    case GLOBAL_SETTING = 'global.setting';
    case GLOBAL_SETTING_GENERAL = 'global.setting.general';
    case GLOBAL_SETTING_SMS_TEMPLATE = 'global.setting.sms_template';
    case GLOBAL_SETTING_PUSH_TEMPLATE = 'global.setting.push_template';
    case GLOBAL_SETTING_EMAIL_TEMPLATE = 'global.setting.email_template';

    case HEALTH_MONITORING_VIEW = 'health_monitoring.view';

    public function label(): string
    {
        return match ($this) {
            self::USER_LIST => 'User List',
            self::USER_VIEW => 'View User',
            self::USER_CREATE => 'Create User',
            self::USER_EDIT => 'Edit User',
            self::USER_DELETE => 'Delete User',
            self::USER_PASSWORD_UPDATE => 'Update User Password',

            self::ROLE_LIST => 'Role List',
            self::ROLE_VIEW => 'View Role',
            self::ROLE_CREATE => 'Create Role',
            self::ROLE_EDIT => 'Edit Role',
            self::ROLE_DELETE => 'Delete Role',

            self::GLOBAL_SETTING => 'Global Setting',
            self::GLOBAL_SETTING_GENERAL => 'General Setting',
            self::GLOBAL_SETTING_SMS_TEMPLATE => 'SMS Template Setting',
            self::GLOBAL_SETTING_PUSH_TEMPLATE => 'Push Template Setting',
            self::GLOBAL_SETTING_EMAIL_TEMPLATE => 'Email Template Setting',

            self::HEALTH_MONITORING_VIEW => 'Infrastructure Monitoring View',
        };
    }
}
