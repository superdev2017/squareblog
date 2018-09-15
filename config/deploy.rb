# config valid only for current version of Capistrano
lock "3.7.1"

set :application, "easywebbuilder"
set :repo_url, "git@github.com:Ivan0905/easywebbuilder.git"

# Default branch is :master
# ask :branch, `git rev-parse --abbrev-ref HEAD`.chomp

set :deploy_to, "/var/www/easywebbuilder"

# Default value for :format is :airbrussh.
# set :format, :airbrussh

# You can configure the Airbrussh format using :format_options.
# These are the defaults.
# set :format_options, command_output: true, log_file: "log/capistrano.log", color: :auto, truncate: :auto

# Default value for :pty is false
# set :pty, true

# Default value for :linked_files is []
set :linked_files, fetch(:linked_files, []).push('app/config/parameters.yml').push('web/.htaccess')

# Default value for linked_dirs is []
# set :linked_dirs, fetch(:linked_dirs, []).push('var')

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for keep_releases is 5
# set :keep_releases, 5


after 'deploy:starting', 'composer:install_executable'
after 'deploy:updated', 'symfony:assets:install'
after 'deploy', 'deploy:run_post_setup'
after 'deploy', 'deploy:cleanup'

before 'deploy:cleanup', 'deploy:cleanup_permissions'

namespace :deploy do

  desc "Dump assets and export env"
  task :run_post_setup do
      on roles :all do
        execute "php #{current_path}/bin/console assetic:dump --env=prod --no-debug"

        execute "cd #{current_path} && export SYMFONY_ENV=prod # --allow-root"

        execute "chmod -R 0777 #{current_path}/app/Resources/translations # --allow-root"

        execute "chmod -R 0777 #{current_path}/var/cache # --allow-root"

        execute "chmod -R 0777 #{current_path}/var/sessions # --allow-root"

        execute "cd #{current_path} && bower install # --allow-root"
      end
  end

  desc 'Set permissions on old releases before cleanup'
  task :cleanup_permissions do
    on release_roles :all do |host|
      releases = capture(:ls, '-x', releases_path).split
      if releases.count >= fetch(:keep_releases)
        info "Cleaning permissions on old releases"
        directories = (releases - releases.last(1))
        if directories.any?
          directories.each do |release|
            within releases_path.join(release) do
                execute :sudo, :chown, '-R', 'admin', 'var'
            end
          end
        else
          info t(:no_old_releases, host: host.to_s, keep_releases: fetch(:keep_releases))
        end
      end
    end
  end

end