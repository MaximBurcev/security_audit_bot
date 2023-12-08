@servers(['web' => '82.146.58.115'])

@task('deploy')
    cd /var/www/bot
    git pull origin master
@endtask
