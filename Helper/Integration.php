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
use Magento\Integration\Model\OauthService;
use Magento\Setup\Exception;

class Integration extends AbstractHelper
{
  /** @var IntegrationService */
  private $integrationService;
  /** @var AuthorizationService */
  private $authorizationService;
  /** @var Token */
  private $token;
  /** @var OauthService */
  private $oauthService;
  /** @var Provider */
  private $tokenProvider;
  /** @var WriterInterface  */
  protected $configWriter;
  /** @var ScopeConfigInterface  */
  protected $scopeConfig;

  private $integrationData = [
    'name' => 'Emarsys Integration',
    'email' => 'emarsys@emarsys.com',
    'status' => '1',
    'endpoint' => 'http://localhost:9300',
    'setup_type' => '0'
  ];

  /**
   * @param Context $context
   * @param IntegrationService $integrationService
   * @param OauthService $oauthService
   * @param AuthorizationService $authorizationService
   * @param Token $token
   * @param Provider $tokenProvider
   * @param WriterInterface $configWriter
   * @param ScopeConfigInterface $scopeConfig
   */
  public function __construct(
    Context $context,
    IntegrationService $integrationService,
    OauthService $oauthService,
    AuthorizationService $authorizationService,
    Token $token,
    Provider $tokenProvider,
    WriterInterface $configWriter,
    ScopeConfigInterface $scopeConfig
  ) {
    parent::__construct($context);
    $this->integrationService = $integrationService;
    $this->token = $token;
    $this->authorizationService = $authorizationService;
    $this->oauthService = $oauthService;
    $this->tokenProvider = $tokenProvider;
    $this->configWriter = $configWriter;
    $this->scopeConfig = $scopeConfig;
  }

  /**
   * @return void
   */
  public function create()
  {
    if ($this->getExistingIntegration()->getId() === null) {
      try{
        $integration = $this->integrationService->create($this->integrationData);
        $this->authorizationService->grantAllPermissions($integration->getId());
        $this->token->createVerifierToken($integration->getConsumerId());
      } catch(\Exception $e) {
        echo 'Error : '.$e->getMessage();
      }
    }
  }

  /**
   * @throws \Magento\Framework\Oauth\Exception
   */
  public function saveConnectTokenToConfig() {
    $token = $this->getToken()['token'];
    $parsedUrl = parse_url($this->getBaseUrl());
    $hostname = $parsedUrl['host'];
    if ($parsedUrl['port']) {
      $hostname .= ':' . $parsedUrl['port'];
    }
    $connectJson = json_encode(['hostname' => $hostname, 'token' => $token]);

    $connectToken = base64_encode($connectJson);

    $this->configWriter->save('emartech/emarsys/connecttoken', $connectToken);

    return $connectToken;
  }

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
   * @return mixed
   */
  private function getBaseUrl() {
    $baseUrl = $this->scopeConfig->getValue('web/unsecure/base_url');

    return $baseUrl;
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
