<?
/*

A Class to handle the middleware route protection

@author: Jeremy Voros


*/

class AuthProtect
{
    protected $app;
    
    function __construct($app) {
        $this->app = $app;
    }
    
    public function protect() {
        return function() {        
            if ($_SESSION['loggedin'] == FALSE) {
                $this->app->response->redirect(BASE_URL . '/login', 303);
            }
        };
    }
    
}
