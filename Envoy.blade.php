@setup
    $repo = 'git@github.com:naimsolong/personal-financial.git';
    $directory = $directory ?? '/var/www/personal-financial';
    $user = $user ?? 'unknown';
    $server = $server ?? '127.0.0.1';
    $branch = $branch ?? 'main';
    $server_env = $server_env ?? '';
	$php_version = $php_version ?? '8.2';
@endsetup

@servers(['web' => $user.'@'.$server])

@story('deploy')
    check-version
    update-code
    sync-env
    install-dependencies
    compile-assets
    clear-cache
@endstory

@task('check')
    echo "{{ $repo }}"
    echo "{{ $directory }}"
    echo "{{ $user }}"
    echo "{{ $server }}"
    echo "{{ $branch }}"
    echo "{{ $server_env }}"
    echo "{{ $php_version }}"
@endtask

@task('check-version')
    echo "[1] Checking version"
    /usr/bin/php{{ $php_version }} -v
    /usr/bin/php{{ $php_version }} /usr/local/bin/composer --version
    node -v
    npm -v
    echo "[1] Version checked"
@endtask

@task('update-code')
    echo "[2] Updating code"
    [ -d {{ $directory }}/.git ] || git clone {{ $repo }} --branch={{ $branch }} {{ $directory }}
    cd {{ $directory }}
    git fetch --all
    git reset --hard {{ $commit }}
    echo "[2] Repository up to date"
@endtask

@task('sync-env')
    echo "[3] Sync env"
    aws s3 sync {{ $server_env }} {{ $directory }}
    echo "[3] Env have been synced"
@endtask

@task('install-dependencies')
    echo "[4] Installing dependencies"
    cd {{ $directory }}
    /usr/bin/php{{ $php_version }} /usr/local/bin/composer install -o --no-dev --no-interaction
    /usr/bin/php{{ $php_version }} artisan migrate --force
    /usr/bin/php{{ $php_version }} artisan db:seed --force
    npm install
    echo "[4] Dependencies have been installed"
@endtask

@task('compile-assets')
    echo "[5] Building assets"
    cd {{ $directory }}
    npm run build
    echo "[5] Assets have been build"
@endtask

@task('clear-cache')
    echo "[6] Clearing cache"
    cd {{ $directory }}
    /usr/bin/php{{ $php_version }} /usr/local/bin/composer dump-autoload -o
	/usr/bin/php{{ $php_version }} artisan optimize:clear
	/usr/bin/php{{ $php_version }} artisan about
	echo "[6] Cache cleared"
@endtask

{{-- @finished
    @telegram('bot-id','chat-id')
@endfinished --}}
