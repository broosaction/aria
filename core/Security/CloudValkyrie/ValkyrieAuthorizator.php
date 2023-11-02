<?php
/**
 * Copyright (c) 2023.  Broos Action
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * broosaction.com
 * ValkyrieAuthorizator.php created  09-01-2023  21:24
 */


namespace Core\Security\CloudValkyrie;

use Amp\Failure;
use Core\Router\Http\ApiResponse;
use Nette\Security\Permission;
use Nette\Security\SimpleIdentity;
use Nette\Utils\Json;
use function Amp\call;

class ValkyrieAuthorizator extends Permission
{


    private SimpleIdentity $user;

    public function setUser($user_id, $role){
        $this->user = new SimpleIdentity((string)$user_id, (string)$role);
    }

    public function isUserAllowed($resources, $action, $callback,  ...$args){
        if($this->isAllowed($this->user->roles[0], $resources,$action)){
          if(is_string($callback)){
              return true;
          }elseif (is_callable($callback)){
              try {
                  $callback(...$args);
              } catch (\Throwable $exception) {
                  return false;
              }
          }
          return true;
        }

        return false;
    }


    /**
     * @param string $view
     * @return void
     */
    public function blockView(string $view = 'error.404'){
        view()->code = 'Access Denied!';
        view()->message = $this->getUnAuthMsg();
        view()->render($view, false);
    }

    public function blockJson(){

        return (new ApiResponse())->addData('status', 'error')->addData('message', $this->getUnAuthMsg())->getJSONResponse();
    }


    private function getUnAuthMsg(){
        $messages = [
            "Access Denied: You do not have the necessary permissions to perform this action.",
            "Permission Denied: You're not authorized to perform this operation.",
            "Sorry, you can't do that: You don't have the required access rights.",
            "Access Error: You lack the permission needed to complete this task.",
            "Unauthorized: You're not allowed to proceed with this action.",
            "Oops! Permission denied: This feature is restricted to authorized users.",
            "No Access: Your current role does not grant access to this function.",
            "Restricted Action: Please contact your administrator for access.",
            "Denied Access: You must have higher privileges to use this feature.",
            "Sorry, you can't go there: Access to this area is restricted."
        ];

        // Pick a random index from the array
        $randomIndex = array_rand($messages);

        // Return the randomly selected message
        return $messages[$randomIndex];
    }

}