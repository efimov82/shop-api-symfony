<?php

namespace App\Enums;

enum Roles: string
{
  case UNKNOWN = "";
  case USER = "ROLE_USER";
  case MANAGER = "ROLE_MANAGER";
  case ADMIN = "ROLE_ADMIN";
}
