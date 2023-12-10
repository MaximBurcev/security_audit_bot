@servers(['web' => 'security_audit_bot@82.146.58.115'])

@setup
    $projectRoot = '/var/www/security_audit_bot/www';
@endsetup

@story('deploy')
    update-code
    update-dependencies
    run-migrations
    key-generate
    run-tests
@endstory

@task('update-code')
    cd {{ $projectRoot }}
    git pull origin master
@endtask

@task('update-dependencies')
    cd {{ $projectRoot }}
    php /usr/local/bin/composer update
@endtask

@task('run-migrations')
    cd {{ $projectRoot }}
    php artisan migrate
@endtask

@task('key-generate')
    cd {{ $projectRoot }}
    php artisan key:generate
@endtask

@task('npm')
    curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | bash
    cat .bashrc
    source .bashrc
    nmv
@endtask

@task('run-tests')
    cd {{ $projectRoot }}
    php artisan test
@endtask

