<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * Authenticate against LinkedIn telling it where to call back to.
     *
     * @Route("/authenticate", name="authenticate")
     */
    public function authenticateAction()
    {
        $importer = $this->get('ccc_linkedin_importer.importer');
        $profileToRetrieve = 'https://www.linkedin.com/in/janmeshit/';
        $callbackUrl = $this->generateUrl('callback', ['url' => $profileToRetrieve], UrlGeneratorInterface::ABSOLUTE_URL);
        $importer->setRedirect($callbackUrl);
        return $importer->requestPermission('basic');
    }

    /**
     * Receive callback from LinkedIn and display the results.
     *
     * @Route("/callback", name="callback")
     */
    public function callbackAction(Request $request)
    {
        $importer = $this->get('ccc_linkedin_importer.importer');

        $authenticationCode = $request->get('code');
        $importer->setCode($authenticationCode);

        $profileToRetrieve = $request->get('url');
        $importer->setPublicProfileUrl($profileToRetrieve);

        $accessToken = $importer->requestAccessToken();
        $profileData = $importer->requestUserData('public', $accessToken);

        dump($profileData);
        exit;
    }
}
