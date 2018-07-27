# SOLID in Practice

### @jessicamauerhan | https://joind.in/talk/34870 | @PHPDet

---

```php
<?php

class UserRegistration
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

        //Send Confirmation Email
        $this->sendConfirmationEmail($user->getEmail());
        return new Response('register', ['success' => 'You are registered! Please check your email!']);
    }

    private function emailIsRegistered(string $email): bool
    {
        $dsn    = 'host=' . $_ENV['DB_HOST'] .
                  ' dbname=' . $_ENV['DB_DB'] .
                  ' password=' . $_ENV['DB_PASS'] .
                  ' user=' . $_ENV['DB_USER'];
        $db     = pg_connect($dsn);
        $result = pg_query($db, "SELECT id FROM users WHERE (email='{$email}')");
        $rows   = pg_num_rows($result);
        return $rows > 0;
    }

    private function saveUser(User $user): void
    {
        $dsn = 'host=' . $_ENV['DB_HOST'] .
               ' dbname=' . $_ENV['DB_DB'] .
               ' password=' . $_ENV['DB_PASS'] .
               ' user=' . $_ENV['DB_USER'];
        $db  = pg_connect($dsn);
        pg_query($db, "INSERT INTO users(email, password) VALUES('{$user->email}', '{$user->password}')");
    }

    private function sendConfirmationEmail(string $email)
    {
        $subject = "Confirm Email";
        $message = "Please <a>click here</a> to confirm your email!";
        $headers = "From: mysite@email.com";
        mail($email, $subject, $message, $headers);
    }

}
```

@[7-13](Collecting User Input)
@[23-33](Validate User Input)
@[15-18](Validate User Input)

Note:
When we're creating the user registration controller we're going to start by putting those four steps in the UserRegistration class. I'm using a framework to handle all the routing, the view rendering, etc, so we can focus on those four steps from our business logic.

So we'll typically start by creating that register action method, which will accept the Request and handle it, and probably return a Response. 

Collecting the user input is probably pretty easy with our Request object, and we'll throw that data into a User object. 

Before we can do that though we should ensure the data is even there. So if it's not, we'll return a response with an error.
