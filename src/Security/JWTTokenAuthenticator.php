<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator as BaseAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor;

class JWTTokenAuthenticator extends BaseAuthenticator
{
    /**
     * @return TokenExtractor\TokenExtractorInterface
     */
    protected function getTokenExtractor()
    {
        // Return a custom extractor, no matter of what are configured
        return new TokenExtractor\AuthorizationHeaderTokenExtractor('Token', 'Authorization');

        // Or retrieve the chain token extractor for mapping/unmapping extractors for this authenticator
        $chainExtractor = parent::getTokenExtractor();
        
        // Clear the token extractor map from all configured extractors
        $chainExtractor->clearMap();
        
        // Or only remove a specific extractor
        $chainTokenExtractor->removeExtractor(function (TokenExtractor\TokenExtractorInterface $extractor) {
            return $extractor instanceof TokenExtractor\CookieTokenExtractor;
        });
        
        // Add a new query parameter extractor to the configured ones
        $chainExtractor->addExtractor(new TokenExtractor\QueryParameterTokenExtractor('jwt'));
        
        // Return the chain token extractor with the new map
        return $chainTokenExtractor;
    }
}