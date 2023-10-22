<?php

namespace App\Http\Controllers;

use App\Service\AuditService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public AuditService $service;

    public function __construct(AuditService $service)
    {
        $this->service = $service;
    }
}
