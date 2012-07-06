<?php

$app->get('{repo}/branches/{branch}', function($repo, $branch) use($app) {
    $repository = $app['git']->getRepository($app['git.repos'] . $repo);
    return $app['twig']->render('branches.twig', array(
        'baseurl'        => $app['baseurl'],
        'page'           => 'branches',
        'repo'           => $repo,
        'branch'         => $branch,
        'branchesDetail' => $repository->getBranchesDetail($branch),
        'branches'       => $repository->getBranches(),
        'tags'           => $repository->getTags(),
    ));
})->assert('repo', '[\w-._]+')
  ->assert('branch', '[\w-_]+')
  ->value('branch', 'master');


$app->get('{repo}/branches/{source}...{target}', function($repo, $source, $target) use($app) {
    $repository = $app['git']->getRepository($app['git.repos'] . $repo);
    $branchDiff = $repository->getBranchDiff($source, $target);
    $breadcrumbs = $app['utils']->getBreadcrumbs("$repo/");

    return $app['twig']->render('branch_diff.twig', array(
        'baseurl'        => $app['baseurl'],
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
