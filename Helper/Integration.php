<?php
namespace Emartech\Emarsys\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Integration\Model\AuthorizationService;
use Magento\Integration\Model\IntegrationService;
use Magento\Integration\Model\Oauth\Token;
use Magento\Integration\Model\Oauth\Token\Provider;
use Magento\Integration\Model\OauthService;

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
   */
  public function __construct(
    Context $context,
    IntegrationService $integrationService,
    OauthService $oauthService,
    AuthorizationService $authorizationService,
    Token $token,
    Provider $tokenProvider
  ) {
    parent::__construct($context);
    $this->integrationService = $integrationService;
    $this->token = $token;
    $this->authorizationService = $authorizationService;
    $this->oauthService = $oauthService;
    $this->tokenProvider = $tokenProvider;
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
   * @return array
   * @throws \Magento\Framework\Oauth\Exception
   */
  public function getToken()
  {
    $existingIntegration = $this->getExistingIntegration();
    if ($existingIntegration->getId() === null) {
      $this->create();
    } else {
      $this->token = $this->tokenProvider->getIntegrationTokenByConsumerId($existingIntegration->getConsumerId());
    }

    return ['token' => $this->token->getToken(), 'secret' => $this->token->getSecret()];
  }

  /**
   * @return void
   */
  public function delete()
  {
    $existingIntegrationId = $this->getExistingIntegration()->getId();
    if ($existingIntegrationId !== null) {
      $this->integrationService->delete($existingIntegrationId);
    }
  }

  private function getExistingIntegration()
  {
    return $this->integrationService->findByName($this->integrationData['name']);
  }
}