<?php

namespace Core\Drivers;

use App\lib\Version;
use Core\Joi\Start;
use Core\Router\Http\Request;
use Litebase\LitebaseClient;
use Litebase\Service\AriaStudio\Registration;

class Cloud
{

    public const VAL_CLOUD_USER = 'AriaStudio_installed_cloud_user';
    public const VAL_INSTALLED = 'AriaStudio_installed_fully';

    private Start $server;
    private LitebaseClient $CloudClient;

    /**
     * @param Start $server
     * @throws \Throwable
     */
    public function __construct(Start $server)
    {
        $this->server = $server;
        $this->CloudClient = $this->getLTEClient();
        $this->appHost = (new Request())->getUrl()->getHost();
    }

    /**
     * @return LitebaseClient
     * @throws \Throwable
     */
    private function getLTEClient()
    {

        $options = [];

        if ($this->server->getCache()->load(Cloud::VAL_CLOUD_USER) !== null) {
            $options['username'] = $this->server->getCache()->load(Cloud::VAL_CLOUD_USER);
        }

        if ($this->server->getCache()->load('AriaStudio_installed_api_key') !== null) {
            $options['client_id'] = $this->server->getCache()->load('AriaStudio_installed_api_key');
        } else {
            $options['client_id'] = Version::getPlaceHolderAPIKeys()['public'];
        }

        if ($this->server->getCache()->load('AriaStudio_installed_private_key') !== null) {
            $options['client_secret'] = $this->server->getCache()->load('AriaStudio_installed_private_key');
        } else {
            $options['client_secret'] = Version::getPlaceHolderAPIKeys()['private'];
        }


        $options['application_name'] = (new Request())->getUrl()->getHost();


        return new LitebaseClient($options);
    }


    /**
     * @return LitebaseClient
     */
    public function getCloudClient(): LitebaseClient
    {
        return $this->CloudClient;
    }


    /**
     * @return string
     */
    public function getCloudVersion(): string
    {
        return $this->CloudClient->getLibraryVersion();
    }

    /**
     * @return bool
     * @throws \Throwable
     */
    public function isAriaStudioInstalledFully(){
        return $this->server->getCache()->load(Cloud::VAL_INSTALLED) !== null;
    }

    public function setNewKeys($key, $private){

        //need to validate these lol
        $this->server->getCache()->save('AriaStudio_installed_api_key', $key);
        $this->server->getCache()->save('AriaStudio_installed_private_key', $private);
    }


    public function checkDomainRegistration(){

        if($this->server->getCache()->load('CF_checkDomainRegistration')){
            return $this->server->getCache()->load('CF_checkDomainRegistration');
        }

        $as = new Registration($this->getCloudClient());

       $dcheck = $as->isLinkTaken((new Request())->getUrl()->getHost());


            $this->server->getCache()->save('CF_checkDomainRegistration', $dcheck, array(
                $this->server->getCache()::EXPIRE => '3 minutes', // accepts also seconds or a timestamp.
                $this->server->getCache()::SLIDING => false,
            ));

            return $dcheck;

    }


    public function createAccount($email, $name, $password){
        $this->server->getCache()->save('Aria_Studio_Admin_Email',$email );
        $acc = new Registration($this->getCloudClient());
        $acc->createAccount($email, $name, $password);
        $this->getUser($email);
    }


    private function getUser($email){
        $acc = new Registration($this->getCloudClient());
       $this->server->getCache()->save(self::VAL_CLOUD_USER,$acc->getUsername($email) );
    }

    public function getCloudUserName(){
       return $this->server->getCache()->load(Cloud::VAL_CLOUD_USER) ?? $this->getUser($this->server->getCache()->load('Aria_Studio_Admin_Email'));
    }

    public function createProject($name){
        $proj = new Registration($this->getCloudClient());
        $proj->createproject($this->getCloudUserName(), $name, $this->appHost);

        $apikeys = $this->getAPIKeys();

    }


    public function getAPIKeys(){
        $proj = new Registration($this->getCloudClient());
        $apikeys = $proj->getAPIkeys($this->getCloudUserName(), $this->appHost);
        $this->server->getCache()->save('Broos_Action_Cloud_API_CRED',$apikeys );
        if(isset($apikeys)){
            $this->setNewKeys($apikeys['public'], $apikeys['private']);
        }
        return $apikeys;
    }

    /**
     * @return string|null
     */
    public function getAppHost(): ?string
    {
        return $this->appHost;
    }



}