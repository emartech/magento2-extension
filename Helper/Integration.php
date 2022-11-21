<?php

namespace Emartech\Emarsys\Helper;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Oauth\Exception;
use Magento\Integration\Model\AuthorizationService;
use Magento\Integration\Model\Integration as IntegrationModel;
use Magento\Integration\Model\IntegrationService;
use Magento\Integration\Model\Oauth\Token;
use Magento\Integration\Model\Oauth\Token\Provider;
use Magento\Setup\Exception as SetupException;

class Integration extends AbstractHelper
{
    /**
     * @var IntegrationService
     */
    private $integrationService;

    /**
     * @var AuthorizationService
     */
    private $authorizationService;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var Token
     */
    private $token;

    /**
     * @var Provider
     */
    private $tokenProvider;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var array
     */
    private $integrationData = [
        'name'       => 'Emarsys Integration',
        'email'      => 'emarsys@emarsys.com',
        'status'     => '1',
        'endpoint'   => 'https://localhost',
        'setup_type' => '0'
    ];

    /**
     * @var Context
     */
    private $context;

    public const MAGENTO_VERSION = 2;

    /**
     * Integration constructor.
     *
     * @param Context              $context
     * @param IntegrationService   $integrationService
     * @param AuthorizationService $authorizationService
     * @param Json                 $json
     * @param Token                $token
     * @param Provider             $tokenProvider
     * @param WriterInterface      $configWriter
     */
    public function __construct(
        Context $context,
        IntegrationService $integrationService,
        AuthorizationService $authorizationService,
        Json $json,
        Token $token,
        Provider $tokenProvider,
        WriterInterface $configWriter
    ) {
        parent::__construct($context);
        $this->context = $context;
        $this->integrationService = $integrationService;
        $this->token = $token;
        $this->authorizationService = $authorizationService;
        $this->tokenProvider = $tokenProvider;
        $this->configWriter = $configWriter;
        $this->json = $json;
    }

    /**
     * Create
     *
     * @return void
     */
    public function create(): void
    {
        if ($this->getExistingIntegration()->getId() === null) {
            try {
                $integration = $this->integrationService->create($this->integrationData);
                $this->authorizationService->grantAllPermissions($integration->getId());
                $this->token->createVerifierToken($integration->getConsumerId());
            } catch (\Exception $e) {
                $this->_logger->error($e);
            }
        }
    }

    /**
     * GenerateConnectToken
     *
     * @return string
     * @throws SetupException
     * @throws Exception
     */
    public function generateConnectToken(): string
    {
        $token = $this->getToken()['token'];
        $base_url = $this->getBaseUrl();
        $magento_version = self::MAGENTO_VERSION;

        $connectJson = $this->json->serialize(compact('token', 'magento_version', 'base_url'));

        return base64_encode($connectJson);
    }

    /**
     * Delete
     *
     * @return void
     * @throws IntegrationException
     */
    public function delete(): void
    {
        $this->configWriter->delete('emartech/emarsys/connecttoken');
        $existingIntegrationId = $this->getExistingIntegration()->getId();
        if ($existingIntegrationId !== null) {
            $this->integrationService->delete($existingIntegrationId);
        }
    }

    /**
     * GetExistingIntegration
     *
     * @return IntegrationModel
     */
    private function getExistingIntegration(): IntegrationModel
    {
        return $this->integrationService->findByName($this->integrationData['name']);
    }

    /**
     * GetBaseUrl
     *
     * @return string
     * @throws SetupException
     */
    private function getBaseUrl(): string
    {
        $baseUrl = $this->context->getScopeConfig()->getValue('web/unsecure/base_url');

        if (!$baseUrl) {
            throw new SetupException('Missing base_url setting. Set web/unsecure/base_url.');
        }

        return $baseUrl;
    }

    /**
     * GetToken
     *
     * @return array
     * @throws Exception
     */
    private function getToken(): array
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
