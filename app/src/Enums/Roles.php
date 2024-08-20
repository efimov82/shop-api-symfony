<?php

namespace App\Enums;

enum Roles: string
{
  case UNKNOWN = "";
  case USER = "ROLE_USER";
  case ADMIN = "ROLE_ADMIN";
}
