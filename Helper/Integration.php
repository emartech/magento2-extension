<?php

namespace Emartech\Emarsys\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Integration\Model\AuthorizationService;
use Magento\Integration\Model\IntegrationService;
use Magento\Integration\Model\Oauth\Token;
use Magento\Integration\Model\Oauth\Token\Provider;
use Magento\Setup\Exception;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Zend\Uri\Http;

class Integration extends AbstractHelper
{
    /** @var IntegrationService */
    private $integrationService;
    /** @var AuthorizationService */
    private $authorizationService;
    /**  @var LoggerInterface */
    private $logger;
    /** @var Json */
    private $json;
    /** @var Token */
    private $token;
    /** @var Provider */
    private $tokenProvider;
    /** @var WriterInterface */
    // @codingStandardsIgnoreLine
    protected $configWriter;
    /** @var ScopeConfigInterface */
    // @codingStandardsIgnoreLine
    protected $scopeConfig;

    private $integrationData = [
        'name'       => 'Emarsys Integration',
        'email'      => 'emarsys@emarsys.com',
        'status'     => '1',
        'endpoint'   => 'http://localhost:9300',
        'setup_type' => '0'
    ];
    /**
     * @var \Zend\Uri\Http
     */
    private $http;

    /**
     * Integration constructor.
     * @param Context $context
     * @param IntegrationService $integrationService
     * @param AuthorizationService $authorizationService
     * @param LoggerInterface $logger
     * @param Http $http
     * @param Json $json
     * @param Token $token
     * @param Provider $tokenProvider
     * @param WriterInterface $configWriter
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        IntegrationService $integrationService,
        AuthorizationService $authorizationService,
        LoggerInterface $logger,
        Http $http,
        Json $json,
        Token $token,
        Provider $tokenProvider,
        WriterInterface $configWriter,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->integrationService = $integrationService;
        $this->token = $token;
        $this->authorizationService = $authorizationService;
        $this->tokenProvider = $tokenProvider;
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->json = $json;
        $this->http = $http;
    }

    /**
     * @return void
     */
    public function create()
    {
        if ($this->getExistingIntegration()->getId() === null) {
            try {
                $integration = $this->integrationService->create($this->integrationData);
                $this->authorizationService->grantAllPermissions($integration->getId());
                $this->token->createVerifierToken($integration->getConsumerId());
            } catch (\Exception $e) {
                $this->logger->error($e);
            }
        }
    }

    /**
     * @return string
     * @throws Exception
     * @throws \Magento\Framework\Oauth\Exception
     */
    public function saveConnectTokenToConfig()
    {
        $token = $this->getToken()['token'];
        $parsedUrl = $this->getBaseUrl();
        $hostname = $parsedUrl->getHost();
        $port = $parsedUrl->getPort();
        if ($port && $port !== 80) {
            $hostname .= ':' . $port;
        }
        $connectJson = $this->json->serialize(compact('hostname', 'token'));

        $connectToken = base64_encode($connectJson);

        $this->configWriter->save('emartech/emarsys/connecttoken', $connectToken);

        return $connectToken;
    }

    /**
     * @return mixed|string
     * @throws Exception
     * @throws \Magento\Framework\Oauth\Exception
     */
    public function getConnectToken()
    {
        $connectToken = $this->scopeConfig->getValue('emartech/emarsys/connecttoken');

        if (!$connectToken) {
            $this->create();
            $this->saveConnectTokenToConfig();
            $connectToken = $this->saveConnectTokenToConfig();
        }

        return $connectToken;
    }

    /**
     * @return void
     */
    public function delete()
    {
        $this->configWriter->delete('emartech/emarsys/connecttoken');
        $existingIntegrationId = $this->getExistingIntegration()->getId();
        if ($existingIntegrationId !== null) {
            $this->integrationService->delete($existingIntegrationId);
        }
    }

    private function getExistingIntegration()
    {
        return $this->integrationService->findByName($this->integrationData['name']);
    }

    /**
     * @return Http
     * @throws Exception
     */
    private function getBaseUrl()
    {
        $baseUrl = $this->scopeConfig->getValue('web/unsecure/base_url');

        if (!$baseUrl) {
            throw new Exception('Missing base_url setting. Set web/unsecure/base_url.');
        }

        $parsedUrl = $this->http->parse($baseUrl);

        return $parsedUrl;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Oauth\Exception
     */
    private function getToken()
    {
        $existingIntegration = $this->getExistingIntegration();
        if ($existingIntegration->getId() === null) {
            $this->create();
        } else {
            $this->token = $this->tokenProvider->getIntegrationTokenByConsumerId($existingIntegration->getConsumerId());
        }

        return ['token' => $this->token->getToken(), 'secret' => $this->token->getSecret()];
    }
}
