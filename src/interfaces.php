<?php
namespace LoginMaster;

interface Handler{
    public function handle($state, $target=0);
}

interface PasswordEngine{
    public function verify($input, $database);
}

interface TwoFactor{
    public function challange($userId);
}