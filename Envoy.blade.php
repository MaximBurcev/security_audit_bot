@servers(['web' => '82.146.58.115'])

@task('deploy')
    cd /var/www
    git clone git@github.com:MaximBurcev/security_audit_bot.git
@endtask
