<?php

namespace App\Packages\Auth\Enums;

class UserState
{
    public const SET = 'set';
    public const ACTIVE = 'active';
    public const APPROVED = 'approved';
    public const BANNED = 'banned';
    public const CANCELLED = 'cancelled';
    public const AVAILABLE = 'available';
    public const DELETED = 'deleted';
    public const ASSIGNED = 'assigned';
}
