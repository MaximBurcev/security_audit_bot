<?php

namespace App\Enums;

enum EntityEnum: string
{
    case AUDITS = 'audits';

    case REPORTS = 'reports';

    case PROJECTS = 'projects';

    case UTILITIES = 'utilities';

    case ALL = 'all';
}
