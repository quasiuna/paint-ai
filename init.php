<?php

require 'bootstrap.php';

use quasiuna\paintai\DB;

if (!DB::exists()) {
    DB::create();
}
