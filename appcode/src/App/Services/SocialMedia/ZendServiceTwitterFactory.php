<?php
declare(strict_types=1);
namespace App\Services\SocialMedia;

use Interop\Container\ContainerInterface;
use ZendService\Twitter\Twitter;

/**
 * Class ZendServiceTwitterFactory
 * @package App\Services\SocialMedia
 */
class ZendServiceTwitterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $configApp = $container->get('config');

        $config = [
            'access_token' => [
                'token'  => $configApp['twitter']['yournews365']['token'],
                'secret' => $configApp['twitter']['yournews365']['secret'],
            ],
            'oauth_options' => [
                'consumerKey' => $configApp['twitter']['yournews365']['apiKey'],
                'consumerSecret' => $configApp['twitter']['yournews365']['apiSecret'],
            ],
            'http_client_options' => [
                'adapter' => 'Zend\Http\Client\Adapter\Curl',
                'curloptions' => [
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false,
                ],
            ],
        ];

        return new Twitter($config);
    }
}
