<?php

class Functions
{
    public function checkSession($sessionCreated)
    {
        //session currently set to 60 minutes
        if (time() - $sessionCreated > 3600) {
            //if over 60 minutes
            session_destroy();
            $_SESSION = array();
            //when timing out, send the user to the login screen
            //start defining the URL.
            $url = 'https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
            //check for a trailing slash.
            if ((substr($url, -1) == '/') OR (substr($url, -1) == '\\') ) {
                $url = substr ($url, 0, -1); // Chop off the slash.
            }
            //add the page.
            $url .= '/login.php';
            //take the user to the login page
            header('Location: ' . $url);
            //quit the script.
            exit();
        } else {
            $_SESSION['created'] = time();
        }
    }

    /**
     * @param string $un
     * @access public
     * check the unique id at a predetermined interval
     */
    public function checkUniqueRand($username)
    {
        //instantiate a new database object
        $tools = new MySQLTools();

        //fetch the session
        $uniqrand = $_SESSION['uniqrand'];

        //check the current uniqid against what's stored in the database
        if ($uniqrand != $tools->getUniqRand($username)) {
            //send the user to the login screen
            //start defining the URL.
            $url = 'https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
            //check for a trailing slash
            if ((substr($url, -1) == '/') OR (substr($url, -1) == '\\') ) {
                $url = substr ($url, 0, -1); // Chop off the slash.
            }
            //add the page
            $url .= '/login.php';
            //take the user to the login page
            header('Location: ' . $url);
            //quit the script
            exit();
        }
    }
}
