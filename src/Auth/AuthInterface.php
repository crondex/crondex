<?php namespace Crondex\Auth;

interface AuthInterface
{
    public function removeLoggedInUser();
    public function login($user);
    public function check($user_id);
    public function logout();
}
