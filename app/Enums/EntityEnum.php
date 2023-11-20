<?php

namespace App\Enums;

enum EntityEnum: string
{
    case AUDIT = 'Audit';

    case REPORT = 'Report';

    case PROJECT = 'Project';

    case UTILITY = 'Utility';

    case ALL = 'All';
}
