<?php

class HomeController implements Controller
{
    public function index()
    {
        $a = 1;
        $b = 2;
        return view('home.index', compact('a', 'b'));
    }

    public function show($arg)
    {
    }

    public function store()
    {
    }

    public function update($arg)
    {
    }
}