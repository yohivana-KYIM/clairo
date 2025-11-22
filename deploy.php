<?php
namespace Deployer;

require 'recipe/symfony.php';

// Project name
set('application', 'cleo');

// Config

set('repository', 'git@gitlab.com:f1031/srv-cleo.git');

// [Optional] Allocate tty for git clone. Default value is false.
// set('git_tty', true);

// Shared files/dirs between deploys
set('shared_files', ['.env.local']);
set('shared_dirs', ['public/uploads','public/uploads']);

// Writable dirs by web server
// set('writable_mode', 'chmod');
set('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts

host('10.226.32.110')
    ->user('fluxel')
    ->set('deploy_path', '/var/www/cleo');

// Tasks
desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'sf:vendors',
    // 'sf:createDatabase',
    'sf:bddAppli',
    'sf:migrate',
    // 'sf:schema',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'sf:clear_cache',
    'cleanup',
    'success'
]);

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

task('sf:vendors', function () {
    run(('cd {{release_path}} && composer install'));
    });

        task('sf:bddAppli', function () {
            run(('cd {{release_path}} && source .env.local'));
        });
        // task('sf:schema', function () {
        //     run(('php {{release_path}}/bin/console doctrine:schema:update '));
        // });

        task('sf:clear_cache', function () {
        run(('php {{release_path}}/bin/console cache:clear --env=prod'));
        });
    // task('sf:createDatabase', function () {
    //     run(('php {{release_path}}/bin/console d:d:c'));
    //     });
    task('sf:migrate', function () {
    run(('php {{release_path}}/bin/console doctrine:migrations:migrate --env=prod'));
    });

