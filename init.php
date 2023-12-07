<?php

require 'bootstrap.php';

if (!DB::exists()) {
    DB::create();
}
