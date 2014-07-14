<?
/*

A Class to handle the Opauth response data

@author: Jeremy Voros

Requires RedBean running
Injects an Opauth object for error/auth checking
Injects Oauth response from provider
Checks for new vs returning users
Creates new users
Updates last visit for returning users
Returns an error or the user_id

*/

class OauthResponse
{
    protected $response;    //oauth response from provider, e.g. google
    protected $opauth;      //opauth object for error checking
    protected $error;       // error to return
    protected $user;        // user to return
    
    public function __construct($response, Opauth $opauth) {
        // assign variables passed in
        $this->response = $response;
        $this->opauth = $opauth;
        
        // run the error check on instantiation
        $this->errorCheck();
    }
    
    private function errorCheck() {
        if (array_key_exists('error', $this->response)) {
            $this->error = '<strong style="color: red;">Authentication error: </strong> Opauth returns error auth response.'."<br>\n";
        } elseif (empty($this->response['auth']) || empty($this->response['timestamp']) || empty($this->response['signature']) || empty($this->response['auth']['provider']) || empty($this->response['auth']['uid'])) {
            $this->error = '<strong style="color: red;">Invalid auth response: </strong>Missing key auth response components.'."<br>\n";
        } elseif (!$this->opauth->validate(sha1(print_r($this->response['auth'], true)), $this->response['timestamp'], $this->response['signature'], $reason)) {
            $this->error = '<strong style="color: red;">Invalid auth response: </strong>'.$reason.".<br>\n";
        }
    } 
    
    private function newUser() {
        $this->user                 = R::dispense('user');
        $this->user->created        = date("Y-m-d H:i:s");
        $this->user->last           = date("Y-m-d H:i:s");
        $this->user->name           = $this->response['auth']['info']['name'];
        $this->user->email          = $this->response['auth']['info']['email'];
        $newuser_id = R::store($this->user); 
        return $this->user->export();
    } 
    

    private function checkDomain($e, $d) {
        $email_pieces = explode("@", $e);
        $email = $email_pieces[0];
        $domain = $email_pieces[1];
        if ($domain != $d) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    
    public function getUser() {
        
        // check for error from Opauth
        if (isset($this->error)) { 
            $response['error'] = $this->error;
            return $response;
        } 
            
        // check for error from domain
        elseif (!$this->checkDomain($this->response['auth']['info']['email'], "denverem.org")) {
            $response['error'] = "You must use your @denverem.org email address";
            return $response;
        } 
            
        // correct domain, no Opauth errors
        else {
        
            // check for user
            $user = R::findOne('user', ' email = ?', array($this->response['auth']['info']['email']));
           
            // new user vs returning user
            if (is_null($user)) { 
                $u = $this->newUser(); 
            } else { 
                $user->last = date("Y-m-d H:i:s"); 
                $uid = R::store($user);
                $u = $user->export();
            }
            
            $response['user'] = $u;
            return $response;
        }
    }
}




