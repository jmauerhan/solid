<?php

Class UserRegistration
{
   public function register(Request $request): Response
   {
       //Collect user input
       if (!$request->has('email') || !$request->has('password')) {
           return new Response('register', ['error' => 'Please provide an email and a password']);
       }
       $user           = new User();
       $user->email    = $request->get('email');
       $user->password = $request->get('password');
       
       //Validate user input
       if ($this->emailIsRegistered($user->getEmail())) {
           return new Response('register', ['error' => 'Your email address is already registered']);
       }
       
       $this->saveUser($user);
   }
   
   private function emailIsRegistered(string $email): bool
   {
       $dsn    = 'host='.$_ENV['DB_HOST'].' dbname='.$_ENV['DB_DB'].' password='.$_ENV['DB_PASS'].' user='.$_ENV['DB_USER'];
       $db     = pg_connect($dsn);
       $result = pg_query($db, "SELECT id FROM users WHERE (email='{$email}')");
       $rows   = pg_num_rows($result);
       return $rows > 0;
   }
   
   private function saveUser(User $user): void
   {
       $dsn    = 'host='.$_ENV['DB_HOST'].' dbname='.$_ENV['DB_DB'].' password='.$_ENV['DB_PASS'].' user='.$_ENV['DB_USER'];
       $db  = pg_connect($dsn);
       pg_query($db, "INSERT INTO users(email, password) VALUES('{$user->email}', '{$user->password}')");
   }


}

