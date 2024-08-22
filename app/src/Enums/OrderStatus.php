<?php

namespace App\Enums;

enum OrderStatus: int
{
  case NEW = 0;
  case IN_PROCCESS = 1;
  case CONFIRMED = 2;
  case DELIVERED = 4;
  case CANCELED = 5;
}
