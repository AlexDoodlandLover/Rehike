<?php
namespace Rehike\Controller;

use \Rehike\Controller\core\HitchhikerController;

use Rehike\ControllerV2\{
    IGetController,
    IPostController,
};

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use Rehike\Network;
use Rehike\SignInV2\SignIn;

/**
 * Controller for the /profile endpoint.
 * 
 * This endpoint simply redirects to the user's channel page if they're logged
 * in. Otherwise it redirects to the homepage.
 * 
 * In other words, this only exists for compatibility with the standard YT
 * server.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author The Rehike Maintainers
 */
class ProfileRedirectEndpointController extends HitchhikerController implements IGetController
{
    // Doesn't have a corresponding page as this redirects the user.
    public bool $useTemplate = false;

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        if (!SignIn::isSignedIn())
        {
            header("Location: /");
            exit();
        }

        Network::innertubeRequest(
            action: "navigation/resolve_url",
            body: [
                "url" => "https://www.youtube.com/profile"
            ]
        )->then(function ($response) {
            $ytdata = $response->getJson();

            if ($a = @$ytdata->endpoint->urlEndpoint->url)
            {
                header("Location: " . str_replace("https://www.youtube.com", "", $a));
            }
        });
    }
}