<?php
/*
 * Project:     Beacon
 * File:        Migration.php
 * Date:        2026-06-12
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Database;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Builder;

abstract class Migration
{
    abstract public function up();
    abstract public function down();

    protected function schema(): Builder
    {
        return Manager::schema();
    }
}