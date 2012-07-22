<?php

namespace GitList\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BranchesController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $route = $app['controllers_factory'];

        $route->get('{repo}/branches/{branch}', $branchesController = function($repo, $branch = '') use ($app) {
            $repository = $app['git']->getRepository($app['git.repos'] . $repo);
            $head = $repository->getHead();
            if (!$branch) {
                $branch = $head;
            }

            $path = $repo;
            if ($branch != $head) {
              $path . "/{$branch}";
            }

            $breadcrumbs = $app['util.view']->getBreadcrumbs($path);

            return $app['twig']->render('branches.twig', array(
                'page'           => 'branches',
                'repo'           => $repo,
                'branch'         => $branch,
                'branchesDetail' => $repository->getBranchesDetail($branch),
                'branches'       => $repository->getBranches(),
                'tags'           => $repository->getTags(),
            ));
        })->assert('repo', '[\w-._]+')
          ->assert('branch', '[\w-._]+')
          ->bind('branch');

        $route->get('{repo}/compare/{source}...{target}', $branchesController = function($repo, $source, $target) use ($app) {
            $repository = $app['git']->getRepository($app['git.repos'] . $repo);
            $branchDiff = $repository->getBranchDiff($source, $target);
            $breadcrumbs = $app['util.view']->getBreadcrumbs("$repo/");

            return $app['twig']->render('branch_diff.twig', array(
                'page'           => 'branches',
                'branch'         => $source,
                'repo'           => $repo,
                'branchdiff'     => $branchDiff,
                'branches'       => $repository->getBranches(),
                'tags'           => $repository->getTags(),
            ));
        })->assert('repo', '[\w-._]+')
            ->assert('source', '[\w-_]+')
            ->assert('target', '[\w-_]+');

        return $route;
    }
}
