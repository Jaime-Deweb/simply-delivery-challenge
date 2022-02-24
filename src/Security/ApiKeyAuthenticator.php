<?php

namespace App\Security;

use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    private $apiKey;
    
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }
    
    public function supports(Request $request): ?bool
    {
        return true;
    }
    
    public function authenticate(Request $request): Passport
    {        
        $key = $request->query->get('key');          
        
        if ($key === null) {
            throw new CustomUserMessageAuthenticationException('No API key provided.');
        }        
               
        return new Passport(new UserBadge('api'), new CustomCredentials(
            function($credentials) {
                return $credentials === $this->apiKey;
            },            
            $key
        ));
    }
    
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }
    
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
          'errors' => strtr($exception->getMessageKey(), $exception->getMessageData())  
        ];
        
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
