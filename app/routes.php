<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;


/**
 * 
 * Ideally, this should be in a controller or helper file
 * @param $tree
 * @param null $parentId
 * @return array
 * 
 * reference: https://akki.ca/blog/php-convert-adjacency-set-to-nested-json/
 */
function transformTree($tree, $parentId = null)
{
    $output = [];
    foreach ($tree as $node) {

        if ($node['parent_id'] == $parentId) {
            $children = transformTree($tree, $node['id']);

            if ($children) {
                $node['children'] = $children;
            }
            $output[] = $node;
            unset($node);
        }        
    }
    return $output;
}

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
    
    $app->add(new Tuupola\Middleware\JwtAuthentication([
        // @note: this should be moved to a secure location, probably in .env file
        // test token below
        // eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0b2tlbiI6ImV4YW1wbGVfdG9rZW4ifQ.kQ4P_7brMqjO5uOeAJTuCysLbyrSsTzC7T4x0t0BhlE
        "secret" => "example_key"
    ]));

    /** 
     *  Ideally:
     *  route logic can be moved to a an action or controller
     *  
     *
     */
    
    $app->get('/facet', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);
        // @note: ideally my preference is to use MTPP (modified pre-order tree traversal) over adjacency sets
        $sth = $db->prepare("SELECT id, parent_id, name FROM facets ORDER BY parent_id");

        try {
            $sth->execute();
            $data = $sth->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (\Exception $e) {
            // a pdo exception was thrown
            $payload = json_encode(['message' => 'An internal error occured. Please try again.']);
            $response->getBody()->write($payload);

            // log error 
            $this->logger->info(sprintf('DB exception thrown in file[%s] at line[%u] with exception message [%s]'), 
                __FILE__, __LINE__, $e->getMessage());

            return $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
        }
        
        if (!empty($data)) {     
            // facets found
            $result = transformTree($data, 0);    
            $payload = json_encode(['collection' => $result]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        else {
            // no item found
            $payload = json_encode(['message' => 'No items found.']);
            $response->getBody()->write($payload);

            return $response
                ->withStatus(204)
                ->withHeader('Content-Type', 'application/json');
        }        
    });
};
	