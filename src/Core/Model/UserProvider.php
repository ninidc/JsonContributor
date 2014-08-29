<?php
namespace Core\Model;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\DBAL\Connection;
use Core\Controller\Component\Consultix;
 
class UserProvider implements UserProviderInterface
{

    private $conn;
 
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }
 

    public function loadUserByUsername($username)
    {

        global $app;

        $Consultix = new Consultix();

        $_username = isset($_POST["_username"]) ? $_POST["_username"] : null;
        $_password = isset($_POST["_password"]) ? $_POST["_password"] : null;

        $sql = '
            SELECT 
                * 
            FROM 
                users 
            WHERE 
                username = ?
        ';

        $stmt = $this->conn->executeQuery($sql, array(strtolower($username)));

        if (!$user = $stmt->fetch()) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }
      
        // If User can login to consultix... 
        if($_username && $_password) {
            $responseCx = $Consultix->login($_username, $_password);

            if($responseCx["status"] == 0) {
                //return new User($user['username'], $user['passwd'], explode(',', $user['roles']), true, true, true, true);    
                return new User($user['username'], $user['passwd'], explode(',', $user['roles']), true, true, true, true);    
            } 
        } else {
            return new User($user['username'], $user['passwd'], explode(',', $user['roles']), true, true, true, true);    
        }
        
        // User not logged...
        return new User($user['username'], "", explode(',', $user['roles']), true, true, true, true);
                
    }

 
    public function refreshUser(UserInterface $user)
    {

        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
 
        return $this->loadUserByUsername($user->getUsername());
    }
 

    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}

?>