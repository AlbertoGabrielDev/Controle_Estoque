<?php

namespace App\Enums;

enum TaxMethod: int
{
    case Percent = 1;
    case Fixed = 2;
    case Formula = 3;
}
