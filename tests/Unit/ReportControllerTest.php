<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\V1\ReportController;
use PHPUnit\Framework\TestCase;

class ReportControllerTest extends TestCase
{
    public function test_report_controller_exists(): void
    {
        $controller = new ReportController();
        $this->assertInstanceOf(ReportController::class, $controller);
    }

    public function test_report_controller_has_index_method(): void
    {
        $controller = new ReportController();
        $this->assertTrue(method_exists($controller, 'index'));
    }
}
